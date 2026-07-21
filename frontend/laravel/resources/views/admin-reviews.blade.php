<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reviews - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-pink-700 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Admin Reviews</h1>
                <p class="text-rose-200 mt-1">Review and verify student submissions</p>
                @php
                    $pendingCount = collect($submissions ?? [])->where('status', 'pending')->count();
                @endphp
                @if ($pendingCount > 0)
                    <div class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-white/20 backdrop-blur-sm px-3.5 py-1.5 text-sm font-medium text-white">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        {{ $pendingCount }} pending
                    </div>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Approved</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ collect($submissions ?? [])->where('status', 'approved')->count() }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-5">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Rejected</p>
                <p class="mt-1 text-2xl font-bold text-rose-600">{{ collect($submissions ?? [])->where('status', 'rejected')->count() }}</p>
            </div>
        </div>

        <!-- Submissions List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">All Submissions</h2>
            <div class="space-y-4">
                @forelse ($submissions as $item)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-slate-300 hover:shadow-sm transition-all duration-200">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-800">{{ $item['title'] ?? 'Untitled' }}</p>
                                    @php
                                        $status = $item['status'] ?? 'pending';
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                        ];
                                    @endphp
                                    <span class="rounded-full {{ $statusColors[$status] ?? 'bg-slate-100 text-slate-600' }} px-2.5 py-0.5 text-xs font-medium">{{ ucfirst($status) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $item['description'] ?? '' }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                    @if (!empty($item['user']))
                                        <span>{{ is_array($item['user']) ? ($item['user']['name'] ?? 'Unknown') : (is_object($item['user']) ? $item['user']->name : 'Unknown') }}</span>
                                    @endif
                                    <span>{{ $item['submissionType'] ?? 'project' }}</span>
                                    @if (!empty($item['createdAt']))
                                        <span>{{ \Carbon\Carbon::parse($item['createdAt'])->diffForHumans() }}</span>
                                    @endif
                                    @if (!empty($item['points']) && $status === 'approved')
                                        <span class="text-amber-600 font-semibold">+{{ $item['points'] }} pts</span>
                                    @endif
                                </div>
                                @if (!empty($item['evidence']))
                                    <a href="{{ $item['evidence'] }}" target="_blank" class="mt-2 text-xs text-sky-600 hover:text-sky-700 font-medium">View Evidence</a>
                                @endif
                            </div>
                            @if ($status === 'pending')
                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('admin.reviews.update', ['id' => $item['id']]) }}">
                                        @csrf
                                        <input type="hidden" name="decision" value="approved" />
                                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.update', ['id' => $item['id']]) }}">
                                        @csrf
                                        <input type="hidden" name="decision" value="rejected" />
                                        <button type="submit" class="rounded-xl border border-rose-300 bg-white px-4 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 transition-colors">Reject</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No submissions to review.</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
