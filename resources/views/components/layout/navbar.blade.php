<nav class="bg-white border border-gray-200 rounded-lg px-4 py-3 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                Panel principal
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Bienvenido al sistema
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button id="userMenuButton" data-dropdown-toggle="userDropdown"
                class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                type="button">
                <span class="sr-only">Abrir menú de usuario</span>
                <div class="w-8 h-8 rounded-full bg-gray-300"></div>
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
