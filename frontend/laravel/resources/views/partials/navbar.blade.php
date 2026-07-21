<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="/dashboard" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <span class="font-bold text-slate-800 text-lg">Talent<span class="text-indigo-600">Hub</span></span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-1">
                @php
                    $isAdmin = !empty($user) && ($user['role'] ?? '') === 'admin';
                    $current = request()->route()?->getName() ?? '';
                @endphp

                @if ($isAdmin)
                    <!-- Admin Nav -->
                    <a href="/dashboard" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ $current === '' || str_contains($current, 'dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.students') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ str_contains($current, 'admin.students') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                        Students
                    </a>
                    <a href="{{ route('admin.reviews') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ str_contains($current, 'admin.reviews') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                        Reviews
                    </a>
                    <a href="{{ route('rewards') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ str_contains($current, 'rewards') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                        Rewards
                    </a>
                    <a href="{{ route('opportunities') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ str_contains($current, 'opportunities') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                        Opportunities
                    </a>
                @else
                    <!-- Student Nav -->
                    @php
                        $navItems = [
                            ['route' => 'profile', 'label' => 'Profile'],
                            ['route' => 'submissions', 'label' => 'Submissions'],
                            ['route' => 'rewards', 'label' => 'Rewards'],
                            ['route' => 'recommendations', 'label' => 'AI Recs'],
                        ];
                    @endphp
                    @foreach ($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ str_contains($current, $item['route']) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-800' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span class="text-sm font-semibold text-amber-800">{{ $user['points'] ?? 0 }}</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center">
                        <span class="text-xs font-bold text-white">{{ substr($user['name'] ?? 'U', 0, 1) }}</span>
                    </div>
                    <span class="text-sm font-medium text-indigo-700 hidden sm:inline">{{ $user['name'] ?? 'User' }}</span>
                </div>

                <!-- Mobile menu toggle -->
                <details class="md:hidden relative">
                    <summary class="flex items-center gap-1 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer list-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </summary>
                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 overflow-hidden">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800">{{ $user['name'] ?? 'User' }}</p>
                            <p class="text-xs text-slate-500">{{ $user['email'] ?? '' }} • {{ $user['role'] ?? '' }}</p>
                        </div>
                        <div class="py-1">
                            @if ($isAdmin)
                                <a href="/dashboard" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Dashboard</a>
                                <a href="{{ route('admin.students') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Students</a>
                                <a href="{{ route('admin.reviews') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Reviews</a>
                                <a href="{{ route('rewards') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Rewards</a>
                                <a href="{{ route('opportunities') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Opportunities</a>
                            @else
                                @foreach ($navItems as $item)
                                    <a href="{{ route($item['route']) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ $item['label'] }}</a>
                                @endforeach
                            @endif
                        </div>
                        <div class="border-t border-slate-100 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-medium">Logout</button>
                            </form>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </div>
</nav>
