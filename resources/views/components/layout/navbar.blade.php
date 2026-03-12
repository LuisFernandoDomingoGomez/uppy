<nav class="sticky top-0 z-30 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-between px-4 py-4 lg:px-6">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Panel principal
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Bienvenido, {{ auth()->user()?->name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:block">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        placeholder="Buscar..."
                        class="w-64 py-2.5 pl-10 pr-4 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                </div>
            </div>

            <button id="userMenuButton" data-dropdown-toggle="userDropdown"
                class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700"
                type="button">
                <div class="flex items-center justify-center w-10 h-10 text-sm font-bold text-white bg-blue-600 rounded-full">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                </div>

                <div class="hidden text-left sm:block">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ auth()->user()?->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ ucfirst(auth()->user()?->roles->first()?->name ?? 'Sin rol') }}
                    </p>
                </div>
            </button>

            <div id="userDropdown"
                class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg dark:bg-gray-700 dark:divide-gray-600 min-w-56">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white">{{ auth()->user()?->name }}</span>
                    <span class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ auth()->user()?->email }}</span>
                </div>
                <ul class="py-2">
                    <li>
                        <a href="#"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white">
                            Perfil
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
                            >
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
