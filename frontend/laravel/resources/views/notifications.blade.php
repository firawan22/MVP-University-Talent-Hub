<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Notifications</h1>
                    <p class="text-rose-200 mt-1">Stay updated on your activities</p>
                </div>
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="rounded-xl bg-white/20 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white hover:bg-white/30 transition-all duration-200">
                        Mark All Read
                    </button>
                </form>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            @php
                $unreadCount = collect($notifications ?? [])->where('isRead', false)->count();
            @endphp
            @if ($unreadCount > 0)
                <div class="flex items-center gap-2 mb-6 text-sm text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    {{ $unreadCount }} unread
                </div>
            @endif
            <div class="space-y-2">
                @forelse ($notifications as $notif)
                    <div class="rounded-xl border p-4 transition-all duration-200 {{ !$notif['isRead'] ? 'border-rose-200 bg-rose-50/50' : 'border-slate-200 hover:border-slate-300 hover:shadow-sm' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold {{ !$notif['isRead'] ? 'text-slate-800' : 'text-slate-600' }}">{{ $notif['title'] }}</p>
                                    @if (!$notif['isRead'])
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    @endif
                                </div>
                                @if (!empty($notif['message']))
                                    <p class="mt-1 text-sm {{ !$notif['isRead'] ? 'text-slate-600' : 'text-slate-500' }}">{{ $notif['message'] }}</p>
                                @endif
                                <p class="mt-1 text-xs text-slate-400">{{ \Carbon\Carbon::parse($notif['createdAt'] ?? now())->diffForHumans() }}</p>
                            </div>
                            @if (!$notif['isRead'])
                                <form method="POST" action="{{ route('notifications.read', ['id' => $notif['id']]) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors shrink-0">Mark read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">All clear! No notifications yet.</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
