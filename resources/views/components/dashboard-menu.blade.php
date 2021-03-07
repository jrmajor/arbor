<ul class="uppercase">
    <div class="flex flex-col xs:flex-row md:flex-col">
        <div class="flex-grow">

            <{{ $active === 'users' ? 'span' : 'a' }}
                href="{{ route('dashboard.users') }}"
                class="{{ $active === 'users' ? 'text-blue-700' : 'group text-gray-700 hover:text-gray-800 focus:text-gray-800 focus:outline-none' }}
                    transition-colors duration-100 ease-out">
                <li class="px-3 py-1 rounded
                        {{ $active !== 'dashboard.users' ? 'group-hover:bg-gray-200 group-focus:bg-gray-200' : '' }}
                        transition-colors duration-100 ease-out">
                    <span class="w-full {{ $active === 'users' ? 'border-b-2 border-dotted border-blue-500' : '' }} flex items-center">
                        <svg class="h-4 w-4 mr-2 fill-current
                                {{ $active === 'users' ? 'text-blue-600' : 'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700' }}
                                transition-colors duration-100 ease-out"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 8a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1c2.15 0 4.2.4 6.1 1.09L12 16h-1.25L10 20H4l-.75-4H2L.9 10.09A17.93 17.93 0 0 1 7 9zm8.31.17c1.32.18 2.59.48 3.8.92L18 16h-1.25L16 20h-3.96l.37-2h1.25l1.65-8.83zM13 0a4 4 0 1 1-1.33 7.76 5.96 5.96 0 0 0 0-7.52C12.1.1 12.53 0 13 0z"/>
                        </svg>
                        Users
                    </span>
                </li>
            </{{ $active === 'users' ? 'span' : 'a' }}>

            <{{ $active === 'activityLog' ? 'span' : 'a' }}
                href="{{ route('dashboard.activityLog') }}"
                class="{{ $active === 'activityLog' ? 'text-blue-700' : 'group text-gray-700 hover:text-gray-800 focus:text-gray-800 focus:outline-none' }}
                    transition-colors duration-100 ease-out">
                <li class="px-3 py-1 rounded
                    {{ $active !== 'activityLog' ? 'group-hover:bg-gray-200 group-focus:bg-gray-200' : '' }}
                    transition-colors duration-100 ease-out">
                    <span class="w-full {{ $active === 'activityLog' ? 'border-b-2 border-dotted border-blue-500' : '' }} flex items-center">
                        <svg class="h-4 w-4 mr-2 fill-current
                                {{ $active === 'activityLog' ? 'text-blue-600' : 'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700' }}
                                transition-colors duration-100 ease-out"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 1.5a1 1 0 01.949.684L12.5 14.338l1.551-4.654A1 1 0 0115 9h4a1 1 0 110 2h-3.28l-2.271 6.816a1 1 0 01-1.898 0L7.5 5.662l-1.551 4.654A1 1 0 015 11H1a1 1 0 110-2h3.28L6.55 2.184A1 1 0 017.5 1.5z"/>
                        </svg>
                        Activity log
                    </span>
                </li>
            </{{ $active === 'activityLog' ? 'span' : 'a' }}>

            <{{ $active === 'reports' ? 'span' : 'a' }}
                href="{{ route('dashboard.reports') }}"
                class="{{ $active === 'reports' ? 'text-blue-700' : 'group text-gray-700 hover:text-gray-800 focus:text-gray-800 focus:outline-none' }}
                    transition-colors duration-100 ease-out">
                <li class="px-3 py-1 rounded
                    {{ $active !== 'dashboard.reports' ? 'group-hover:bg-gray-200 group-focus:bg-gray-200' : '' }}
                    transition-colors duration-100 ease-out">
                    <span class="w-full {{ $active === 'reports' ? 'border-b-2 border-dotted border-blue-500' : '' }} flex items-center">
                        <svg class="h-4 w-4 mr-2 fill-current
                                {{ $active === 'reports' ? 'text-blue-600' : 'text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700' }}
                                transition-colors duration-100 ease-out"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5h2v6H9V5zm0 8h2v2H9v-2z"/>
                        </svg>
                        Reports
                    </span>
                </li>
            </{{ $active === 'reports' ? 'span' : 'a' }}>

        </div>
    </div>
</ul>
