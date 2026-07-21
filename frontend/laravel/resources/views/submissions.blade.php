<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submissions - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">My Submissions</h1>
                <p class="text-emerald-200 mt-1">Submit achievements and earn points</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- New Submission Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">New Submission</h2>
            <form method="POST" action="{{ route('submissions.create') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Title</label>
                        <input type="text" name="title" required placeholder="e.g. Capstone Project"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Submission Type</label>
                        <select name="submissionType" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-colors">
                            <option value="project">Project</option>
                            <option value="certificate">Certificate</option>
                            <option value="competition">Competition</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3" required placeholder="Describe your achievement..."
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition-colors"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Evidence File</label>
                    <input type="file" name="evidence_file" accept=".pdf,.jpg,.png,.jpeg,.doc,.docx"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors cursor-pointer" />
                </div>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-emerald-700 hover:to-teal-700 transition-all duration-200">
                    Submit for Review
                </button>
            </form>
        </div>

        <!-- Submission History -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">Submission History</h2>
                <span class="text-xs text-slate-400">{{ count($submissions ?? []) }} total</span>
            </div>
            <div class="space-y-3">
                @forelse ($submissions as $item)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-emerald-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
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
                                        $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-600';
                                    @endphp
                                    <span class="rounded-full {{ $color }} px-2.5 py-0.5 text-xs font-medium">{{ ucfirst($status) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $item['description'] ?? '' }}</p>
                                <div class="mt-1.5 flex items-center gap-3 text-xs text-slate-400">
                                    <span>{{ $item['submissionType'] ?? 'project' }}</span>
                                    @if (!empty($item['createdAt']))
                                        <span>{{ \Carbon\Carbon::parse($item['createdAt'])->diffForHumans() }}</span>
                                    @endif
                                    @if (!empty($item['points']) && $status === 'approved')
                                        <span class="text-amber-600 font-semibold">+{{ $item['points'] }} pts</span>
                                    @endif
                                </div>
                            </div>
                            @if (!empty($item['evidence']))
                                <a href="{{ $item['evidence'] }}" target="_blank" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-colors shrink-0">
                                    Evidence
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No submissions yet. Submit your first achievement above!</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
