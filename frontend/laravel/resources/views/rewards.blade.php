<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards & Leaderboard - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-600 to-orange-700 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Rewards & Leaderboard</h1>
                <p class="text-amber-200 mt-1">Redeem rewards and climb the rankings</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- User Points Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg">
                        <span class="text-2xl font-bold text-white">{{ $user['points'] ?? 0 }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Your Balance</p>
                        <p class="text-xl font-bold text-slate-800">{{ $user['name'] ?? 'User' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Leaderboard</h2>
            <div class="space-y-2">
                @forelse ($leaderboard as $entry)
                    @php
                        $isMe = isset($user['name']) && $entry['name'] === $user['name'];
                        $medals = ['🥇', '🥈', '🥉'];
                        $rankColors = ['bg-amber-50 border-amber-200', 'bg-slate-50 border-slate-200', 'bg-orange-50 border-orange-200'];
                    @endphp
                    <div class="flex items-center justify-between rounded-xl border p-4 {{ $isMe ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : ($entry['rank'] <= 3 ? $rankColors[$entry['rank'] - 1] : 'border-slate-200 hover:border-slate-300') }} transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $entry['rank'] <= 3 ? 'text-lg' : 'text-sm bg-slate-100 text-slate-600' }}">
                                @if ($entry['rank'] <= 3)
                                    <span>{{ $medals[$entry['rank'] - 1] }}</span>
                                @else
                                    #{{ $entry['rank'] }}
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold {{ $isMe ? 'text-indigo-700' : 'text-slate-800' }}">{{ $entry['name'] }} {{ $isMe ? '(You)' : '' }}</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold {{ $isMe ? 'text-indigo-700' : 'text-amber-600' }}">{{ $entry['points'] }}</span>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No rankings yet. Start submitting achievements to earn points!</p>
                @endforelse
            </div>
        </div>

        <!-- Reward Catalog -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">Reward Catalog</h2>
                <span class="text-xs text-slate-400">{{ count($rewards) }} reward{{ count($rewards) !== 1 ? 's' : '' }}</span>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($rewards as $reward)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-amber-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-slate-800">{{ $reward['name'] }}</p>
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">{{ $reward['pointsRequired'] }} pts</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $reward['description'] ?? 'No description' }}</p>
                                @if (($user['points'] ?? 0) >= $reward['pointsRequired'])
                                    <p class="mt-2 text-xs text-emerald-600 font-medium">You can redeem this!</p>
                                @else
                                    <p class="mt-2 text-xs text-slate-400">{{ number_format($reward['pointsRequired'] - ($user['points'] ?? 0)) }} more points needed</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if (!empty($user) && ($user['role'] ?? '') === 'admin')
                                    <button onclick="editReward({{ $reward['id'] }}, '{{ addslashes($reward['name']) }}', '{{ addslashes($reward['description'] ?? '') }}', {{ $reward['pointsRequired'] }})"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.rewards.delete', ['id' => $reward['id']]) }}" onsubmit="return confirm('Delete this reward?')">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">Delete</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('rewards.redeem', ['id' => $reward['id']]) }}">
                                    @csrf
                                    <button type="submit" {{ ($user['points'] ?? 0) < $reward['pointsRequired'] ? 'disabled' : '' }}
                                        class="rounded-xl {{ ($user['points'] ?? 0) >= $reward['pointsRequired'] ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-600 shadow-sm' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }} px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200">
                                        Redeem
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="md:col-span-2 text-slate-500 text-center py-8">No rewards available yet.</p>
                @endforelse
            </div>
        </div>

        @if (!empty($user) && ($user['role'] ?? '') === 'admin')
        <!-- Admin: Manage Rewards -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Manage Rewards</h2>
            <p class="text-sm text-slate-500 mb-6">Create a new reward or edit existing ones above.</p>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.rewards.create') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                        <input type="text" name="name" required placeholder="e.g. Voucher Belanja"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Points Required</label>
                        <input type="number" name="pointsRequired" required min="1" placeholder="e.g. 100"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-amber-600 hover:to-orange-700 transition-all duration-200">
                            Add Reward
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="2" required placeholder="Describe the reward..."
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors"></textarea>
                </div>
            </form>
        </div>

        <!-- Edit Reward Modal (hidden by default) -->
        <div id="editRewardModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden p-6">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-800">Edit Reward</h3>
                    <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
                </div>
                <form id="editRewardForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                        <input type="text" name="name" id="editRewardName" required
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Points Required</label>
                        <input type="number" name="pointsRequired" id="editRewardPoints" required min="1"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" id="editRewardDesc" rows="3" required
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditModal()" class="flex-1 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                        <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-amber-600 hover:to-orange-700 transition-all duration-200">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function editReward(id, name, description, pointsRequired) {
            document.getElementById('editRewardName').value = name;
            document.getElementById('editRewardDesc').value = description;
            document.getElementById('editRewardPoints').value = pointsRequired;
            document.getElementById('editRewardForm').action = '/admin/rewards/' + id + '/update';
            document.getElementById('editRewardModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editRewardModal').classList.add('hidden');
        }
        // Close modal on backdrop click
        document.getElementById('editRewardModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        </script>
        @endif
    </main>
</body>
</html>
