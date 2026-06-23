<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use App\Models\HppProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    private string $botToken;

    private string $geminiKey;

    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->geminiKey = config('services.gemini.api_key');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    public function webhook(Request $request): Response
    {
        $update = $request->all();
        Log::info('Telegram update', $update);

        // Return 200 OK immediately so Telegram doesn't retry
        $response = response('ok');

        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (! $message) {
            return $response;
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? null;
        $photo = $message['photo'] ?? null;
        $caption = $message['caption'] ?? '';

        // Flush response to Telegram, then process in background
        $this->flushResponse();

        if ($text === '/start') {
            $this->sendMessage($chatId,
                "👋 Halo! Saya bot *Forka Coffee & Space*.\n\n".
                "📸 Kirim *foto nota penjualan* dan saya akan otomatis memasukkan data ke sistem.\n\n".
                "Tambahkan keterangan pada foto:\n".
                "• Shift: `pagi` atau `sore`\n".
                "• Tanggal opsional, default hari ini\n\n".
                'Contoh caption: `pagi 2026-05-05`'
            );

            return $response;
        }

        if ($text === '/produk') {
            $products = HppProduct::active()->orderBy('name')->get();
            $list = $products->map(fn ($p) => "• {$p->name} — Rp ".number_format($p->harga_jual, 0, ',', '.'))->implode("\n");
            $this->sendMessage($chatId, "📦 *Daftar Produk Aktif:*\n\n{$list}");

            return $response;
        }

        if ($photo) {
            $this->sendMessage($chatId, '⏳ Sedang memproses foto nota...');

            $fileId = end($photo)['file_id'];
            $imageBase64 = $this->downloadPhotoAsBase64($fileId);

            if (! $imageBase64) {
                $this->sendMessage($chatId, '❌ Gagal mengunduh foto. Coba lagi.');

                return $response;
            }

            $shift = 'pagi';
            $date = today()->toDateString();

            if (str_contains(strtolower($caption), 'sore')) {
                $shift = 'sore';
            }
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', $caption, $m)) {
                $date = $m[1];
            }

            $items = $this->parseReceiptWithGemini($imageBase64, $caption);

            if ($items === null) {
                $this->sendMessage($chatId,
                    "⚠️ *AI tidak dapat diakses saat ini.*\n\n".
                    "Kemungkinan penyebab:\n".
                    "• Kuota harian Gemini AI habis\n".
                    "• Coba lagi besok atau hubungi admin untuk upgrade API key\n\n".
                    '_Tip: Input manual tetap bisa dilakukan di web sistem._'
                );

                return $response;
            }

            if (empty($items)) {
                $this->sendMessage($chatId,
                    "❌ Tidak bisa membaca data dari foto.\n\n".
                    "Tips:\n• Pastikan foto jelas dan tidak buram\n• Tambahkan keterangan shift di caption"
                );

                return $response;
            }

            $saved = $this->saveDailySales($date, $shift, $items);

            $shiftLabel = $shift === 'pagi' ? '☀️ Pagi' : '🌆 Sore';
            $dateLabel = Carbon::parse($date)->translatedFormat('l, d F Y');
            $totalOmset = collect($saved)->sum('subtotal');
            $totalQty = collect($saved)->sum('qty');

            $detail = collect($saved)
                ->map(fn ($i) => "• {$i['name']} × {$i['qty']} = Rp ".number_format($i['subtotal'], 0, ',', '.'))
                ->implode("\n");

            $this->sendMessage($chatId,
                "✅ *Data berhasil disimpan!*\n\n".
                "📅 {$dateLabel} — {$shiftLabel}\n".
                "📦 {$totalQty} item terjual\n".
                '💰 Omset: *Rp '.number_format($totalOmset, 0, ',', '.')."*\n\n".
                "{$detail}\n\n".
                '🔗 Cek di: '.config('app.url').'/penjualan-harian'
            );

            return $response;
        }

        if ($text) {
            $this->sendMessage($chatId,
                "ℹ️ Kirim *foto nota* untuk input data penjualan.\n".
                'Ketik /start untuk panduan atau /produk untuk daftar produk.'
            );
        }

        return $response;
    }

    /**
     * Flush HTTP response to client immediately so Telegram gets 200 OK
     * before we make any outbound API calls (avoids 30s timeout).
     */
    private function flushResponse(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            ob_start();
            echo 'ok';
            ob_end_flush();
            fastcgi_finish_request();
        }

        set_time_limit(0);
        ignore_user_abort(true);
    }

    private function parseReceiptWithGemini(string $imageBase64, string $caption): ?array
    {
        $products = HppProduct::active()->get(['id', 'name', 'harga_jual']);
        $productList = $products->map(fn ($p) => "{$p->name} (Rp ".number_format($p->harga_jual, 0, ',', '.').')')->implode(', ');

        $prompt = "Kamu adalah sistem kasir café. Analisa gambar nota/struk penjualan ini.\n\n".
            "Daftar produk yang tersedia di sistem:\n{$productList}\n\n".
            "Tugas:\n".
            "1. Baca setiap item di nota beserta jumlah (qty)\n".
            "2. Cocokkan nama item di nota dengan produk yang tersedia (fuzzy match)\n".
            "3. Kembalikan HANYA JSON array, tanpa teks lain\n\n".
            "Format response (JSON saja):\n".
            '[{"product_id": 3, "name": "Americano", "qty": 2}, {"product_id": 16, "name": "Dimsum", "qty": 1}]'."\n\n".
            'Jika tidak ada item yang cocok atau gambar bukan nota, kembalikan: []';

        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$this->geminiKey}",
            [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => ['mime_type' => 'image/jpeg', 'data' => $imageBase64]],
                    ],
                ]],
                'generationConfig' => ['temperature' => 0.1],
            ]
        );

        if ($response->failed()) {
            $status = $response->status();
            Log::error('Gemini API error', ['status' => $status, 'body' => $response->body()]);

            // Return null to indicate API error (distinct from empty item list)
            return null;
        }

        $text = $response->json('candidates.0.content.parts.0.text', '');
        $text = trim(preg_replace('/```json|```/', '', $text));

        $items = json_decode($text, true);
        if (! is_array($items)) {
            Log::warning('Gemini unparseable response', ['text' => $text]);

            return null;
        }

        return $items;
    }

    private function saveDailySales(string $date, string $shift, array $items): array
    {
        DailySale::where('sale_date', $date)->where('shift', $shift)->delete();

        $saved = [];

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $qty = (int) ($item['qty'] ?? 0);

            if (! $productId || $qty <= 0) {
                continue;
            }

            $product = HppProduct::find($productId);
            if (! $product) {
                continue;
            }

            $hpp = (float) ($product->bahan_baku + $product->tenaga_kerja + $product->overhead);
            $price = (float) $product->harga_jual;
            $subtotal = $price * $qty;
            $hppTotal = $hpp * $qty;

            DailySale::create([
                'sale_date' => $date,
                'shift' => $shift,
                'hpp_product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $price,
                'hpp_per_unit' => $hpp,
                'quantity_sold' => $qty,
                'subtotal' => $subtotal,
                'hpp_total' => $hppTotal,
                'profit' => $subtotal - $hppTotal,
                'created_by' => 1,
            ]);

            $saved[] = ['name' => $product->name, 'qty' => $qty, 'subtotal' => $subtotal];
        }

        return $saved;
    }

    private function downloadPhotoAsBase64(string $fileId): ?string
    {
        $fileResponse = Http::get("{$this->apiUrl}/getFile", ['file_id' => $fileId]);
        if ($fileResponse->failed()) {
            return null;
        }

        $filePath = $fileResponse->json('result.file_path');
        $fileUrl = "https://api.telegram.org/file/bot{$this->botToken}/{$filePath}";

        $imageResponse = Http::timeout(30)->get($fileUrl);
        if ($imageResponse->failed()) {
            return null;
        }

        return base64_encode($imageResponse->body());
    }

    private function sendMessage(int|string $chatId, string $text): void
    {
        try {
            Http::timeout(10)->post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Throwable $e) {
            Log::warning('sendMessage failed', ['chat_id' => $chatId, 'error' => $e->getMessage()]);
        }
    }
}
