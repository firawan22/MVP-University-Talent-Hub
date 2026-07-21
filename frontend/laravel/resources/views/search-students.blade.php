<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Students - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Search Students</h1>
                <p class="text-slate-300 mt-1">Find peers by name, major, or skills</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <form method="GET" action="{{ route('students.search') }}" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, major, or skill..."
                    class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                <button type="submit" class="rounded-xl bg-gradient-to-r from-slate-600 to-slate-800 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-slate-700 hover:to-slate-900 transition-all duration-200">
                    Search
                </button>
            </form>
        </div>

        <!-- Results -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">Results</h2>
                @if ($query)
                    <span class="text-xs text-slate-400">Showing results for "{{ $query }}"</span>
                @endif
            </div>
            <div class="space-y-3">
                @forelse ($results as $student)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-indigo-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white font-bold text-lg shrink-0">
                                    {{ substr($student['name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $student['name'] }}</p>
                                    <p class="text-sm text-slate-500">{{ $student['major'] ?? 'No major specified' }}</p>
                                    @if (!empty($student['skills']))
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach ((array) $student['skills'] as $skill)
                                                <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-lg font-bold text-amber-600">{{ $student['points'] ?? 0 }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Points</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">{{ $query ? 'No students found matching "' . $query . '"' : 'Enter a search term to find students.' }}</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
