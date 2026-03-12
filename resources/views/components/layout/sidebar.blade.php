<aside
    id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar"
>
    <div class="flex flex-col h-full">
        <div class="flex items-center h-20 px-6 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 text-lg font-bold text-white bg-blue-600 rounded-xl">
                    U
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Uppy</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Admin panel</p>
                </div>
            </a>
        </div>

        <div class="flex-1 px-4 py-6 overflow-y-auto">
            <p class="px-3 mb-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                Menú
            </p>

            <ul class="space-y-2 font-medium">
                @can('view dashboard')
                    <li>
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                           {{ request()->routeIs('dashboard')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                 fill="currentColor" viewBox="0 0 22 21">
                                <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039A1 1 0 0 0 16.975 11Z"/>
                                <path d="M12.5 0c-.276 0-.5.224-.5.5v8a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5A8.5 8.5 0 0 0 12.5 0Z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                @can('view users')
                    <li>
                        <a href="{{ route('users.index') }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                           {{ request()->routeIs('users.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                 fill="currentColor" viewBox="0 0 20 18">
                                <path d="M14 2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18 14a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1Z"/>
                            </svg>
                            <span>Usuarios</span>
                        </a>
                    </li>
                @endcan

                @can('manage roles')
                    <li>
                        <a href="{{ route('roles.index') }}"
                           class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                           {{ request()->routeIs('roles.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('roles.*') ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a2 2 0 0 0-2 2v1.172A3.001 3.001 0 0 0 6 8v1H5a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2h-1V8a3.001 3.001 0 0 0-2-2.828V4a2 2 0 0 0-2-2Z"/>
                            </svg>
                            <span>Roles y permisos</span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ auth()->user()?->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ ucfirst(auth()->user()?->roles->first()?->name ?? 'Sin rol') }}
                </p>
            </div>
        </div>
    </div>
</aside>
