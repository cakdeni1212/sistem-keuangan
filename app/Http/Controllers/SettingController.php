<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'business_name' => AppSetting::get('business_name', 'FORKA COFFEE & SPACE'),
            'sidebar_tagline' => AppSetting::get('sidebar_tagline', 'Coffee Shop Manager'),
            'slip_subtitle' => AppSetting::get('slip_subtitle', 'Slip Gaji Karyawan'),
            'wa_number' => AppSetting::get('wa_number', '6281234567890'),
            'landing_address' => AppSetting::get('landing_address', ''),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:100',
            'sidebar_tagline' => 'nullable|string|max:100',
            'slip_subtitle' => 'nullable|string|max:100',
            'wa_number' => 'nullable|string|max:20',
            'landing_address' => 'nullable|string|max:500',
        ]);

        foreach ($data as $key => $value) {
            AppSetting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function landing()
    {
        $raw = AppSetting::get('landing_config', '');
        $config = $raw ? json_decode($raw, true) : [];

        // Normalize: if stored as plain array, wrap in ['sections' => [...]]
        if (is_array($config) && isset($config[0]) && !isset($config['sections'])) {
            $config = ['sections' => $config];
        }

        if (empty($config) || empty($config['sections'])) {
            // Default config
            $config = [
                'sections' => [
                    ['key' => 'hero', 'label' => 'Hero Banner', 'icon' => '🏠', 'desc' => 'Banner utama halaman depan', 'visible' => true, 'fields' => [
                        ['key' => 'page_title', 'label' => 'Judul Halaman (tab browser)', 'type' => 'text', 'value' => 'Forka Coffee & Space', 'placeholder' => 'Cth: Forka Coffee & Space'],
                        ['key' => 'badge', 'label' => 'Badge (teks kecil di atas judul)', 'type' => 'text', 'value' => 'Coffee & Space • Forka', 'placeholder' => 'Cth: Coffee & Space • Forka'],
                        ['key' => 'title', 'label' => 'Judul Utama', 'type' => 'text', 'value' => 'Tempat Ngopi', 'placeholder' => 'Baris pertama judul'],
                        ['key' => 'tagline', 'label' => 'Tagline (baris kedua, warna amber)', 'type' => 'text', 'value' => 'Terbaik di Kota', 'placeholder' => 'Baris kedua judul'],
                        ['key' => 'bg_image', 'label' => 'Hero Background', 'type' => 'upload', 'value' => '', 'placeholder' => 'Upload gambar'],
                        ['key' => 'subtitle', 'label' => 'Subjudul', 'type' => 'text', 'value' => 'Nikmati suasana nyaman dengan kopi pilihan, makanan lezat, dan ruang kreatif', 'placeholder' => 'Deskripsi singkat'],
                    ]],
                    ['key' => 'slideshow', 'label' => 'Galeri / Slideshow', 'icon' => '🖼', 'desc' => 'Foto-foto suasana', 'visible' => true, 'fields' => [
                        ['key' => 'label', 'label' => 'Label Section', 'type' => 'text', 'value' => 'Suasana Forka Coffee', 'placeholder' => 'Label galeri'],
                    ]],
                    ['key' => 'about', 'label' => 'Tentang Kami', 'icon' => '📖', 'desc' => 'Informasi brand', 'visible' => true, 'fields' => [
                        ['key' => 'title', 'label' => 'Judul', 'type' => 'text', 'value' => 'Forka Coffee & Space', 'placeholder' => 'Nama brand'],
                        ['key' => 'tagline', 'label' => 'Tagline (warna amber)', 'type' => 'text', 'value' => 'Taste the experience', 'placeholder' => 'Cth: Taste the experience'],
                        ['key' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'value' => 'Forka Coffee & Space adalah tempat nongkrong favorit yang menyajikan kopi berkualitas, makanan lezat, dan suasana nyaman.', 'placeholder' => 'Cerita tentang brand'],
                    ]],
                    ['key' => 'menu', 'label' => 'Menu Slideshow', 'icon' => '📋', 'desc' => 'Foto menu (upload di bagian bawah)', 'visible' => true, 'fields' => [
                        ['key' => 'label', 'label' => 'Label Section', 'type' => 'text', 'value' => 'Menu Kami', 'placeholder' => 'Label menu'],
                    ]],
                    ['key' => 'location', 'label' => 'Lokasi & Sosial Media', 'icon' => '📍', 'desc' => 'Alamat, peta, dan tautan sosial media', 'visible' => true, 'fields' => [
                        ['key' => 'label', 'label' => 'Label Section', 'type' => 'text', 'value' => 'Temukan Kami', 'placeholder' => 'Label lokasi'],
                        ['key' => 'address', 'label' => 'Alamat / Jam Operasional', 'type' => 'textarea', 'value' => 'Buka Senin - Sabtu, 08:00 - 22:00', 'placeholder' => 'Alamat lengkap'],
                        ['key' => 'maps_url', 'label' => 'Google Maps Embed URL', 'type' => 'text', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.2897595107734!2d113.42859267525202!3d-7.759062592260011!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd701004ce4e121%3A0x8541d2cc07f615e2!2sFORKA%20Coffee%20%26%20Space!5e0!3m2!1sid!2sid!4v1781795748233!5m2!1sid!2sid', 'placeholder' => 'https://www.google.com/maps/embed?pb=... atau paste <iframe>'],
                        ['key' => 'instagram', 'label' => 'Instagram URL', 'type' => 'text', 'value' => '', 'placeholder' => 'https://instagram.com/forkacoffee'],
                        ['key' => 'tiktok', 'label' => 'TikTok URL', 'type' => 'text', 'value' => '', 'placeholder' => 'https://tiktok.com/@forkacoffee'],
                        ['key' => 'whatsapp', 'label' => 'WhatsApp URL', 'type' => 'text', 'value' => '', 'placeholder' => 'https://wa.me/6281234567890'],
                    ]],
                ],
            ];
        }

        return view('settings.landing', compact('config'));
    }

    public function landingUpdate(Request $request)
    {
        // Handle menu image deletion
        if ($request->has('delete_menu_image')) {
            $deleteUrl = $request->delete_menu_image;
            $existing = json_decode(AppSetting::get('landing_menu_images', '[]'), true) ?: [];
            $existing = array_values(array_filter($existing, fn($img) => (is_string($img) ? $img : ($img['url'] ?? '')) !== $deleteUrl));
            $filePath = str_replace('/storage/', '', $deleteUrl);
            $fullPath = storage_path('app/public/' . $filePath);
            if (file_exists($fullPath)) unlink($fullPath);
            AppSetting::set('landing_menu_images', json_encode($existing));
            return back()->with('success', 'Foto menu berhasil dihapus.');
        }

        // Handle menu image reorder
        if ($request->has('move_menu_image')) {
            $moveUrl = $request->move_menu_image;
            $direction = $request->direction;
            $existing = json_decode(AppSetting::get('landing_menu_images', '[]'), true) ?: [];
            $index = null;
            foreach ($existing as $i => $img) {
                $url = is_string($img) ? $img : ($img['url'] ?? '');
                if ($url === $moveUrl) { $index = $i; break; }
            }
            if ($index !== null) {
                $newIndex = $direction === 'up' ? max(0, $index - 1) : min(count($existing) - 1, $index + 1);
                if ($index !== $newIndex) {
                    $temp = $existing[$index];
                    $existing[$index] = $existing[$newIndex];
                    $existing[$newIndex] = $temp;
                    AppSetting::set('landing_menu_images', json_encode($existing));
                }
            }
            return back()->with('success', 'Urutan foto menu diperbarui.');
        }

        // Handle menu image edit (title/desc)
        if ($request->has('edit_menu_image')) {
            $editUrl = $request->edit_menu_image;
            $existing = json_decode(AppSetting::get('landing_menu_images', '[]'), true) ?: [];
            foreach ($existing as &$img) {
                $url = is_string($img) ? $img : ($img['url'] ?? '');
                if ($url === $editUrl) {
                    if (is_string($img)) $img = ['url' => $img, 'title' => '', 'desc' => ''];
                    $img['title'] = $request->menu_title ?? '';
                    $img['desc'] = $request->menu_desc ?? '';
                }
            }
            AppSetting::set('landing_menu_images', json_encode($existing));
            return back()->with('success', 'Judul foto menu diperbarui.');
        }

        // Handle about background deletion
        if ($request->has('delete_about_bg')) {
            $bg = AppSetting::get('landing_about_bg', '');
            if ($bg) {
                $filePath = str_replace('/storage/', '', $bg);
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) unlink($fullPath);
            }
            AppSetting::set('landing_about_bg', '');
            // Also clear from config
            $raw = AppSetting::get('landing_config', '{}');
            $config = json_decode($raw, true);
            $sections = &$config['sections'];
            if (!$sections && isset($config[0])) $sections = &$config;
            foreach ($sections ?? [] as &$section) {
                if (($section['key'] ?? '') === 'about') {
                    foreach ($section['fields'] ?? [] as &$field) {
                        if ($field['key'] === 'bg_image') $field['value'] = '';
                    }
                }
            }
            AppSetting::set('landing_config', json_encode($config));
            return back()->with('success', 'Background about dihapus.');
        }

        // Handle logo deletion
        if ($request->has('delete_logo')) {
            $logo = AppSetting::get('landing_logo', '');
            if ($logo) {
                $filePath = str_replace('/storage/', '', $logo);
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) unlink($fullPath);
            }
            AppSetting::set('landing_logo', '');
            return back()->with('success', 'Logo berhasil dihapus.');
        }

        // Handle favicon deletion
        if ($request->has('delete_favicon')) {
            $favicon = AppSetting::get('landing_favicon', '');
            if ($favicon) {
                $filePath = str_replace('/storage/', '', $favicon);
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) unlink($fullPath);
            }
            AppSetting::set('landing_favicon', '');
            return back()->with('success', 'Favicon berhasil dihapus.');
        }

        // Handle hero BG deletion
        if ($request->has('delete_hero_bg')) {
            $bg = AppSetting::get('landing_hero_bg', '');
            if ($bg) {
                $filePath = str_replace('/storage/', '', $bg);
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) unlink($fullPath);
            }
            AppSetting::set('landing_hero_bg', '');
            return back()->with('success', 'Background hero dihapus.');
        }

        // Handle image deletion first (before config save)
        if ($request->has('delete_image')) {
            $deleteUrl = $request->delete_image;
            $existing = json_decode(AppSetting::get('landing_slideshow_images', '[]'), true) ?: [];
            $existing = array_values(array_filter($existing, fn($img) => $img !== $deleteUrl));

            // Also delete physical file
            $filePath = str_replace('/storage/', '', $deleteUrl);
            $fullPath = storage_path('app/public/' . $filePath);
            if (file_exists($fullPath)) unlink($fullPath);

            AppSetting::set('landing_slideshow_images', json_encode($existing));
            return back()->with('success', 'Foto berhasil dihapus.');
        }

        // Save landing config
        if ($request->filled('landing_config') && $request->landing_config !== '{}') {
            $decoded = json_decode($request->landing_config, true);
            // Normalize: wrap in ['sections' => [...]]
            $config = isset($decoded[0]) ? ['sections' => $decoded] : $decoded;

            // Auto-extract Google Maps embed URL from iframe HTML
            foreach ($config['sections'] ?? [] as &$section) {
                if ($section['key'] === 'location') {
                    foreach ($section['fields'] ?? [] as &$field) {
                        if ($field['key'] === 'maps_url' && !empty($field['value'])) {
                            // If full iframe was pasted, extract src
                            if (str_starts_with(trim($field['value']), '<iframe')) {
                                preg_match('/src="([^"]+)"/', $field['value'], $m);
                                if (!empty($m[1])) {
                                    $field['value'] = $m[1];
                                }
                            }
                        }
                    }
                }
            }

            AppSetting::set('landing_config', json_encode($config));
        }

        // Handle menu images upload
        if ($request->hasFile('menu_images')) {
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            $existing = json_decode(AppSetting::get('landing_menu_images', '[]'), true) ?: [];
            foreach ($request->file('menu_images') as $file) {
                if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                    $path = $file->store('landing/menu', 'public');
                    $existing[] = ['url' => '/storage/' . $path, 'title' => '', 'desc' => ''];
                }
            }
            AppSetting::set('landing_menu_images', json_encode($existing));
        }

        // Handle logo upload (convert to PNG)
        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
            if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                $img = match($file->getMimeType()) {
                    'image/png' => imagecreatefrompng($file->getRealPath()),
                    'image/gif' => imagecreatefromgif($file->getRealPath()),
                    'image/webp' => imagecreatefromwebp($file->getRealPath()),
                    default => imagecreatefromjpeg($file->getRealPath()),
                };
                if ($img) {
                    $filename = 'logo_' . uniqid() . '.png';
                    $path = 'landing/logo/' . $filename;
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    imagepng($img, storage_path('app/public/' . $path));
                    imagedestroy($img);
                    AppSetting::set('landing_logo', '/storage/' . $path);
                }
            }
        }

        // Handle favicon upload (convert to PNG)
        if ($request->hasFile('favicon_image')) {
            $file = $request->file('favicon_image');
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
            if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                $img = match($file->getMimeType()) {
                    'image/png' => imagecreatefrompng($file->getRealPath()),
                    'image/gif' => imagecreatefromgif($file->getRealPath()),
                    'image/webp' => imagecreatefromwebp($file->getRealPath()),
                    default => imagecreatefromjpeg($file->getRealPath()),
                };
                if ($img) {
                    $filename = 'favicon_' . uniqid() . '.png';
                    $path = 'landing/favicon/' . $filename;
                    imagealphablending($img, true);
                    imagesavealpha($img, true);
                    imagepng($img, storage_path('app/public/' . $path));
                    imagedestroy($img);
                    AppSetting::set('landing_favicon', '/storage/' . $path);
                }
            }
        }

        // Handle about background image upload
        if ($request->hasFile('about_bg_image')) {
            $file = $request->file('about_bg_image');
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                $path = $file->store('landing/about', 'public');
                $url = '/storage/' . $path;
                AppSetting::set('landing_about_bg', $url);
                // Update config field
                $raw = AppSetting::get('landing_config', '{}');
                $config = json_decode($raw, true);
                $sections = &$config['sections'];
                if (!$sections && isset($config[0])) $sections = &$config;
                foreach ($sections ?? [] as &$section) {
                    if (($section['key'] ?? '') === 'about') {
                        foreach ($section['fields'] ?? [] as &$field) {
                            if ($field['key'] === 'bg_image') $field['value'] = $url;
                        }
                    }
                }
                AppSetting::set('landing_config', json_encode($config));
            }
        }

        // Handle hero background image upload
        if ($request->hasFile('hero_bg_image')) {
            $file = $request->file('hero_bg_image');
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                $path = $file->store('landing/hero', 'public');
                $url = '/storage/' . $path;
                AppSetting::set('landing_hero_bg', $url);
            }
        }

        // Handle slideshow image upload
        if ($request->hasFile('slideshow_images')) {
            $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];
            $existing = json_decode(AppSetting::get('landing_slideshow_images', '[]'), true) ?: [];
            $uploaded = 0;
            $skipped = 0;
            foreach ($request->file('slideshow_images') as $file) {
                if ($file->isValid() && in_array($file->getMimeType(), $validMimes)) {
                    $path = $file->store('landing/slideshow', 'public');
                    $existing[] = '/storage/' . $path;
                    $uploaded++;
                } else {
                    $skipped++;
                }
            }
            AppSetting::set('landing_slideshow_images', json_encode($existing));
        }

        return back()->with('success', 'Pengaturan landing page berhasil disimpan.');
    }
}
