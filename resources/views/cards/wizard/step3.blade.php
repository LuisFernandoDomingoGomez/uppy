@extends('layouts.app')

@section('content')
    @php
        $initialLinks = old('links', $card->links->map(fn($link) => [
            'type' => $link->type,
            'value' => $link->value,
            'label' => $link->label,
        ])->toArray());

        if (empty($initialLinks)) {
            $initialLinks = [
                ['type' => 'url', 'value' => '', 'label' => ''],
            ];
        }

        $initialSections = old('sections', $card->sections->map(fn($section) => [
            'title' => $section->title,
            'content' => $section->content,
        ])->toArray());

        if (empty($initialSections)) {
            $initialSections = [
                ['title' => '', 'content' => ''],
            ];
        }

        $displayName = old('display_name', $card->settings_json['display_name'] ?? $card->name);
        $previewPlatform = $card->design?->preview_platform ?? 'ios';
        $mainImageUrl = $card->design?->main_image_path ? asset('storage/' . $card->design->main_image_path) : null;
        $logoHorizontalUrl = $card->design?->logo_horizontal_path ? asset('storage/' . $card->design->logo_horizontal_path) : null;
        $logoSquareUrl = $card->design?->logo_square_path ? asset('storage/' . $card->design->logo_square_path) : null;
        $backgroundColor = $card->design?->background_color ?? '#F3F4F6';
        $textColor = $card->design?->text_color ?? '#111827';
    @endphp

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3"
         x-data="cardDetailsStep(
            @js($initialLinks),
            @js($initialSections),
            @js($displayName)
         )">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Editar tarjeta</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tipo: {{ $card->type->name }} · Estado: {{ ucfirst($card->status) }}
                    </p>
                </div>

                <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                    Draft
                </span>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-900 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('cards.wizard.step1', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">1. Información</a>
                        <a href="{{ route('cards.wizard.step2', $card) }}"
                           class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">2. Diseño</a>
                        <span class="px-3 py-1 rounded-full bg-blue-600 text-white">3. Detalles</span>
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500">4. Notificaciones</span>
                    </div>
                </div>

                <form action="{{ route('cards.wizard.step3.save', $card) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Nombre visible de la tarjeta
                        </label>
                        <input
                            x-model="displayName"
                            name="display_name"
                            type="text"
                            class="w-full p-3 text-sm border border-gray-300 rounded-xl"
                            placeholder="Nombre visible"
                        >
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Términos y condiciones
                        </label>
                        <textarea
                            name="terms"
                            x-model="terms"
                            rows="6"
                            class="w-full p-3 text-sm border border-gray-300 rounded-xl"
                            placeholder="Escribe aquí los términos y condiciones..."
                        >{{ old('terms', $card->terms) }}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-900">Enlaces</label>
                            <button type="button"
                                    @click="links.push({type: 'url', value: '', label: ''})"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                + Agregar enlace
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(link, index) in links" :key="index">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    <div class="md:col-span-3">
                                        <select x-model="link.type"
                                                x-bind:name="`links[${index}][type]`"
                                                class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                            <option value="url">URL</option>
                                            <option value="phone">Teléfono</option>
                                            <option value="whatsapp">WhatsApp</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="facebook">Facebook</option>
                                            <option value="tiktok">TikTok</option>
                                            <option value="email">Email</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-4">
                                        <input x-model="link.value"
                                               x-bind:name="`links[${index}][value]`"
                                               type="text"
                                               placeholder="Valor"
                                               class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                    </div>

                                    <div class="md:col-span-4">
                                        <input x-model="link.label"
                                               x-bind:name="`links[${index}][label]`"
                                               type="text"
                                               placeholder="Texto"
                                               class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                    </div>

                                    <div class="md:col-span-1">
                                        <button type="button"
                                                @click="links.splice(index, 1)"
                                                class="w-full px-3 py-3 text-sm font-medium text-red-600 bg-red-50 rounded-xl hover:bg-red-100">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-900">Secciones adicionales</label>
                            <button type="button"
                                    @click="sections.push({title: '', content: ''})"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                + Agregar sección
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(section, index) in sections" :key="index">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    <div class="md:col-span-4">
                                        <input x-model="section.title"
                                               x-bind:name="`sections[${index}][title]`"
                                               type="text"
                                               placeholder="Título"
                                               class="w-full p-3 text-sm border border-gray-300 rounded-xl">
                                    </div>

                                    <div class="md:col-span-7">
                                        <textarea x-model="section.content"
                                                  x-bind:name="`sections[${index}][content]`"
                                                  rows="3"
                                                  placeholder="Contenido"
                                                  class="w-full p-3 text-sm border border-gray-300 rounded-xl"></textarea>
                                    </div>

                                    <div class="md:col-span-1">
                                        <button type="button"
                                                @click="sections.splice(index, 1)"
                                                class="w-full px-3 py-3 text-sm font-medium text-red-600 bg-red-50 rounded-xl hover:bg-red-100">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('cards.wizard.step2', $card) }}"
                           class="px-5 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                            Anterior
                        </a>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700"
                            >
                                Guardar paso 3
                            </button>

                            <a href="{{ route('cards.wizard.step4', $card) }}"
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
            <p class="mt-1 text-sm text-gray-500">
                Preview reactivo de detalles.
            </p>

            <div class="mt-6 flex justify-center">
                @if($previewPlatform === 'ios')
                    <div class="w-64 rounded-[2rem] border-8 border-gray-900 p-4 shadow-xl" style="background-color: {{ $backgroundColor }}">
                        <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                        <div class="rounded-2xl bg-white p-4 shadow-sm min-h-[360px]" style="color: {{ $textColor }}">
                            <div class="text-center">
                                @if($logoSquareUrl)
                                    <img src="{{ $logoSquareUrl }}" class="h-10 w-10 rounded-lg object-cover mx-auto mb-2" alt="">
                                @elseif($logoHorizontalUrl)
                                    <img src="{{ $logoHorizontalUrl }}" class="h-7 object-contain mx-auto mb-2" alt="">
                                @endif

                                <p class="text-sm font-semibold" x-text="displayName"></p>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-[10px]">
                                <div class="rounded-lg bg-gray-50 p-2">
                                    <p class="opacity-60">Nombre</p>
                                    <p>Juan Pérez</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-2">
                                    <p class="opacity-60">Número</p>
                                    <p>00123456</p>
                                </div>
                            </div>

                            <div class="mt-4 h-20 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center text-sm text-gray-400">
                                @if($mainImageUrl)
                                    <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                @else
                                    Imagen principal
                                @endif
                            </div>

                            <div class="mt-4 text-xs">
                                <template x-for="(link, index) in links.slice(0,2)" :key="index">
                                    <div class="flex justify-between border-b py-1">
                                        <span class="font-medium" x-text="link.type"></span>
                                        <span class="text-blue-600" x-text="link.label || link.value"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="w-72 rounded-[2.2rem] border-[7px] border-slate-900 p-3 shadow-2xl bg-slate-100">
                        <div class="mx-auto mb-3 h-5 w-20 rounded-full bg-slate-900"></div>

                        <div class="rounded-[1.5rem] overflow-hidden shadow-lg bg-white min-h-[390px]">
                            <div class="px-4 pt-4 pb-3 text-center" style="background-color: {{ $backgroundColor }}; color: {{ $textColor }}">
                                @if($logoSquareUrl)
                                    <img src="{{ $logoSquareUrl }}" class="h-10 w-10 rounded-lg object-cover mx-auto mb-2" alt="">
                                @elseif($logoHorizontalUrl)
                                    <img src="{{ $logoHorizontalUrl }}" class="h-7 object-contain mx-auto mb-2" alt="">
                                @endif

                                <p class="text-lg font-semibold leading-tight" x-text="displayName"></p>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="mt-3 h-24 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400">
                                    @if($mainImageUrl)
                                        <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        Imagen principal
                                    @endif
                                </div>

                                <div class="mt-4 text-xs space-y-2">
                                    <template x-for="(section, index) in sections.slice(0,2)" :key="index">
                                        <div class="rounded-xl bg-gray-50 p-3">
                                            <p class="font-semibold" x-text="section.title || 'Sección'"></p>
                                            <p class="text-gray-500" x-text="section.content || 'Contenido'"></p>
                                        </div>
                                    </template>
                                </div>

                                <div class="mt-4 rounded-2xl bg-gray-100 p-4 text-xs">
                                    <p class="font-semibold mb-1">Enlaces</p>
                                    <template x-for="(link, index) in links.slice(0,2)" :key="index">
                                        <div class="flex justify-between py-1">
                                            <span x-text="link.type"></span>
                                            <span class="text-blue-600" x-text="link.label || link.value"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-6 text-sm text-gray-500">
                <p><strong>Términos:</strong></p>
                <p class="text-gray-500" x-text="terms ? terms.substring(0, 120) : 'Sin términos aún.'"></p>
            </div>
        </div>
    </div>

    <script>
        function cardDetailsStep(initialLinks, initialSections, initialDisplayName) {
            return {
                links: initialLinks,
                sections: initialSections,
                displayName: initialDisplayName,
                terms: @js(old('terms', $card->terms)),
            }
        }
    </script>
@endsection
