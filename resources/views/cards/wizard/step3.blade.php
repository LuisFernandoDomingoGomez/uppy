@extends('layouts.app')

@section('content')
    @php
        $displayName = old('display_name', $card->settings_json['display_name'] ?? $card->name);
        $terms = old('terms', $card->terms);

        $links = old(
            'links',
            $card->links->map(fn ($link) => [
                'type' => $link->type,
                'value' => $link->value,
                'label' => $link->label,
            ])->values()->toArray()
        );

        $sections = old(
            'sections',
            $card->sections->map(fn ($section) => [
                'title' => $section->title,
                'content' => $section->content,
            ])->values()->toArray()
        );

        if (empty($links)) {
            $links = [
                ['type' => 'url', 'value' => '', 'label' => ''],
            ];
        }

        if (empty($sections)) {
            $sections = [
                ['title' => '', 'content' => ''],
            ];
        }

        $design = $card->design;
        $logoHorizontalUrl = $design?->logo_horizontal_path ? asset('storage/' . $design->logo_horizontal_path) : null;
        $logoSquareUrl = $design?->logo_square_path ? asset('storage/' . $design->logo_square_path) : null;
        $mainImageUrl = $design?->main_image_path ? asset('storage/' . $design->main_image_path) : null;
        $backgroundColor = $design?->background_color ?? '#F3F4F6';
        $textColor = $design?->text_color ?? '#111827';
    @endphp

    <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
        x-data="{
            autosave: null,
            previewPlatform: localStorage.getItem('uppy_preview_platform') || 'ios',

            form: {
                display_name: @js($displayName),
                terms: @js($terms),
                links: @js($links),
                sections: @js($sections),
            },

            init() {
                this.autosave = window.uppyAutosave({
                    url: '{{ route('cards.autosave', $card) }}',
                    section: 'step3'
                });
            },

            changed() {
                this.autosave.queue(this.form);
            },

            addLink() {
                this.form.links.push({
                    type: 'url',
                    value: '',
                    label: ''
                });
                this.changed();
            },

            removeLink(index) {
                this.form.links.splice(index, 1);

                if (!this.form.links.length) {
                    this.form.links.push({
                        type: 'url',
                        value: '',
                        label: ''
                    });
                }

                this.changed();
            },

            addSection() {
                this.form.sections.push({
                    title: '',
                    content: ''
                });
                this.changed();
            },

            removeSection(index) {
                this.form.sections.splice(index, 1);

                if (!this.form.sections.length) {
                    this.form.sections.push({
                        title: '',
                        content: ''
                    });
                }

                this.changed();
            },

            previewLabel(link) {
                return link.label && link.label.trim() !== '' ? link.label : (link.value || 'Sin texto');
            }
        }"
    >
        {{-- Panel principal --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200">
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

            {{-- Indicador autosave --}}
            <div class="px-6 pt-3">
                <div id="autosave-indicator" class="text-xs text-gray-400">
                    Guardado
                </div>
            </div>

            {{-- Tabs --}}
            <div class="px-6 pt-4 pb-3 border-b border-gray-200">
                <div class="grid grid-cols-4 gap-2 text-xs sm:text-sm">
                    <a href="{{ route('cards.wizard.step1', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Información
                    </a>

                    <a href="{{ route('cards.wizard.step2', $card) }}"
                       class="relative pb-2 text-gray-400 border-b-2 border-gray-200">
                        Diseño
                    </a>

                    <div class="relative pb-2 text-blue-600 border-b-2 border-blue-600">
                        Detalles
                    </div>

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

                {{-- Nombre visible --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">
                        Nombre visible de la tarjeta
                    </label>

                    <input
                        type="text"
                        x-model="form.display_name"
                        @input="changed()"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        placeholder="Ej. Club Coffee Premium"
                    >
                </div>

                {{-- Términos --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">
                        Términos y condiciones
                    </label>

                    <textarea
                        x-model="form.terms"
                        @input="changed()"
                        rows="5"
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                        placeholder="Escribe aquí los términos y condiciones..."
                    ></textarea>
                </div>

                {{-- Links --}}
                <div class="border-t border-gray-200 pt-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Enlaces</h3>

                        <button
                            type="button"
                            @click="addLink()"
                            class="inline-flex items-center justify-center px-3 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition"
                        >
                            + Agregar enlace
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(link, index) in form.links" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start border border-gray-200 rounded-xl p-4">
                                <div class="md:col-span-3">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Tipo</label>
                                    <select
                                        x-model="link.type"
                                        @change="changed()"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                    >
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
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Valor</label>
                                    <input
                                        type="text"
                                        x-model="link.value"
                                        @input="changed()"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                        placeholder="Ej. https://..."
                                    >
                                </div>

                                <div class="md:col-span-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Texto</label>
                                    <input
                                        type="text"
                                        x-model="link.label"
                                        @input="changed()"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                        placeholder="Ej. Visitar sitio"
                                    >
                                </div>

                                <div class="md:col-span-1 flex md:justify-end">
                                    <button
                                        type="button"
                                        @click="removeLink(index)"
                                        class="mt-0 md:mt-7 inline-flex items-center justify-center px-3 py-2 text-sm text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Secciones --}}
                <div class="border-t border-gray-200 pt-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Secciones adicionales</h3>

                        <button
                            type="button"
                            @click="addSection()"
                            class="inline-flex items-center justify-center px-3 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition"
                        >
                            + Agregar sección
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(section, index) in form.sections" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start border border-gray-200 rounded-xl p-4">
                                <div class="md:col-span-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Título</label>
                                    <input
                                        type="text"
                                        x-model="section.title"
                                        @input="changed()"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                        placeholder="Ej. Beneficios"
                                    >
                                </div>

                                <div class="md:col-span-7">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Contenido</label>
                                    <textarea
                                        x-model="section.content"
                                        @input="changed()"
                                        rows="3"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg"
                                        placeholder="Describe esta sección..."
                                    ></textarea>
                                </div>

                                <div class="md:col-span-1 flex md:justify-end">
                                    <button
                                        type="button"
                                        @click="removeSection(index)"
                                        class="mt-0 md:mt-7 inline-flex items-center justify-center px-3 py-2 text-sm text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Navegación --}}
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <a href="{{ route('cards.wizard.step2', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Anterior
                    </a>

                    <a href="{{ route('cards.wizard.step4', $card) }}"
                       class="inline-flex items-center justify-center px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        Siguiente
                    </a>
                </div>
            </div>
        </div>

        {{-- Preview lateral --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-900">Vista previa</h3>
            <p class="mt-1 text-xs text-gray-500">Detalle reactivo de la tarjeta.</p>

            <div class="mt-5 flex justify-center">
                {{-- iOS --}}
                <template x-if="previewPlatform === 'ios'">
                    <div class="w-64 rounded-[2rem] border-8 border-gray-900 p-4 shadow-2xl"
                         style="background: linear-gradient(180deg, {{ $backgroundColor }} 0%, #f3f4f6 100%)">
                        <div class="mx-auto mb-4 h-6 w-24 rounded-full bg-gray-900"></div>

                        <div class="rounded-[1.4rem] bg-white p-4 shadow-lg min-h-[390px] border border-gray-100"
                             style="color: {{ $textColor }}">

                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    @if($logoHorizontalUrl)
                                        <img src="{{ $logoHorizontalUrl }}" class="h-8 object-contain mb-2" alt="">
                                    @elseif($logoSquareUrl)
                                        <img src="{{ $logoSquareUrl }}" class="h-9 w-9 object-cover rounded-lg mb-2" alt="">
                                    @endif

                                    <p class="text-sm font-semibold leading-tight" x-text="form.display_name || '{{ $card->name }}'"></p>
                                </div>

                                <div class="text-right">
                                    <p class="text-[10px] opacity-60">Estado</p>
                                    <p class="text-[10px]">{{ ucfirst($card->status) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 h-20 rounded-2xl bg-gray-100 overflow-hidden flex items-center justify-center text-sm text-gray-400">
                                @if($mainImageUrl)
                                    <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                @else
                                    Imagen principal
                                @endif
                            </div>

                            <div class="mt-4 space-y-2">
                                <template x-for="(link, index) in form.links.slice(0, 2)" :key="'ios-link-'+index">
                                    <div class="rounded-xl bg-gray-50 px-3 py-2 border border-gray-100">
                                        <p class="text-[10px] uppercase tracking-wide text-gray-400" x-text="link.type || 'url'"></p>
                                        <p class="text-xs font-medium text-blue-600 truncate" x-text="previewLabel(link)"></p>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-4 rounded-xl bg-gray-50 p-3 border border-gray-100">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-1">Términos</p>
                                <p class="text-[11px] text-gray-600 line-clamp-4" x-text="form.terms || 'Sin términos aún.'"></p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Android --}}
                <template x-if="previewPlatform === 'android'">
                    <div class="w-72 rounded-[2.2rem] border-[7px] border-slate-900 p-3 shadow-2xl bg-slate-100">
                        <div class="mx-auto mb-3 h-5 w-20 rounded-full bg-slate-900"></div>

                        <div class="rounded-[1.6rem] overflow-hidden shadow-xl bg-white min-h-[410px] border border-gray-100">
                            <div class="px-4 pt-4 pb-3"
                                 style="background: linear-gradient(135deg, {{ $backgroundColor }} 0%, #ffffff 100%); color: {{ $textColor }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        @if($logoSquareUrl)
                                            <img src="{{ $logoSquareUrl }}" class="h-10 w-10 rounded-full object-cover shadow-sm" alt="">
                                        @elseif($logoHorizontalUrl)
                                            <img src="{{ $logoHorizontalUrl }}" class="h-7 object-contain" alt="">
                                        @endif

                                        <div>
                                            <p class="text-xs opacity-70">{{ $card->type->name }}</p>
                                            <p class="font-semibold leading-tight" x-text="form.display_name || '{{ $card->name }}'"></p>
                                        </div>
                                    </div>

                                    <div class="text-right text-[10px] opacity-70">
                                        <p>Estado</p>
                                        <p>{{ ucfirst($card->status) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 pb-4">
                                <div class="mt-4 h-20 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center text-sm text-gray-400 shadow-sm">
                                    @if($mainImageUrl)
                                        <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        Imagen principal
                                    @endif
                                </div>

                                <div class="mt-4 space-y-2">
                                    <template x-for="(section, index) in form.sections.slice(0, 2)" :key="'android-section-'+index">
                                        <div class="rounded-xl bg-gray-50 p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-800" x-text="section.title || 'Sección'"></p>
                                            <p class="mt-1 text-[11px] text-gray-500 line-clamp-3" x-text="section.content || 'Sin contenido'"></p>
                                        </div>
                                    </template>
                                </div>

                                <div class="mt-4 rounded-xl bg-gray-50 p-3 border border-gray-100">
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 mb-2">Enlaces</p>

                                    <template x-for="(link, index) in form.links.slice(0, 2)" :key="'android-link-'+index">
                                        <div class="flex items-center justify-between gap-2 py-1">
                                            <span class="text-[11px] text-gray-400 uppercase" x-text="link.type || 'url'"></span>
                                            <span class="text-[11px] text-blue-600 truncate" x-text="previewLabel(link)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-5 text-xs text-gray-500 space-y-1">
                <p><span class="font-medium text-gray-700">Nombre:</span> <span x-text="form.display_name || '{{ $card->name }}'"></span></p>
                <p><span class="font-medium text-gray-700">Links:</span> <span x-text="form.links.length"></span></p>
                <p><span class="font-medium text-gray-700">Secciones:</span> <span x-text="form.sections.length"></span></p>
                <p><span class="font-medium text-gray-700">Preview:</span> <span x-text="previewPlatform"></span></p>
            </div>
        </div>
    </div>
@endsection
