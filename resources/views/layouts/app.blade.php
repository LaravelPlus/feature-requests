<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Feature Requests') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        border: "hsl(214.3 31.8% 91.4%)",
                        input: "hsl(214.3 31.8% 91.4%)",
                        ring: "hsl(222.2 84% 4.9%)",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(222.2 84% 4.9%)",
                        primary: {
                            DEFAULT: "hsl(222.2 47.4% 11.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        secondary: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                        destructive: {
                            DEFAULT: "hsl(0 84.2% 60.2%)",
                            foreground: "hsl(210 40% 98%)",
                        },
                        muted: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(215.4 16.3% 46.9%)",
                        },
                        accent: {
                            DEFAULT: "hsl(210 40% 96%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                        popover: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                        card: {
                            DEFAULT: "hsl(0 0% 100%)",
                            foreground: "hsl(222.2 84% 4.9%)",
                        },
                    },
                    borderRadius: {
                        lg: "0.5rem",
                        md: "calc(0.5rem - 2px)",
                        sm: "calc(0.5rem - 4px)",
                    },
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .content-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="font-sans antialiased bg-background text-foreground">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
        <!-- Sidebar -->
        <div class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border lg:translate-x-0 lg:static lg:inset-0"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Sidebar Header -->
            <div class="flex h-16 items-center justify-between px-6 border-b border-border">
                <div class="flex items-center space-x-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary">
                        <i data-lucide="lightbulb" class="h-4 w-4 text-primary-foreground"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-foreground">Feature Requests</h1>
                        <p class="text-xs text-muted-foreground">Admin Panel</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden">
                    <i data-lucide="x" class="h-5 w-5 text-muted-foreground"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 p-4">
                <div class="space-y-1">
                    <a href="{{ route('feature-requests.index') }}" 
                       class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('feature-requests.index') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <i data-lucide="layout-dashboard" class="mr-3 h-4 w-4"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('feature-requests.create') }}" 
                       class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('feature-requests.create') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <i data-lucide="plus-circle" class="mr-3 h-4 w-4"></i>
                        Create Request
                    </a>

                    <a href="#" 
                       class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                        <i data-lucide="users" class="mr-3 h-4 w-4"></i>
                        Users
                    </a>

                    <a href="#" 
                       class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                        <i data-lucide="tag" class="mr-3 h-4 w-4"></i>
                        Categories
                    </a>

                    <a href="#" 
                       class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                        <i data-lucide="bar-chart-3" class="mr-3 h-4 w-4"></i>
                        Analytics
                    </a>
                </div>

                <div class="border-t border-border pt-4">
                    <div class="px-3 py-2">
                        <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Settings</h3>
                    </div>
                    <div class="space-y-1">
                        <a href="#" 
                           class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                            <i data-lucide="settings" class="mr-3 h-4 w-4"></i>
                            General
                        </a>
                        <a href="#" 
                           class="group flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors text-muted-foreground hover:bg-accent hover:text-accent-foreground">
                            <i data-lucide="bell" class="mr-3 h-4 w-4"></i>
                            Notifications
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Section -->
            <div class="border-t border-border p-4">
                @auth
                    <div class="flex items-center space-x-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-sm font-medium">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-1 rounded-md hover:bg-accent">
                                <i data-lucide="more-vertical" class="h-4 w-4 text-muted-foreground"></i>
                            </button>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 bottom-full mb-2 w-48 bg-popover border border-border rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                                    <i data-lucide="user" class="mr-2 h-4 w-4"></i>
                                    Profile
                                </a>
                                <a href="#" class="flex items-center px-3 py-2 text-sm text-foreground hover:bg-accent">
                                    <i data-lucide="settings" class="mr-2 h-4 w-4"></i>
                                    Settings
                                </a>
                                <div class="border-t border-border my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-foreground hover:bg-accent">
                                        <i data-lucide="log-out" class="mr-2 h-4 w-4"></i>
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="flex items-center justify-center w-full px-3 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-md hover:bg-primary/90 transition-colors">
                        <i data-lucide="log-in" class="mr-2 h-4 w-4"></i>
                        Sign In
                    </a>
                @endauth
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-transition flex-1 flex flex-col lg:ml-0" :class="sidebarOpen ? 'lg:ml-0' : 'lg:ml-0'">
            <!-- Top Header -->
            <header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 px-6">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden">
                        <i data-lucide="menu" class="h-5 w-5 text-muted-foreground"></i>
                    </button>
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">@yield('header', 'Feature Requests')</h2>
                        <p class="text-sm text-muted-foreground">@yield('subheader', 'Manage and track feature requests')</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative hidden md:block">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"></i>
                            <input type="text" 
                                   placeholder="Search requests..." 
                                   class="w-80 pl-10 pr-4 py-2 bg-background border border-input rounded-md text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-muted-foreground hover:text-foreground transition-colors">
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        <span class="absolute -top-1 -right-1 h-3 w-3 bg-destructive rounded-full"></span>
                    </button>

                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-2">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-secondary text-secondary-foreground hover:bg-secondary/80 h-9 px-3">
                            <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                            Filter
                        </button>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3">
                            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                            New Request
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4" role="alert">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mr-3"></i>
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4" role="alert">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-600 mr-3"></i>
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             x-cloak
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Auto-hide flash messages
            const flashMessages = document.querySelectorAll('[role="alert"]');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    message.style.transition = 'all 0.5s ease-out';
                    message.style.transform = 'translateY(-100%)';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.remove();
                    }, 500);
                }, 5000);
            });

            // Search functionality
            const searchInput = document.querySelector('input[placeholder="Search requests..."]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.trim();
                        if (searchTerm) {
                            window.location.href = '{{ route("feature-requests.index") }}?search=' + encodeURIComponent(searchTerm);
                        }
                    }
                });
            }

            // Handle sidebar state on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    document.querySelector('[x-data]').__x.$data.sidebarOpen = true;
                }
            });
        });
    </script>
</body>
</html>