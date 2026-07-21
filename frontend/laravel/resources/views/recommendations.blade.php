<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Recommendations - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-purple-800 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">AI Recommendations</h1>
                <p class="text-violet-200 mt-1">Personalized opportunities and skill suggestions</p>
            </div>
        </div>

        <!-- Recommended Opportunities -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-1">Recommended Opportunities</h2>
            <p class="text-sm text-slate-500 mb-6">Matched to your skills and interests</p>
            <div class="space-y-3">
                @forelse ($opportunities as $opp)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-violet-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-800">{{ $opp['title'] }}</p>
                                    @if (!empty($opp['type']))
                                        @php
                                            $typeColors = [
                                                'internship' => 'bg-blue-100 text-blue-700',
                                                'job' => 'bg-emerald-100 text-emerald-700',
                                                'volunteer' => 'bg-violet-100 text-violet-700',
                                                'scholarship' => 'bg-amber-100 text-amber-700',
                                            ];
                                        @endphp
                                        <span class="rounded-full {{ $typeColors[$opp['type']] ?? 'bg-slate-100 text-slate-600' }} px-2.5 py-0.5 text-xs font-medium">{{ ucfirst($opp['type']) }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-500">
                                    @if (!empty($opp['company'])) {{ $opp['company'] }} @endif
                                    @if (!empty($opp['location'])) &middot; {{ $opp['location'] }} @endif
                                </p>
                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ Str::limit($opp['description'], 250) }}</p>
                            </div>
                            @if (($opp['relevanceScore'] ?? 0) > 0)
                                <div class="shrink-0 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 border border-emerald-200 flex flex-col items-center justify-center">
                                        <span class="text-lg font-bold text-emerald-600">{{ $opp['relevanceScore'] }}%</span>
                                        <span class="text-[10px] font-medium text-emerald-500 -mt-0.5">Match</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">{{ empty($user) ? 'Log in to see recommendations.' : 'No matching opportunities found. Add more skills to your profile!' }}</p>
                @endforelse
            </div>
        </div>

        <!-- Recommended Skills -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-1">Skills You Might Want to Learn</h2>
            <p class="text-sm text-slate-500 mb-6">Based on peers with similar profiles</p>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($recommendedSkills as $skill)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-amber-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-800 capitalize">{{ $skill['skill'] }}</p>
                                <p class="text-xs text-slate-400">{{ $skill['peerCount'] }} peer{{ $skill['peerCount'] !== 1 ? 's' : '' }} have this skill</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">Recommended</span>
                        </div>
                    </div>
                @empty
                    <p class="md:col-span-2 text-slate-500 text-center py-8">Add skills to your profile to get personalized recommendations.</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
