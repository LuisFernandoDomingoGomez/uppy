<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <div class="mb-6 px-2">
            <span class="text-2xl font-bold text-gray-900 dark:text-white">
                Uppy
            </span>
        </div>

        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039A1 1 0 0 0 16.975 11Z"/>
                        <path d="M12.5 0c-.276 0-.5.224-.5.5v8a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5A8.5 8.5 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            @can('view users')
                <li>
                    <a href="#"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor" viewBox="0 0 20 18">
                            <path d="M14 2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 14a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1Z"/>
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Usuarios</span>
                    </a>
                </li>
            @endcan

            @can('manage roles')
                <li>
                    <a href="#"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a2 2 0 0 0-2 2v1.172A3.001 3.001 0 0 0 6 8v1H5a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2h-1V8a3.001 3.001 0 0 0-2-2.828V4a2 2 0 0 0-2-2Z"/>
                        </svg>
                        <span class="ms-3">Roles y permisos</span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>
