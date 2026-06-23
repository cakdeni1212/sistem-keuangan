<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="page-title">Pengaturan Landing Page</h2>
                <p class="page-desc">Atur tampilan dan konten halaman depan Forka Coffee</p>
            </div>
            <a href="{{ route('settings.index') }}" class="btn-secondary btn-sm">Pengaturan Lain</a>
        </div>
    </x-slot>

    {{-- ===== TOAST NOTIFICATION ===== --}}
    @if(session('success'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 3500)"
         x-show="show"
         x-transition.opacity.duration.500ms
         class="fixed top-20 right-5 z-50 flex items-center gap-3 px-5 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-lg"
         style="max-width: 400px;">
        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-emerald-800">Berhasil Disimpan</p>
            <p class="text-xs text-emerald-600 mt-0.5">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
            @endif

            {{-- ===== SECTION SETTINGS ===== --}}
            <div class="card mb-4">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-900">Section Settings</p>
                    <p class="text-xs text-surface-400">Atur visibilitas, urutan, dan isi setiap section landing page</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data" x-data="landingBuilder({{ json_encode($config) }})">
                @csrf
                @method('PUT')

                {{-- Hidden input untuk config JSON --}}
                <input type="hidden" name="landing_config" :value="JSON.stringify(sections)">

                {{-- Section List --}}
                <template x-for="(section, index) in sections" :key="section.key">
                    <div class="card mb-4"
                         :class="{ 'opacity-40': !section.visible }"
                         :data-section="section.key">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-surface-100 cursor-move"
                             @mousedown="startDrag(index, $event)"
                             style="user-select:none;">
                            <div class="flex items-center gap-3">
                                <span class="text-surface-300 cursor-grab active:cursor-grabbing">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                </span>
                                <span class="text-lg" x-text="section.icon"></span>
                                <div>
                                    <p class="text-sm font-semibold text-surface-900" x-text="section.label"></p>
                                    <p class="text-xs text-surface-400" x-text="section.desc"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="moveUp(index)" :disabled="index === 0"
                                        class="btn-icon" :class="index === 0 ? '!text-surface-200' : ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button type="button" @click="moveDown(index)" :disabled="index === sections.length - 1"
                                        class="btn-icon" :class="index === sections.length - 1 ? '!text-surface-200' : ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="section.visible" class="sr-only peer">
                                    <div class="w-9 h-5 bg-surface-200 rounded-full peer peer-checked:bg-brand-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                </label>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 space-y-4" x-show="section.visible">
                            <template x-for="(field, fIdx) in section.fields" :key="fIdx">
                                <div>
                                    <label class="label" x-text="field.label"></label>

                                    {{-- Text input --}}
                                    <input x-show="field.type === 'text'"
                                           type="text"
                                           :name="'field_' + section.key + '_' + field.key"
                                           x-model="field.value"
                                           class="input-field"
                                           :placeholder="field.placeholder || ''">

                                    {{-- Textarea --}}
                                    <textarea x-show="field.type === 'textarea'"
                                              :name="'field_' + section.key + '_' + field.key"
                                              x-model="field.value"
                                              class="input-field !resize-y"
                                              rows="3"
                                              :placeholder="field.placeholder || ''"></textarea>

                                    {{-- Image URL --}}
                                    <input x-show="field.type === 'image'"
                                           type="text"
                                           :name="'field_' + section.key + '_' + field.key"
                                           x-model="field.value"
                                           class="input-field"
                                           placeholder="https://...">

                                    {{-- About Background Upload (in-card) --}}
                                    <div x-show="section.key === 'about' && field.key === 'description'">
                                        @php $abtBg = \App\Models\AppSetting::get('landing_about_bg', ''); @endphp
                                        <div class="mt-4 pt-4 border-t border-surface-100">
                                            <p class="text-xs font-semibold text-surface-600 mb-2">Background Tentang Kami</p>
                                            @if($abtBg)
                                            <div class="relative mb-3 rounded-xl overflow-hidden border border-surface-200 h-24">
                                                <img src="{{ $abtBg }}" class="w-full h-full object-cover">
                                                <form method="POST" action="{{ route('settings.landing.update') }}" class="absolute top-2 right-2">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="delete_about_bg" value="1">
                                                    <button type="submit" class="w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                                </form>
                                            </div>
                                            @endif
                                            <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input type="file" name="about_bg_image" accept="image/*" class="flex-1 text-xs text-surface-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                                <button type="submit" class="btn-primary btn-sm !text-xs shrink-0">Upload</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                    <div x-show="section.key === 'menu' && field.key === 'label'" x-data="{ files: [], previews: [] }">
                                        @php $menuImages = json_decode(\App\Models\AppSetting::get('landing_menu_images', '[]'), true) ?: []; @endphp
                                        <div class="mt-4 pt-4 border-t border-surface-100">
                                            <p class="text-xs font-semibold text-surface-600 mb-3">Foto Menu</p>
                                            @if(count($menuImages) > 0)
                                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mb-4">
                                                @foreach($menuImages as $img)
                                                @php $imgUrl2 = is_string($img) ? $img : ($img['url'] ?? ''); @endphp
                                                <div class="relative group rounded-lg overflow-hidden border border-surface-200 aspect-[3/4] bg-surface-50">
                                                    <img src="{{ $imgUrl2 }}" class="w-full h-full object-cover">
                                                    <form method="POST" action="{{ route('settings.landing.update') }}" class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-black/40 flex items-center justify-center">
                                                        @csrf @method('PUT')
                                                        <input type="hidden" name="delete_menu_image" value="{{ $imgUrl2 }}">
                                                        <button type="submit" class="w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 shadow-lg text-xs">✕</button>
                                                    </form>
                                                </div>
                                                @endforeach
                                            </div>
                                            @else
                                            <p class="text-xs text-surface-400 mb-3">Belum ada foto menu.</p>
                                            @endif
                                            <div class="border-2 border-dashed border-surface-300 rounded-xl p-4 text-center cursor-pointer hover:border-brand-400 transition" @click="$refs.menuGalleryInput.click()">
                                                <input type="file" name="menu_images[]" multiple accept="image/*" class="hidden" x-ref="menuGalleryInput"
                                                       @change="files = Array.from($event.target.files); previews = []; files.forEach(f => { const r = new FileReader(); r.onload = e => previews.push(e.target.result); r.readAsDataURL(f); });">
                                                <svg class="w-6 h-6 mx-auto text-surface-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/></svg>
                                                <p class="text-xs text-surface-500 font-medium">Tambah Foto</p>
                                            </div>
                                            <template x-if="files.length > 0">
                                                <div class="mt-3">
                                                    <p class="text-xs text-surface-600 mb-2" x-text="files.length + ' foto dipilih'"></p>
                                                    <button type="submit" class="btn-primary btn-sm w-full">Upload <span x-text="files.length"></span> Foto</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="flex items-center justify-between pt-4 mt-4 border-t border-surface-100">
                    <a href="{{ url('/') }}" target="_blank" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat Halaman
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

            {{-- ===== MEDIA UPLOADS ===== --}}
            <div class="card mb-4 mt-6">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-900">Media Uploads</p>
                    <p class="text-xs text-surface-400">Upload logo, favicon, background, dan gambar slideshow</p>
                </div>
            </div>

            {{-- Logo & Favicon --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="card">
                    <div class="px-5 py-4 border-b border-surface-100">
                        <p class="text-sm font-semibold text-surface-900">Logo</p>
                        <p class="text-xs text-surface-400">Upload logo brand</p>
                    </div>
                    <div class="p-5">
                        @php $logo = \App\Models\AppSetting::get('landing_logo', ''); @endphp
                        @if($logo)
                        <div class="relative mb-3 rounded-xl overflow-hidden border border-surface-200 h-24 w-24 mx-auto">
                            <img src="{{ $logo }}" class="w-full h-full object-contain">
                            <form method="POST" action="{{ route('settings.landing.update') }}" class="absolute top-1 right-1">
                                @csrf @method('PUT')
                                <input type="hidden" name="delete_logo" value="1">
                                <button type="submit" class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs">✕</button>
                            </form>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="file" name="logo_image" accept="image/*" class="block w-full text-sm text-surface-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            <button type="submit" class="btn-primary btn-sm mt-2 w-full">Upload Logo</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="px-5 py-4 border-b border-surface-100">
                        <p class="text-sm font-semibold text-surface-900">Favicon</p>
                        <p class="text-xs text-surface-400">Icon tab browser (.ico, .png)</p>
                    </div>
                    <div class="p-5">
                        @php $favicon = \App\Models\AppSetting::get('landing_favicon', ''); @endphp
                        @if($favicon)
                        <div class="relative mb-3 rounded-xl overflow-hidden border border-surface-200 h-16 w-16 mx-auto">
                            <img src="{{ $favicon }}" class="w-full h-full object-contain">
                            <form method="POST" action="{{ route('settings.landing.update') }}" class="absolute top-1 right-1">
                                @csrf @method('PUT')
                                <input type="hidden" name="delete_favicon" value="1">
                                <button type="submit" class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs">✕</button>
                            </form>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <input type="file" name="favicon_image" accept=".ico,.png,.jpg,.jpeg,.svg" class="block w-full text-sm text-surface-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            <button type="submit" class="btn-primary btn-sm mt-2 w-full">Upload Favicon</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Hero Background Upload --}}
            <div class="card mb-4">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-900">Hero Background</p>
                    <p class="text-xs text-surface-400">Upload gambar latar hero banner</p>
                </div>
                <div class="p-5">
                    @php $heroBg = \App\Models\AppSetting::get('landing_hero_bg', ''); @endphp
                    @if($heroBg)
                    <div class="relative mb-4 rounded-xl overflow-hidden border border-surface-200 h-32">
                        <img src="{{ $heroBg }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input type="file" name="hero_bg_image" accept="image/*" class="block w-full text-sm text-surface-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            </div>
                            <button type="submit" class="btn-primary btn-sm shrink-0">Upload</button>
                            @if($heroBg)<button type="submit" name="delete_hero_bg" value="1" class="btn-danger btn-sm shrink-0">Hapus</button>@endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Slideshow Images --}}
            <div class="card mb-4" x-data="{ files: [], previews: [] }">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-900">Foto Slideshow</p>
                    <p class="text-xs text-surface-400">Upload & kelola foto galeri landing page</p>
                </div>
                <div class="p-5">
                    @php $slideshowImages = json_decode(\App\Models\AppSetting::get('landing_slideshow_images', '[]'), true) ?: []; @endphp
                    @if(count($slideshowImages) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-5">
                        @foreach($slideshowImages as $img)
                        <div class="relative group rounded-xl overflow-hidden border border-surface-200 aspect-[4/3] bg-surface-50">
                            <img src="{{ $img }}" class="w-full h-full object-cover">
                            <form method="POST" action="{{ route('settings.landing.update') }}" class="absolute inset-0 opacity-0 group-hover:opacity-100 transition bg-black/40 flex items-center justify-center">
                                @csrf @method('PUT') <input type="hidden" name="delete_image" value="{{ $img }}">
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 shadow-lg">✕</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-surface-400 mb-3">Belum ada foto.</p>
                    @endif
                    <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="border-2 border-dashed border-surface-300 rounded-xl p-4 text-center cursor-pointer hover:border-brand-400 transition mb-3" @click="$refs.slideshowInput.click()">
                            <input type="file" name="slideshow_images[]" multiple accept="image/*" class="hidden" x-ref="slideshowInput"
                                   @change="files = Array.from($event.target.files); previews = []; files.forEach(f => { const r = new FileReader(); r.onload = e => previews.push(e.target.result); r.readAsDataURL(f); });">
                            <p class="text-xs text-surface-500 font-medium">Tambah Foto (drag / klik)</p>
                        </div>
                        <template x-if="files.length > 0">
                            <button type="submit" class="btn-primary w-full">Upload <span x-text="files.length"></span> Foto</button>
                        </template>
                    </form>
                </div>
            </div>

            {{-- Menu Images --}}
            <div class="card mb-4" x-data="{ files: [], previews: [] }">
                <div class="px-5 py-4 border-b border-surface-100">
                    <p class="text-sm font-semibold text-surface-900">📋 Foto Menu</p>
                    <p class="text-xs text-surface-400">Upload, edit judul, atur urutan foto menu</p>
                </div>
                <div class="p-5">
                    @php $menuImages = json_decode(\App\Models\AppSetting::get('landing_menu_images', '[]'), true) ?: []; @endphp
                    @if(count($menuImages) > 0)
                    <div class="space-y-3 mb-5" id="menu-image-list">
                        @foreach($menuImages as $i => $img)
                        @php $imgUrl = is_string($img) ? $img : ($img['url'] ?? ''); $imgTitle = is_string($img) ? '' : ($img['title'] ?? ''); $imgDesc = is_string($img) ? '' : ($img['desc'] ?? ''); @endphp
                        <div class="flex gap-3 bg-surface-50 rounded-xl p-3">
                            <div class="flex flex-col gap-1 shrink-0">
                                <form method="POST" action="{{ route('settings.landing.update') }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="move_menu_image" value="{{ $imgUrl }}">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" {{ $i === 0 ? 'disabled' : '' }} class="w-6 h-6 rounded text-surface-400 hover:text-brand-600 {{ $i === 0 ? 'opacity-30' : '' }}" title="Geser ke atas">▲</button>
                                </form>
                                <form method="POST" action="{{ route('settings.landing.update') }}" class="inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="move_menu_image" value="{{ $imgUrl }}">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" {{ $i === count($menuImages) - 1 ? 'disabled' : '' }} class="w-6 h-6 rounded text-surface-400 hover:text-brand-600 {{ $i === count($menuImages) - 1 ? 'opacity-30' : '' }}" title="Geser ke bawah">▼</button>
                                </form>
                            </div>
                            <div class="w-20 h-20 rounded-lg overflow-hidden border border-surface-200 shrink-0">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <form method="POST" action="{{ route('settings.landing.update') }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="edit_menu_image" value="{{ $imgUrl }}">
                                    <input type="text" name="menu_title" value="{{ $imgTitle }}" placeholder="Judul menu" class="input-field !py-1.5 !text-xs mb-1">
                                    <div class="flex gap-2">
                                        <input type="text" name="menu_desc" value="{{ $imgDesc }}" placeholder="Deskripsi singkat" class="input-field !py-1.5 !text-xs flex-1">
                                        <button type="submit" class="btn-primary btn-sm !text-xs shrink-0">Simpan</button>
                                    </div>
                                </form>
                            </div>
                            <form method="POST" action="{{ route('settings.landing.update') }}" class="shrink-0 flex items-start">
                                @csrf @method('PUT')
                                <input type="hidden" name="delete_menu_image" value="{{ $imgUrl }}">
                                <button type="submit" class="w-7 h-7 rounded-lg bg-red-100 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center text-xs transition" title="Hapus" onclick="return confirm('Hapus foto ini?')">✕</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-surface-400 mb-3">Belum ada foto menu.</p>
                    @endif
                    <form method="POST" action="{{ route('settings.landing.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <label class="text-xs font-semibold text-surface-600 mb-1 block">Tambah Foto Baru</label>
                        <div class="border-2 border-dashed border-surface-300 rounded-xl p-4 text-center cursor-pointer hover:border-brand-400 transition mb-2" @click="$refs.menuInput2.click()" @dragover.prevent @drop.prevent="files = Array.from($event.dataTransfer.files).filter(f => f.type.startsWith('image/')); previews = []; files.forEach(f => { const r = new FileReader(); r.onload = e => previews.push(e.target.result); r.readAsDataURL(f); })">
                            <input type="file" name="menu_images[]" multiple accept="image/*" class="hidden" x-ref="menuInput2"
                                   @change="files = Array.from($event.target.files); previews = []; files.forEach(f => { const r = new FileReader(); r.onload = e => previews.push(e.target.result); r.readAsDataURL(f); })">
                            <template x-if="previews.length === 0">
                                <div>
                                    <svg class="w-8 h-8 mx-auto text-surface-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12"/></svg>
                                    <p class="text-sm text-surface-500 font-medium">Klik atau drag & drop foto menu</p>
                                    <p class="text-xs text-surface-400 mt-1">Bisa pilih banyak foto sekaligus</p>
                                </div>
                            </template>
                            <template x-if="previews.length > 0">
                                <div>
                                    <p class="text-sm font-semibold text-brand-600 mb-2" x-text="previews.length + ' foto dipilih'"></p>
                                    <div class="grid grid-cols-6 sm:grid-cols-8 gap-2">
                                        <template x-for="(preview, idx) in previews" :key="idx">
                                            <div class="aspect-square rounded-lg overflow-hidden border border-surface-200"><img :src="preview" class="w-full h-full object-cover"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="submit" class="btn-primary w-full" x-show="files.length > 0">Upload <span x-text="files.length"></span> Foto</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
    function landingBuilder(config) {
        return {
            sections: config.sections || (Array.isArray(config) ? config : []),
            dragIndex: null,

            moveUp(index) {
                if (index === 0) return;
                [this.sections[index - 1], this.sections[index]] = [this.sections[index], this.sections[index - 1]];
            },

            moveDown(index) {
                if (index === this.sections.length - 1) return;
                [this.sections[index], this.sections[index + 1]] = [this.sections[index + 1], this.sections[index]];
            },

            startDrag(index, event) {
                this.dragIndex = index;
                const startY = event.clientY;
                const el = event.target.closest('[data-section]');
                const origY = el.getBoundingClientRect().top;

                const onMove = (e) => {
                    const diff = e.clientY - startY;
                    el.style.transform = `translateY(${diff}px)`;
                    el.style.zIndex = 10;
                    el.style.position = 'relative';

                    // Detect swap
                    const siblings = [...document.querySelectorAll('[data-section]')];
                    const currentIdx = siblings.indexOf(el);
                    if (currentIdx > 0 && diff < -20) {
                        this.moveUp(currentIdx);
                        this.dragIndex = currentIdx - 1;
                        el.style.transform = '';
                    } else if (currentIdx < siblings.length - 1 && diff > 20) {
                        this.moveDown(currentIdx);
                        this.dragIndex = currentIdx + 1;
                        el.style.transform = '';
                    }
                };

                const onUp = () => {
                    el.style.transform = '';
                    el.style.zIndex = '';
                    el.style.position = '';
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                };

                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            }
        };
    }
    </script>
</x-app-layout>
