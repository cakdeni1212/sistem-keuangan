@php
    $rawConfig = \App\Models\AppSetting::get('landing_config', '');
    $landingConfig = $rawConfig ? json_decode($rawConfig, true) : [];
    // Support both: array langsung atau {sections: [...]}
    $sections = collect($landingConfig['sections'] ?? $landingConfig);
    $heroSection = $sections->firstWhere('key', 'hero');
    $aboutSection = $sections->firstWhere('key', 'about');
    $menuSection = $sections->firstWhere('key', 'menu');
    $locationSection = $sections->firstWhere('key', 'location');
    $slideshowSection = $sections->firstWhere('key', 'slideshow');
    $heroVisible = $heroSection['visible'] ?? true;
    $slideshowVisible = $slideshowSection['visible'] ?? true;
    $aboutVisible = $aboutSection['visible'] ?? true;
    $menuVisible = $menuSection['visible'] ?? true;
    $locationVisible = $locationSection['visible'] ?? true;
    $getField = fn($section, $key, $default) => collect($section['fields'] ?? [])->firstWhere('key', $key)['value'] ?? $default;
@endphp
@php
    $logoUrl = \App\Models\AppSetting::get('landing_logo', '');
    $faviconUrl = \App\Models\AppSetting::get('landing_favicon', '');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $getField($heroSection, 'page_title', \App\Models\AppSetting::get('business_name', 'Forka Coffee & Space')) }}</title>
    @if($faviconUrl)
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background: #fafaf9; color: #1c1917; }

        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #1c1917 0%, #292524 50%, #1c1917 100%);
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -30%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(217,119,6,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -20%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(217,119,6,0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .nav-link { color: #a8a29e; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: #f59e0b; }

        .slide-track {
            display: flex;
            gap: 1rem;
            animation: scroll 30s linear infinite;
            width: max-content;
        }
        .slide-track:hover { animation-play-state: paused; }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .slide-item {
            min-width: 280px;
            height: 200px;
            border-radius: 1rem;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }
        .menu-card {
            background: white;
            border: 1px solid #e7e5e4;
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: all 0.2s;
        }
        .menu-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(0,0,0,0.08);
        }

        .whatsapp-float {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 50;
            width: 3.5rem; height: 3.5rem;
            background: #25D366;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 15px rgba(37,211,102,0.3);
            transition: transform 0.2s;
        }
        .whatsapp-float:hover { transform: scale(1.1); }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav style="position:fixed;top:0;left:0;right:0;z-index:40;background:rgba(28,25,23,0.9);backdrop-filter:blur(12px);border-bottom:1px solid rgba(255,255,255,0.06);">
        <div style="max-width:1200px;margin:0 auto;padding:0 1.5rem;height:3.5rem;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                @if($logoUrl)
                <img src="{{ $logoUrl }}" style="height:2rem;width:auto;border-radius:0.375rem;">
                @else
                <div style="width:2rem;height:2rem;border-radius:0.5rem;background:#d97706;display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:bold;">F</div>
                @endif
                <span style="color:white;font-weight:700;font-size:0.9rem;">{{ \App\Models\AppSetting::get('business_name', 'FORKA COFFEE') }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:1.5rem;">
                <a href="#menu" class="nav-link">Menu</a>
                <a href="#about" class="nav-link">Tentang</a>
                <a href="#location" class="nav-link">Lokasi</a>
                @auth
                    <a href="{{ route('dashboard') }}" style="padding:0.4rem 1rem;background:#d97706;color:white;border-radius:0.5rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" style="padding:0.4rem 1rem;background:#d97706;color:white;border-radius:0.5rem;text-decoration:none;font-size:0.8rem;font-weight:600;">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    @if($heroVisible)
    @php $heroBg = \App\Models\AppSetting::get('landing_hero_bg', ''); @endphp
    {{-- Hero --}}
    <section style="display:flex;align-items:center;justify-content:center;padding:6rem 1.5rem 4rem;position:relative;overflow:hidden;min-height:100vh;{{ $heroBg ? 'background:linear-gradient(rgba(0,0,0,0.65),rgba(0,0,0,0.65)),url('.$heroBg.') center/cover no-repeat;' : 'background:linear-gradient(135deg,#1c1917 0%,#292524 50%,#1c1917 100%);' }}">
        @if(!$heroBg)
        <div class="hero-overlay" style="position:absolute;top:-50%;right:-30%;width:600px;height:600px;background:radial-gradient(circle,rgba(217,119,6,0.08) 0%,transparent 70%);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30%;left:-20%;width:400px;height:400px;background:radial-gradient(circle,rgba(217,119,6,0.05) 0%,transparent 70%);border-radius:50%;"></div>
        @endif
        <div style="position:relative;z-index:1;text-align:center;max-width:700px;">
            @php
                $heroTitle = $getField($heroSection, 'title', 'Tempat Ngopi');
                $heroTagline = $getField($heroSection, 'tagline', 'Terbaik di Kota');
                $heroBadge = $getField($heroSection, 'badge', 'Coffee & Space · Forka');
            @endphp
            <div style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.4rem 1rem;border-radius:999px;background:rgba(217,119,6,0.15);color:#fbbf24;font-size:0.75rem;font-weight:600;margin-bottom:1.5rem;">
                <span style="width:0.4rem;height:0.4rem;border-radius:50%;background:#f59e0b;"></span>
                {{ $heroBadge ?: 'Coffee & Space • Forka' }}
            </div>
            <h1 style="font-size:clamp(2rem, 5vw, 3.5rem);font-weight:800;color:white;line-height:1.15;letter-spacing:-0.02em;">
                {{ $heroTitle }}<br>
                <span style="color:#f59e0b;">{{ $heroTagline }}</span>
            </h1>
            <p style="color:#a8a29e;font-size:1.05rem;margin-top:1rem;line-height:1.6;max-width:500px;margin-left:auto;margin-right:auto;">
                {{ $getField($heroSection, 'subtitle', 'Nikmati suasana nyaman dengan kopi pilihan dan makanan lezat.') }}
            </p>
            <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:2rem;">
                <a href="#menu" style="padding:0.7rem 1.5rem;background:#d97706;color:white;border-radius:0.75rem;text-decoration:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(217,119,6,0.3);">Lihat Menu</a>
                <a href="#location" style="padding:0.7rem 1.5rem;border:1px solid #44403c;color:#d6d3d1;border-radius:0.75rem;text-decoration:none;font-weight:500;font-size:0.9rem;transition:all 0.2s;">Kunjungi Kami</a>
            </div>
        </div>
    </section>
    @endif

    @if($slideshowVisible)
    {{-- Slideshow --}}
    <section style="padding:3rem 0;background:#1c1917;overflow:hidden;border-top:1px solid rgba(255,255,255,0.05);border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="max-width:1200px;margin:0 auto;padding:0 1.5rem;margin-bottom:1.5rem;">
            <p style="color:#a8a29e;font-size:0.8rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;text-align:center;">{{ $getField($slideshowSection, 'label', 'Suasana Forka Coffee') }}</p>
        </div>
        <div style="display:flex;overflow:hidden;">
            <div class="slide-track">
                @php
                    $uploadedImages = json_decode(\App\Models\AppSetting::get('landing_slideshow_images', '[]'), true) ?: [];
                    $images = !empty($uploadedImages) ? $uploadedImages : [
                        'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=400&h=300&fit=crop',
                        'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=300&fit=crop',
                        'https://images.unsplash.com/photo-1445116572660-236099ec97a0?w=400&h=300&fit=crop',
                        'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=400&h=300&fit=crop',
                        'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=300&fit=crop',
                        'https://images.unsplash.com/photo-1521017432531-fbd92d768814?w=400&h=300&fit=crop',
                    ];
                @endphp
                @foreach(array_merge($images, $images) as $img)
                    <div class="slide-item" style="background-image:url('{{ $img }}');"></div>
                @endforeach
            </div>
        </div>
    </section>

    @if($menuVisible)
    @php
        $menuImages = json_decode(\App\Models\AppSetting::get('landing_menu_images', '[]'), true) ?: [];
    @endphp
    {{-- Menu Slideshow --}}
    <section id="menu" style="padding:4rem 1.5rem;background:#f5f5f4;">
        <div style="max-width:1200px;margin:0 auto;">
            <div style="text-align:center;margin-bottom:2rem;">
                <span style="display:inline-block;padding:0.3rem 0.8rem;border-radius:999px;background:#fef3c7;color:#b45309;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">{{ $getField($menuSection, 'label', 'Menu Kami') }}</span>
                <h2 style="font-size:1.75rem;font-weight:800;color:#1c1917;">Menu Kami</h2>
                <p style="color:#78716c;font-size:0.9rem;margin-top:0.5rem;">Slide untuk lihat semua menu</p>
            </div>

            @if(count($menuImages) > 0)
            <div style="display:flex;gap:1rem;overflow-x:auto;padding-bottom:1rem;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
                @foreach($menuImages as $img)
                @php $imgUrl = is_string($img) ? $img : ($img['url'] ?? ''); $imgTitle = is_string($img) ? '' : ($img['title'] ?? ''); $imgDesc = is_string($img) ? '' : ($img['desc'] ?? ''); @endphp
                <div style="flex-shrink:0;width:280px;scroll-snap-align:start;border-radius:1rem;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.08);background:white;">
                    <img src="{{ $imgUrl }}" style="width:100%;height:320px;object-fit:cover;display:block;">
                    @if($imgTitle || $imgDesc)
                    <div style="padding:0.75rem;">
                        @if($imgTitle)<p style="font-weight:700;font-size:0.8rem;color:#1c1917;">{{ $imgTitle }}</p>@endif
                        @if($imgDesc)<p style="font-size:0.7rem;color:#78716c;margin-top:0.15rem;">{{ $imgDesc }}</p>@endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            <div style="text-align:center;margin-top:0.75rem;">
                <span style="font-size:0.75rem;color:#a8a29e;">Geser ke samping untuk lihat semua menu →</span>
            </div>
            @else
            <div style="text-align:center;padding:3rem;color:#a8a29e;font-size:0.9rem;">
                <p>Foto menu belum tersedia.</p>
            </div>
            @endif
        </div>
    </section>
    @endif
    @endif

    @if($aboutVisible)
    @php $aboutBg = \App\Models\AppSetting::get('landing_about_bg', '') ?: $getField($aboutSection, 'bg_image', ''); @endphp
    {{-- About --}}
    <section id="about" style="padding:5rem 1.5rem;{{ $aboutBg ? 'background:linear-gradient(rgba(245,245,244,0.95),rgba(245,245,244,0.95)),url('.$aboutBg.') center/cover no-repeat;' : 'background:#f5f5f4;' }}">
        <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center;">
            <div>
                <span style="display:inline-block;padding:0.3rem 0.8rem;border-radius:999px;background:#fef3c7;color:#b45309;font-size:0.7rem;font-weight:600;text-transform:uppercase;margin-bottom:0.75rem;">Tentang Kami</span>
                <h2 style="font-size:1.75rem;font-weight:800;color:#1c1917;line-height:1.3;">{{ $getField($aboutSection, 'title', 'Forka Coffee & Space') }}</h2>
                @php $aboutTagline = $getField($aboutSection, 'tagline', ''); @endphp
                @if($aboutTagline)
                <p style="color:#d97706;font-weight:600;font-size:1rem;margin-top:0.5rem;">{{ $aboutTagline }}</p>
                @endif
                <p style="color:#57534e;font-size:0.9rem;margin-top:1rem;line-height:1.7;">
                    {{ $getField($aboutSection, 'description', 'Forka Coffee & Space adalah tempat nongkrong favorit.') }}
                </p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1.5rem;">
                    <div style="background:white;border-radius:0.75rem;padding:1rem;text-align:center;">
                        <p style="font-size:1.5rem;font-weight:800;color:#d97706;">50+</p>
                        <p style="font-size:0.75rem;color:#78716c;">Menu Variasi</p>
                    </div>
                    <div style="background:white;border-radius:0.75rem;padding:1rem;text-align:center;">
                        <p style="font-size:1.5rem;font-weight:800;color:#d97706;">1000+</p>
                        <p style="font-size:0.75rem;color:#78716c;">Pelanggan Puas</p>
                    </div>
                </div>
            </div>
            <div style="border-radius:1rem;overflow:hidden;height:400px;{{ $aboutBg ? 'background:url('.$aboutBg.') center/cover no-repeat;' : 'background:linear-gradient(135deg,#292524,#1c1917);' }}"></div>
        </div>
    </section>
    @endif

    @if($locationVisible)
    @php
        $mapsUrl = $getField($locationSection, 'maps_url', '');
        $igUrl = $getField($locationSection, 'instagram', '');
        $ttUrl = $getField($locationSection, 'tiktok', '');
        $waUrl = $getField($locationSection, 'whatsapp', '');
    @endphp
    {{-- Location --}}
    <section id="location" style="padding:5rem 1.5rem;background:white;">
        <div style="max-width:1200px;margin:0 auto;text-align:center;">
            <span style="display:inline-block;padding:0.3rem 0.8rem;border-radius:999px;background:#fef3c7;color:#b45309;font-size:0.7rem;font-weight:600;text-transform:uppercase;margin-bottom:0.75rem;">{{ $getField($locationSection, 'label', 'Lokasi') }}</span>
            <h2 style="font-size:1.75rem;font-weight:800;color:#1c1917;">{{ $getField($locationSection, 'label', 'Temukan Kami') }}</h2>
            <p style="color:#78716c;font-size:0.9rem;margin-top:0.5rem;max-width:400px;margin-left:auto;margin-right:auto;">
                {{ \App\Models\AppSetting::get('business_name', 'FORKA COFFEE & SPACE') }}
            </p>
            @if($mapsUrl)
            <div style="margin-top:2rem;border-radius:1rem;overflow:hidden;border:1px solid #e7e5e4;">
                <iframe src="{{ $mapsUrl }}" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
            <div style="margin-top:1.5rem;text-align:center;color:#57534e;">
                <p style="font-size:0.9rem;font-weight:600;color:#44403c;">{{ \App\Models\AppSetting::get('business_name', 'FORKA COFFEE & SPACE') }}</p>
                <p style="font-size:0.8rem;margin-top:0.25rem;">{{ $getField($locationSection, 'address', \App\Models\AppSetting::get('landing_address', 'Buka Senin - Sabtu, 08:00 - 22:00')) }}</p>
            </div>
            @if($igUrl || $ttUrl || $waUrl)
            <div style="margin-top:1.5rem;display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
                @if($waUrl)
                <a href="{{ $waUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;background:#25D366;color:white;border-radius:0.75rem;text-decoration:none;font-size:0.85rem;font-weight:600;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                @endif
                @if($ttUrl)
                <a href="{{ $ttUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;background:#111111;color:white;border-radius:0.75rem;text-decoration:none;font-size:0.85rem;font-weight:600;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.02-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    TikTok
                </a>
                @endif
                @if($igUrl)
                <a href="{{ $igUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);color:white;border-radius:0.75rem;text-decoration:none;font-size:0.85rem;font-weight:600;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    Instagram
                </a>
                @endif
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Footer --}}
    <footer style="background:#1c1917;padding:2rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06);">
        <div style="max-width:1200px;margin:0 auto;display:flex;flex-direction:column;align-items:center;gap:0.75rem;text-align:center;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                @if($logoUrl)
                <img src="{{ $logoUrl }}" style="height:1.5rem;width:auto;border-radius:0.25rem;">
                @else
                <div style="width:1.5rem;height:1.5rem;border-radius:0.375rem;background:#d97706;display:flex;align-items:center;justify-content:center;color:white;font-size:0.5rem;font-weight:bold;">F</div>
                @endif
                <span style="color:white;font-weight:600;font-size:0.85rem;">{{ \App\Models\AppSetting::get('business_name', 'FORKA COFFEE & SPACE') }}</span>
            </div>
            <p style="color:#a8a29e;font-size:0.75rem;">&copy; {{ date('Y') }} All rights reserved.</p>
        </div>
    </footer>

    {{-- WhatsApp Float --}}
    <a href="https://wa.me/{{ \App\Models\AppSetting::get('wa_number', '6281234567890') }}" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
