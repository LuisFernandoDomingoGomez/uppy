@extends('layouts.app')

@section('content')
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tarjetas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Administra las tarjetas de lealtad de tu negocio
                </p>
            </div>

            <a href="{{ route('cards.create') }}"
               class="inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                + Agregar
            </a>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-900 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if($cards->count())
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($cards as $card)
                        <a href="{{ route('cards.edit', $card) }}"
                           class="block p-5 bg-gray-50 border border-gray-200 rounded-2xl hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                        {{ $card->type->name }}
                                    </p>
                                    <h3 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $card->name }}
                                    </h3>
                                </div>

                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $card->status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                        : ($card->status === 'draft'
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'
                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300') }}">
                                    {{ ucfirst($card->status) }}
                                </span>
                            </div>

                            <div class="mt-5 rounded-2xl border border-gray-200 bg-white p-4 dark:bg-gray-800 dark:border-gray-600">
                                <div class="rounded-xl bg-gray-100 dark:bg-gray-700 h-36 flex items-center justify-center">
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-300">
                                            Preview
                                        </p>
                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ $card->code_type === 'qr' ? 'QR' : 'Código de barras' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $card->created_at?->format('d/m/Y') }}</span>
                                <span class="truncate max-w-[140px]">{{ $card->slug }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $cards->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Aún no hay tarjetas
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Crea tu primera tarjeta para comenzar.
                    </p>

                    <a href="{{ route('cards.create') }}"
                       class="inline-flex items-center mt-6 px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                        Crear primera tarjeta
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
