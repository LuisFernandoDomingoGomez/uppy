@extends('layouts.app')

@section('content')
    @php
        use App\Support\CardPreviewCode;

        $design = $card->design;
        $backgroundColor = old('background_color', $design->background_color ?? '#F3F4F6');
        $activeColor = old('active_color', $design->active_color ?? '#2563EB');
        $inactiveColor = old('inactive_color', $design->inactive_color ?? '#D1D5DB');
        $textColor = old('text_color', $design->text_color ?? '#111827');
        $codeType = old('code_type', $card->code_type ?? 'qr');
        $stampActive = old('stamp_active_icon_value', $design->stamp_active_icon_value ?? 'star');
        $stampInactive = old('stamp_inactive_icon_value', $design->stamp_inactive_icon_value ?? 'circle');
        $previewPlatform = old('preview_platform', $design->preview_platform ?? 'ios');

        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;

        $qrDataUri = CardPreviewCode::qrDataUri($card->id);
        $barcodeDataUri = CardPreviewCode::barcodeDataUri($card->id);
        $barcodeLabel = CardPreviewCode::barcodeLabel($card->id);
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3"
         x-data="{
            codeType: '{{ $codeType }}',
            previewPlatform: '{{ $previewPlatform }}',
            backgroundColor: '{{ $backgroundColor }}',
            activeColor: '{{ $activeColor }}',
            inactiveColor: '{{ $inactiveColor }}',
            textColor: '{{ $textColor }}',
            stampActive: '{{ $stampActive }}',
            stampInactive: '{{ $stampInactive }}',
            logoHorizontalUrl: @js($logoHorizontalUrl),
            logoSquareUrl: @js($logoSquareUrl),
            mainImageUrl: @js($mainImageUrl),
            qrDataUri: @js($qrDataUri),
            barcodeDataUri: @js($barcodeDataUri),
            barcodeLabel: @js($barcodeLabel),

            previewFile(event, target) {
                const file = event.target.files[0];
                if (!file) return;
                this[target] = URL.createObjectURL(file);
            },

            iconSymbol(name) {
                const map = {
                    star: '★',
                    heart: '♥',
                    gift: '🎁',
                    circle: '●'
                };
                return map[name] ?? '●';
            }
         }">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Editar tarjeta</h2>
                    <p class="text-sm text-gray-500">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">
                    Draft
                </span>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('cards.wizard.step1', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">1. Información</a>
                        <span class="px-3 py-1 rounded-full bg-blue-600 text-white">2. Diseño</span>
                        <a href="{{ route('cards.wizard.step3', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">3. Detalles</a>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">4. Notificaciones</span>
                    </div>
                </div>

                <form action="{{ route('cards.wizard.step2.save', $card) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <div>
                            <label class="block mb-3 text-sm font-medium text-gray-900">
                                Plataforma de vista previa
                            </label>

                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="preview_platform" value="ios" x-model="previewPlatform" class="sr-only">
                                    <div class="flex items-center gap-3 rounded-2xl border px-4 py-4 transition"
                                         :class="previewPlatform === 'ios'
                                            ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 bg-white hover:border-gray-300'">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white text-lg">
                                            
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">iOS</p>
                                            <p class="text-xs text-gray-500">Apple Wallet style</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="preview_platform" value="android" x-model="previewPlatform" class="sr-only">
                                    <div class="flex items-center gap-3 rounded-2xl border px-4 py-4 transition"
                                         :class="previewPlatform === 'android'
                                            ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 bg-white hover:border-gray-300'">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white text-lg font-bold">
                                            A
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Android</p>
                                            <p class="text-xs text-gray-500">Google Wallet style</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Color de fondo</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="background_color" x-model="backgroundColor" class="w-16 h-12 rounded border border-gray-300">
                                    <input type="text" x-model="backgroundColor" class="flex-1 p-3 text-sm border border-gray-300 rounded-xl">
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Color del texto</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="text_color" x-model="textColor" class="w-16 h-12 rounded border border-gray-300">
                                    <input type="text" x-model="textColor" class="flex-1 p-3 text-sm border border-gray-300 rounded-xl">
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Color del sello activo</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="active_color" x-model="activeColor" class="w-16 h-12 rounded border border-gray-300">
                                    <input type="text" x-model="activeColor" class="flex-1 p-3 text-sm border border-gray-300 rounded-xl">
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Color del sello inactivo</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="inactive_color" x-model="inactiveColor" class="w-16 h-12 rounded border border-gray-300">
                                    <input type="text" x-model="inactiveColor" class="flex-1 p-3 text-sm border border-gray-300 rounded-xl">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Logotipo horizontal</label>
                                <input type="file" name="logo_horizontal" @change="previewFile($event, 'logoHorizontalUrl')" class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                @if($design?->logo_horizontal_path)
                                    <p class="mt-2 text-xs text-gray-500">{{ basename($design->logo_horizontal_path) }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Logotipo cuadrado</label>
                                <input type="file" name="logo_square" @change="previewFile($event, 'logoSquareUrl')" class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                @if($design?->logo_square_path)
                                    <p class="mt-2 text-xs text-gray-500">{{ basename($design->logo_square_path) }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Imagen principal</label>
                                <input type="file" name="main_image" @change="previewFile($event, 'mainImageUrl')" class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                @if($design?->main_image_path)
                                    <p class="mt-2 text-xs text-gray-500">{{ basename($design->main_image_path) }}</p>
                                @endif
                            </div>
                        </div>

                        @if($card->type->code === 'stamps')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Ícono activo</label>
                                    <select name="stamp_active_icon_value" x-model="stampActive" class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                        <option value="star">Estrella</option>
                                        <option value="heart">Corazón</option>
                                        <option value="gift">Regalo</option>
                                        <option value="circle">Círculo</option>
                                    </select>
                                    <input type="hidden" name="stamp_active_icon_type" value="preset">
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Ícono inactivo</label>
                                    <select name="stamp_inactive_icon_value" x-model="stampInactive" class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                        <option value="circle">Círculo</option>
                                        <option value="star">Estrella</option>
                                        <option value="heart">Corazón</option>
                                        <option value="gift">Regalo</option>
                                    </select>
                                    <input type="hidden" name="stamp_inactive_icon_type" value="preset">
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block mb-3 text-sm font-medium text-gray-900">Tipo de código</label>

                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="code_type" value="qr" x-model="codeType" class="sr-only">
                                    <div class="flex items-center gap-3 rounded-2xl border px-4 py-4 transition"
                                         :class="codeType === 'qr'
                                            ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 bg-white hover:border-gray-300'">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white font-bold">
                                            QR
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">QR</p>
                                            <p class="text-xs text-gray-500">Código cuadrado</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="code_type" value="barcode" x-model="codeType" class="sr-only">
                                    <div class="flex items-center gap-3 rounded-2xl border px-4 py-4 transition"
                                         :class="codeType === 'barcode'
                                            ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 bg-white hover:border-gray-300'">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-900 text-white text-xs font-bold">
                                            |||
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Barras</p>
                                            <p class="text-xs text-gray-500">Código lineal</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('cards.wizard.step1', $card) }}"
                           class="px-5 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                            Anterior
                        </a>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                            >
                                Guardar paso 2
                            </button>

                            <a href="{{ route('cards.wizard.step3', $card) }}"
                               class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                Siguiente
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900">Vista previa</h3>
            <p class="mt-1 text-sm text-gray-500">Vista reactiva por plataforma.</p>

            <div class="mt-6 flex justify-center">
                <template x-if="previewPlatform === 'ios'">
                    <div class="w-64 rounded-[2rem] border-8 border-gray-900 p-4 shadow-2xl" :style="`background: linear-gradient(180deg, ${backgroundColor} 0%, #f3f4f6 100%)`">
                        <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                        <div class="rounded-[1.4rem] bg-white p-4 shadow-lg min-h-[390px] border border-gray-100" :style="`color:${textColor}`">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <template x-if="logoHorizontalUrl">
                                        <img :src="logoHorizontalUrl" class="h-8 object-contain mb-2">
                                    </template>
                                    <template x-if="!logoHorizontalUrl && logoSquareUrl">
                                        <img :src="logoSquareUrl" class="h-9 w-9 object-cover rounded-lg mb-2">
                                    </template>
                                    <template x-if="!logoHorizontalUrl && !logoSquareUrl">
                                        <p class="text-xs font-medium">{{ $card->type->name }}</p>
                                    </template>
                                    <p class="text-sm font-semibold leading-tight">{{ $card->name }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-[10px] opacity-60">Estado</p>
                                    <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 h-24 rounded-2xl bg-gray-100 overflow-hidden flex items-center justify-center text-sm text-gray-400">
                                <template x-if="mainImageUrl">
                                    <img :src="mainImageUrl" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!mainImageUrl">
                                    <span>Imagen principal</span>
                                </template>
                            </div>

                            @if($card->type->code === 'stamps')
                                <div class="mt-4 grid grid-cols-5 gap-2">
                                    <template x-for="n in 10" :key="n">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold shadow-sm"
                                             :style="n === 1
                                                ? `background-color:${activeColor}; color:#fff`
                                                : `background-color:${inactiveColor}; color:${textColor}`">
                                            <span x-text="n === 1 ? iconSymbol(stampActive) : iconSymbol(stampInactive)"></span>
                                        </div>
                                    </template>
                                </div>
                            @endif

                            <div class="mt-5">
                                <p class="text-xs opacity-60 mb-2">Código</p>
                                <div class="rounded-2xl bg-gray-50 p-3 flex flex-col items-center justify-center min-h-[110px] border border-gray-100">
                                    <template x-if="codeType === 'qr'">
                                        <img :src="qrDataUri" class="w-24 h-24 object-contain">
                                    </template>

                                    <template x-if="codeType === 'barcode'">
                                        <div class="w-full text-center">
                                            <img :src="barcodeDataUri" class="mx-auto h-14 object-contain">
                                            <p class="mt-2 text-[10px] tracking-widest text-gray-500" x-text="barcodeLabel"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="previewPlatform === 'android'">
                    <div class="w-72 rounded-[2.2rem] border-[7px] border-slate-900 p-3 shadow-2xl bg-slate-100">
                        <div class="mx-auto mb-3 h-5 w-20 rounded-full bg-slate-900"></div>

                        <div class="rounded-[1.6rem] overflow-hidden shadow-xl bg-white min-h-[410px] border border-gray-100">
                            <div class="px-4 pt-4 pb-3" :style="`background: linear-gradient(135deg, ${backgroundColor} 0%, #ffffff 100%); color:${textColor}`">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <template x-if="logoSquareUrl">
                                            <img :src="logoSquareUrl" class="h-10 w-10 rounded-full object-cover shadow-sm">
                                        </template>
                                        <template x-if="!logoSquareUrl && logoHorizontalUrl">
                                            <img :src="logoHorizontalUrl" class="h-7 object-contain">
                                        </template>
                                        <template x-if="!logoSquareUrl && !logoHorizontalUrl">
                                            <div class="h-10 w-10 rounded-full bg-white/80 flex items-center justify-center text-xs font-semibold shadow-sm">
                                                U
                                            </div>
                                        </template>

                                        <div>
                                            <p class="text-xs opacity-70">{{ $card->type->name }}</p>
                                            <p class="font-semibold leading-tight">{{ $card->name }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right text-[10px] opacity-70">
                                        <p>Estado</p>
                                        <p>{{ ucfirst($card->status) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="mt-4 h-24 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 shadow-sm">
                                    <template x-if="mainImageUrl">
                                        <img :src="mainImageUrl" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!mainImageUrl">
                                        <span>Imagen principal</span>
                                    </template>
                                </div>

                                @if($card->type->code === 'stamps')
                                    <div class="mt-4 grid grid-cols-5 gap-2">
                                        <template x-for="n in 10" :key="n">
                                            <div class="h-9 w-9 rounded-xl flex items-center justify-center text-xs font-bold shadow-sm"
                                                 :style="n === 1
                                                    ? `background-color:${activeColor}; color:#fff`
                                                    : `background-color:${inactiveColor}; color:${textColor}`">
                                                <span x-text="n === 1 ? iconSymbol(stampActive) : iconSymbol(stampInactive)"></span>
                                            </div>
                                        </template>
                                    </div>
                                @endif

                                <div class="mt-5 rounded-2xl bg-gray-50 p-4 border border-gray-100 shadow-sm">
                                    <p class="text-xs opacity-60 mb-2">Código</p>

                                    <template x-if="codeType === 'qr'">
                                        <div class="flex justify-center">
                                            <img :src="qrDataUri" class="w-28 h-28 object-contain">
                                        </div>
                                    </template>

                                    <template x-if="codeType === 'barcode'">
                                        <div class="text-center">
                                            <img :src="barcodeDataUri" class="mx-auto h-14 object-contain">
                                            <p class="mt-2 text-[10px] tracking-widest text-gray-500" x-text="barcodeLabel"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p><strong>ID:</strong> {{ $card->id }}</p>
                <p><strong>Tipo:</strong> {{ $card->type->code }}</p>
                <p><strong>Plataforma:</strong> <span x-text="previewPlatform"></span></p>
                <p><strong>Código:</strong> <span x-text="codeType"></span></p>
            </div>
        </div>
    </div>
@endsection
