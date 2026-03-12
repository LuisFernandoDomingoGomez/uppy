<nav class="bg-white border border-gray-200 rounded-lg px-4 py-3 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                Panel principal
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Bienvenido, {{ auth()->user()?->name }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button id="userMenuButton" data-dropdown-toggle="userDropdown"
                class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                type="button">
                <div class="flex items-center justify-center w-10 h-10 text-sm font-bold text-white bg-blue-600 rounded-full">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                </div>

                <div class="hidden text-left sm:block">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ auth()->user()?->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ ucfirst(auth()->user()?->roles->first()?->name ?? 'Sin rol') }}
                    </p>
                </div>
            </button>

            <div id="userDropdown"
                class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm dark:bg-gray-700 dark:divide-gray-600">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white">{{ auth()->user()?->name }}</span>
                    <span class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ auth()->user()?->email }}</span>
                </div>
                <ul class="py-2">
                    <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white">
                            Perfil
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
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
