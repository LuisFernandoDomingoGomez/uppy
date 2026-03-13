@extends('layouts.app')

@section('content')
    @php
        use App\Support\CardPreviewCode;

        $design = $card->design;
        $displayName = $card->settings_json['display_name'] ?? $card->name;

        $backgroundColor = old('background_color', $design?->background_color ?? '#F3F4F6');
        $activeColor = old('active_color', $design?->active_color ?? '#2563EB');
        $inactiveColor = old('inactive_color', $design?->inactive_color ?? '#D1D5DB');
        $textColor = old('text_color', $design?->text_color ?? '#111827');

        $stampActiveIconType = old('stamp_active_icon_type', $design?->stamp_active_icon_type ?? 'preset');
        $stampActiveIconValue = old('stamp_active_icon_value', $design?->stamp_active_icon_value ?? 'star');

        $stampInactiveIconType = old('stamp_inactive_icon_type', $design?->stamp_inactive_icon_type ?? 'preset');
        $stampInactiveIconValue = old('stamp_inactive_icon_value', $design?->stamp_inactive_icon_value ?? 'circle');

        $codeType = old('code_type', $card->code_type ?? 'qr');

        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;

        $stampActiveImageUrl = $design?->stamp_active_image_path ? asset('storage/' . $design->stamp_active_image_path) : null;
        $stampInactiveImageUrl = $design?->stamp_inactive_image_path ? asset('storage/' . $design->stamp_inactive_image_path) : null;

        $qrDataUri = CardPreviewCode::qrDataUri($card->id);
        $barcodeDataUri = CardPreviewCode::barcodeDataUri($card->id);
        $barcodeLabel = CardPreviewCode::barcodeLabel($card->id);

        $supportsStamps = $card->type->code === 'stamps';
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_390px]"
        x-data="{
            autosave: null,
            previewPlatform: localStorage.getItem('uppy_preview_platform') || 'ios',

            form: {
                code_type: @js($codeType),
                background_color: @js($backgroundColor),
                active_color: @js($activeColor),
                inactive_color: @js($inactiveColor),
                text_color: @js($textColor),
                stamp_active_icon_type: @js($stampActiveIconType),
                stamp_active_icon_value: @js($stampActiveIconValue),
                stamp_inactive_icon_type: @js($stampInactiveIconType),
                stamp_inactive_icon_value: @js($stampInactiveIconValue),
            },

            logoHorizontalUrl: @js($logoHorizontalUrl),
            logoSquareUrl: @js($logoSquareUrl),
            mainImageUrl: @js($mainImageUrl),
            stampActiveImageUrl: @js($stampActiveImageUrl),
            stampInactiveImageUrl: @js($stampInactiveImageUrl),

            init() {
                this.autosave = window.uppyAutosave({
                    url: '{{ route('cards.autosave', $card) }}',
                    section: 'step2'
                });
            },

            setPreview(platform) {
                this.previewPlatform = platform;
                localStorage.setItem('uppy_preview_platform', platform);
            },

            changed() {
                this.autosave.queue(this.form);
            },

            previewFile(event, target) {
                const file = event.target.files[0];
                if (!file) return;
                this[target] = URL.createObjectURL(file);
            },

            activeIconChar() {
                return this.resolvePresetIcon(this.form.stamp_active_icon_value);
            },

            inactiveIconChar() {
                return this.resolvePresetIcon(this.form.stamp_inactive_icon_value);
            },

            resolvePresetIcon(icon) {
                const map = {
                    star: '★',
                    circle: '●',
                    gift: '🎁',
                    heart: '♥',
                    coffee: '☕',
                    ticket: '🎟',
                    burger: '🍔',
                    pizza: '🍕',
                    icecream: '🍦',
                    bolt: '⚡',
                    diamond: '◆'
                };

                return map[icon] || '★';
            }
        }"
    >
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Diseño</h2>
                    <p class="text-sm text-gray-500">
                        Ajusta colores, branding y apariencia visual de la tarjeta.
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                    Draft
                </span>
            </div>

            <div class="px-6 pt-3">
                <div id="autosave-indicator" class="text-xs text-gray-400">
                    Guardado
                </div>
            </div>

            <div class="px-6 pt-4 pb-3 border-b border-gray-200">
                <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
                    <a href="{{ route('cards.wizard.step1', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Información
                    </a>

                    <div class="relative pb-2 text-blue-600 border-b-2 border-blue-600">
                        Diseño
                    </div>

                    <a href="{{ route('cards.wizard.step3', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Detalles
                    </a>

                    <a href="{{ route('cards.wizard.step4', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Notificaciones
                    </a>
                </div>
            </div>

            <div class="px-6 py-5 space-y-6">
                @if(session('success'))
                    <div class="px-4 py-3 text-sm text-green-800 bg-green-50 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="px-4 py-3 text-sm text-red-800 bg-red-50 rounded-lg">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">Color de fondo</label>
                        <div class="flex items-center gap-3">
                            <input
                                type="color"
                                x-model="form.background_color"
                                @input="changed()"
                                class="w-14 h-11 p-1 bg-white border border-gray-300 rounded-lg"
                            >
                            <input
                                type="text"
                                x-model="form.background_color"
                                @input="changed()"
                                class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">Color del texto</label>
                        <div class="flex items-center gap-3">
                            <input
                                type="color"
                                x-model="form.text_color"
                                @input="changed()"
                                class="w-14 h-11 p-1 bg-white border border-gray-300 rounded-lg"
                            >
                            <input
                                type="text"
                                x-model="form.text_color"
                                @input="changed()"
                                class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                        </div>
                    </div>

                    @if($supportsStamps)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Color del sello activo</label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="color"
                                    x-model="form.active_color"
                                    @input="changed()"
                                    class="w-14 h-11 p-1 bg-white border border-gray-300 rounded-lg"
                                >
                                <input
                                    type="text"
                                    x-model="form.active_color"
                                    @input="changed()"
                                    class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Color del sello inactivo</label>
                            <div class="flex items-center gap-3">
                                <input
                                    type="color"
                                    x-model="form.inactive_color"
                                    @input="changed()"
                                    class="w-14 h-11 p-1 bg-white border border-gray-300 rounded-lg"
                                >
                                <input
                                    type="text"
                                    x-model="form.inactive_color"
                                    @input="changed()"
                                    class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                >
                            </div>
                        </div>
                    @endif
                </div>

                <form
                    action="{{ route('cards.wizard.step2.save', $card) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-4"
                >
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="code_type" :value="form.code_type">
                    <input type="hidden" name="background_color" :value="form.background_color">
                    <input type="hidden" name="active_color" :value="form.active_color">
                    <input type="hidden" name="inactive_color" :value="form.inactive_color">
                    <input type="hidden" name="text_color" :value="form.text_color">
                    <input type="hidden" name="stamp_active_icon_type" :value="form.stamp_active_icon_type">
                    <input type="hidden" name="stamp_active_icon_value" :value="form.stamp_active_icon_value">
                    <input type="hidden" name="stamp_inactive_icon_type" :value="form.stamp_inactive_icon_type">
                    <input type="hidden" name="stamp_inactive_icon_value" :value="form.stamp_inactive_icon_value">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Logotipo horizontal</label>
                            <input
                                type="file"
                                name="logo_horizontal"
                                @change="previewFile($event, 'logoHorizontalUrl')"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-3 file:px-4 file:py-2.5 file:border-0 file:bg-gray-100 file:text-gray-700"
                            >
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Logotipo cuadrado</label>
                            <input
                                type="file"
                                name="logo_square"
                                @change="previewFile($event, 'logoSquareUrl')"
                                class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-3 file:px-4 file:py-2.5 file:border-0 file:bg-gray-100 file:text-gray-700"
                            >
                        </div>

                        @unless($supportsStamps)
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Imagen principal</label>
                                <input
                                    type="file"
                                    name="main_image"
                                    @change="previewFile($event, 'mainImageUrl')"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-3 file:px-4 file:py-2.5 file:border-0 file:bg-gray-100 file:text-gray-700"
                                >
                            </div>
                        @endunless
                    </div>

                    @if($supportsStamps)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Imagen de sello activo</label>
                                <input
                                    type="file"
                                    name="stamp_active_image"
                                    @change="previewFile($event, 'stampActiveImageUrl')"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-3 file:px-4 file:py-2.5 file:border-0 file:bg-gray-100 file:text-gray-700"
                                >
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-800">Imagen de sello inactivo</label>
                                <input
                                    type="file"
                                    name="stamp_inactive_image"
                                    @change="previewFile($event, 'stampInactiveImageUrl')"
                                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg file:mr-3 file:px-4 file:py-2.5 file:border-0 file:bg-gray-100 file:text-gray-700"
                                >
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition"
                        >
                            Subir imágenes
                        </button>
                    </div>
                </form>

                @if($supportsStamps)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Ícono activo</label>
                            <select
                                x-model="form.stamp_active_icon_value"
                                @change="form.stamp_active_icon_type = 'preset'; changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                                <option value="star">Estrella</option>
                                <option value="heart">Corazón</option>
                                <option value="gift">Regalo</option>
                                <option value="circle">Círculo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Ícono inactivo</label>
                            <select
                                x-model="form.stamp_inactive_icon_value"
                                @change="form.stamp_inactive_icon_type = 'preset'; changed()"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                            >
                                <option value="circle">Círculo</option>
                                <option value="star">Estrella</option>
                                <option value="heart">Corazón</option>
                                <option value="gift">Regalo</option>
                            </select>
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-800">Tipo de código</label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="form.code_type = 'qr'; changed()"
                            :class="form.code_type === 'qr'
                                ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500'
                                : 'border-gray-300 bg-white'"
                            class="flex items-center gap-3 p-4 text-left border rounded-2xl transition"
                        >
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-600 text-white text-sm font-bold">
                                QR
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-900">QR</p>
                                <p class="text-xs text-gray-500">Código cuadrado</p>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="form.code_type = 'barcode'; changed()"
                            :class="form.code_type === 'barcode'
                                ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500'
                                : 'border-gray-300 bg-white'"
                            class="flex items-center gap-3 p-4 text-left border rounded-2xl transition"
                        >
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold">
                                Ⅲ
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-900">Barras</p>
                                <p class="text-xs text-gray-500">Código lineal</p>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <a href="{{ route('cards.wizard.step1', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Anterior
                    </a>

                    <a href="{{ route('cards.wizard.step3', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Siguiente
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900">Vista previa</h3>
            <p class="mt-1 text-xs text-gray-500">Simulación visual por dispositivo.</p>

            <div class="mt-5 flex justify-center">
                <template x-if="previewPlatform === 'ios'">
                    <div class="relative w-[276px] rounded-[2.7rem] border-[7px] border-[#111111] bg-[#111111] p-[8px] shadow-[0_20px_45px_rgba(0,0,0,0.22)]">
                        <div class="absolute left-1/2 top-[10px] z-20 -translate-x-1/2 w-[74px] h-[19px] rounded-full bg-black shadow-inner"></div>

                        <div class="rounded-[2.15rem] overflow-hidden min-h-[560px] bg-[#f3f2f8] p-3 pt-9">
                            <div class="px-2">
                                <div
                                    class="rounded-[1.7rem] overflow-hidden border border-white/80 shadow-[0_18px_34px_rgba(0,0,0,0.16)]"
                                    :style="`background: linear-gradient(180deg, ${form.background_color} 0%, #ffffff 86%); color:${form.text_color}`"
                                >
                                    <div class="px-4 pt-4 pb-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <template x-if="logoHorizontalUrl">
                                                    <img :src="logoHorizontalUrl" class="h-6 object-contain shrink-0">
                                                </template>

                                                <template x-if="!logoHorizontalUrl && logoSquareUrl">
                                                    <img :src="logoSquareUrl" class="h-9 w-9 rounded-xl object-cover bg-white shadow-sm shrink-0">
                                                </template>

                                                <template x-if="!logoHorizontalUrl && !logoSquareUrl">
                                                    <div class="flex items-center justify-center h-9 w-9 rounded-xl bg-white text-xs font-semibold text-gray-700 shadow-sm shrink-0">
                                                        U
                                                    </div>
                                                </template>

                                                <div class="min-w-0">
                                                    <p class="text-[10px] opacity-55 truncate">{{ $card->type->name }}</p>
                                                    <p class="text-[15px] font-semibold leading-tight truncate">{{ $displayName }}</p>
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <p class="text-[10px] opacity-45">Estado</p>
                                                <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                            </div>
                                        </div>

                                        @unless($supportsStamps)
                                            <div class="mt-4 h-28 rounded-[1.1rem] bg-white/70 overflow-hidden border border-white/70">
                                                <template x-if="mainImageUrl">
                                                    <img :src="mainImageUrl" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!mainImageUrl">
                                                    <div class="w-full h-full flex items-center justify-center text-sm text-gray-400">
                                                        Imagen principal
                                                    </div>
                                                </template>
                                            </div>
                                        @endunless

                                        @if($supportsStamps)
                                            <div class="mt-4 rounded-[1.15rem] bg-white/65 border border-white/70 px-3 py-3">
                                                <div class="grid grid-cols-5 gap-2">
                                                    <template x-for="index in 10" :key="'ios-'+index">
                                                        <div
                                                            class="h-8.5 w-8.5 rounded-full flex items-center justify-center text-sm font-bold shadow-sm overflow-hidden"
                                                            :style="index === 1
                                                                ? `background:${form.active_color}; color:white;`
                                                                : `background:${form.inactive_color}; color:${form.text_color}; opacity:0.88;`"
                                                        >
                                                            <template x-if="index === 1 && stampActiveImageUrl">
                                                                <img :src="stampActiveImageUrl" class="w-full h-full object-cover">
                                                            </template>

                                                            <template x-if="!(index === 1 && stampActiveImageUrl) && !(index !== 1 && stampInactiveImageUrl)">
                                                                <span x-text="index === 1 ? activeIconChar() : inactiveIconChar()"></span>
                                                            </template>

                                                            <template x-if="index !== 1 && stampInactiveImageUrl">
                                                                <img :src="stampInactiveImageUrl" class="w-full h-full object-cover">
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-5">
                                            <p class="text-xs opacity-60 mb-2">Código</p>
                                            <div class="rounded-[1.1rem] bg-white/80 border border-white/70 p-4 min-h-[122px] flex flex-col items-center justify-center">
                                                <template x-if="form.code_type === 'qr'">
                                                    <img :src="@js($qrDataUri)" class="w-24 h-24 object-contain">
                                                </template>

                                                <template x-if="form.code_type === 'barcode'">
                                                    <div class="w-full text-center">
                                                        <img :src="@js($barcodeDataUri)" class="mx-auto h-14 object-contain">
                                                        <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="previewPlatform === 'android'">
                    <div class="relative w-[280px] rounded-[2.45rem] border-[7px] border-[#151515] bg-[#151515] p-[8px] shadow-[0_20px_45px_rgba(0,0,0,0.22)]">
                        <div class="absolute left-1/2 top-[11px] z-20 -translate-x-1/2 h-[10px] w-[10px] rounded-full bg-black border border-neutral-700"></div>

                        <div class="rounded-[1.95rem] overflow-hidden min-h-[560px] bg-[#f6f7f8] p-3 pt-7">
                            <div
                                class="overflow-hidden rounded-[1.35rem] border border-gray-200 bg-white shadow-[0_10px_24px_rgba(0,0,0,0.10)]"
                                :style="`color:${form.text_color}`"
                            >
                                <div
                                    class="px-4 pt-4 pb-3"
                                    :style="`background: linear-gradient(135deg, ${form.background_color} 0%, ${form.active_color} 100%); color: white;`"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <template x-if="logoSquareUrl">
                                                <img :src="logoSquareUrl" class="h-9 w-9 rounded-full object-cover border border-white/40 shadow-sm shrink-0 bg-white">
                                            </template>

                                            <template x-if="!logoSquareUrl && logoHorizontalUrl">
                                                <img :src="logoHorizontalUrl" class="h-6 object-contain shrink-0 brightness-[1.15]">
                                            </template>

                                            <template x-if="!logoSquareUrl && !logoHorizontalUrl">
                                                <div class="h-9 w-9 rounded-full bg-white/20 flex items-center justify-center text-xs font-semibold shrink-0">
                                                    U
                                                </div>
                                            </template>

                                            <div class="min-w-0">
                                                <p class="text-[10px] text-white/75 truncate">{{ $card->type->name }}</p>
                                                <p class="text-[15px] font-semibold leading-tight truncate text-white">{{ $displayName }}</p>
                                            </div>
                                        </div>

                                        <div class="text-right text-[10px] text-white/75 shrink-0">
                                            <p>Estado</p>
                                            <p>{{ ucfirst($card->status) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-4 bg-white">
                                    @unless($supportsStamps)
                                        <div class="h-24 rounded-[0.95rem] overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 border border-gray-100">
                                            <template x-if="mainImageUrl">
                                                <img :src="mainImageUrl" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!mainImageUrl">
                                                <span>Imagen principal</span>
                                            </template>
                                        </div>
                                    @endunless

                                    @if($supportsStamps)
                                        <div class="mt-4">
                                            <p class="mb-2 text-[11px] font-medium text-gray-500">Progreso</p>
                                            <div class="grid grid-cols-5 gap-2">
                                                <template x-for="index in 10" :key="'android-'+index">
                                                    <div
                                                        class="h-8.5 w-8.5 rounded-lg flex items-center justify-center text-sm font-bold border overflow-hidden"
                                                        :style="index === 1
                                                            ? `background:${form.active_color}; color:white; border-color:${form.active_color};`
                                                            : `background:white; color:${form.inactive_color}; border-color:${form.inactive_color};`"
                                                    >
                                                        <template x-if="index === 1 && stampActiveImageUrl">
                                                            <img :src="stampActiveImageUrl" class="w-full h-full object-cover">
                                                        </template>

                                                        <template x-if="!(index === 1 && stampActiveImageUrl) && !(index !== 1 && stampInactiveImageUrl)">
                                                            <span x-text="index === 1 ? activeIconChar() : inactiveIconChar()"></span>
                                                        </template>

                                                        <template x-if="index !== 1 && stampInactiveImageUrl">
                                                            <img :src="stampInactiveImageUrl" class="w-full h-full object-cover">
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-5 rounded-[1rem] bg-[#f8fafc] p-4 border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-medium text-gray-500">Código</p>
                                            <div class="h-1.5 w-10 rounded-full" :style="`background:${form.active_color}`"></div>
                                        </div>

                                        <template x-if="form.code_type === 'qr'">
                                            <div class="flex justify-center">
                                                <img :src="@js($qrDataUri)" class="w-24 h-24 object-contain">
                                            </div>
                                        </template>

                                        <template x-if="form.code_type === 'barcode'">
                                            <div class="text-center">
                                                <img :src="@js($barcodeDataUri)" class="mx-auto h-14 object-contain">
                                                <p class="mt-2 text-[10px] tracking-widest text-gray-500">{{ $barcodeLabel }}</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4 space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        @click="setPreview('ios')"
                        :class="previewPlatform === 'ios'
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    >
                        <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-900 text-white text-xs"></span>
                        iOS
                    </button>

                    <button
                        type="button"
                        @click="setPreview('android')"
                        :class="previewPlatform === 'android'
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition"
                    >
                        <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-bold">A</span>
                        Android
                    </button>
                </div>
            </div>

            <div class="mt-5 text-xs text-gray-500 space-y-1">
                <p><span class="font-medium text-gray-700">Nombre:</span> {{ $displayName }}</p>
                <p><span class="font-medium text-gray-700">Tipo:</span> {{ $card->type->code }}</p>
                <p><span class="font-medium text-gray-700">Preview:</span> <span x-text="previewPlatform"></span></p>
                <p><span class="font-medium text-gray-700">Código:</span> <span x-text="form.code_type"></span></p>
            </div>
        </div>
    </div>
@endsection
