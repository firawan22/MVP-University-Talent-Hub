<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opportunities - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-blue-700 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Opportunities</h1>
                <p class="text-sky-200 mt-1">Internships, jobs, scholarships, and more</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($user && ($user['role'] ?? '') === 'admin')
            <!-- Create Opportunity Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
                <h2 class="text-lg font-semibold text-slate-800 mb-6">Create New Opportunity</h2>
                <form method="POST" action="{{ route('opportunities.create') }}" class="space-y-5">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Title</label>
                            <input type="text" name="title" required placeholder="e.g. Summer Internship 2025"
                                class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Company</label>
                            <input type="text" name="company" placeholder="e.g. Google, Startup XYZ"
                                class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                            <input type="text" name="location" placeholder="e.g. Remote, Jakarta"
                                class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                            <select name="type" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors">
                                <option value="internship">Internship</option>
                                <option value="job">Job</option>
                                <option value="volunteer">Volunteer</option>
                                <option value="scholarship">Scholarship</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" rows="4" required placeholder="Describe the opportunity in detail..."
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"></textarea>
                    </div>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-sky-700 hover:to-blue-700 transition-all duration-200">
                        Post Opportunity
                    </button>
                </form>
            </div>
        @endif

        <!-- Opportunities List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">All Opportunities</h2>
                <span class="text-xs text-slate-400">{{ count($opportunities ?? []) }} available</span>
            </div>
            <div class="space-y-4">
                @forelse ($opportunities as $opp)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-sky-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="font-semibold text-slate-800 text-lg">{{ $opp['title'] }}</p>
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
                                <div class="mt-1 flex items-center gap-3 text-sm text-slate-500">
                                    @if (!empty($opp['company']))
                                        <span>{{ $opp['company'] }}</span>
                                    @endif
                                    @if (!empty($opp['location']))
                                        <span>{{ $opp['location'] }}</span>
                                    @endif
                                </div>
                                <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ Str::limit($opp['description'], 300) }}</p>
                                @if (!empty($opp['createdAt']))
                                    <p class="mt-2 text-xs text-slate-400">Posted {{ \Carbon\Carbon::parse($opp['createdAt'])->diffForHumans() }}</p>
                                @endif
                            </div>
                            @if ($user && ($user['role'] ?? '') === 'admin')
                                <div class="flex items-center gap-2 shrink-0">
                                    <button onclick="editOpportunity({{ $opp['id'] }}, '{{ addslashes($opp['title']) }}', '{{ addslashes($opp['description'] ?? '') }}', '{{ addslashes($opp['company'] ?? '') }}', '{{ addslashes($opp['location'] ?? '') }}', '{{ $opp['type'] ?? 'internship' }}')"
                                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Edit</button>
                                    <form method="POST" action="{{ route('opportunities.delete', ['id' => $opp['id']]) }}" onsubmit="return confirm('Delete this opportunity?')">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 transition-colors">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">No opportunities available yet.</p>
                @endforelse
            </div>
        </div>
    </main>

    @if ($user && ($user['role'] ?? '') === 'admin')
    <!-- Edit Opportunity Modal -->
    <div id="editOppModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden p-6">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">Edit Opportunity</h3>
                <button onclick="closeEditOpp()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>
            <form id="editOppForm" method="POST" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Title</label>
                        <input type="text" name="title" id="editOppTitle" required
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Company</label>
                        <input type="text" name="company" id="editOppCompany"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                        <input type="text" name="location" id="editOppLocation"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                        <select name="type" id="editOppType" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors">
                            <option value="internship">Internship</option>
                            <option value="job">Job</option>
                            <option value="volunteer">Volunteer</option>
                            <option value="scholarship">Scholarship</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" id="editOppDesc" rows="4" required
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditOpp()" class="flex-1 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-sky-700 hover:to-blue-700 transition-all duration-200">Save</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function editOpportunity(id, title, desc, company, location, type) {
        document.getElementById('editOppTitle').value = title;
        document.getElementById('editOppCompany').value = company;
        document.getElementById('editOppLocation').value = location;
        document.getElementById('editOppDesc').value = desc;
        document.getElementById('editOppType').value = type;
        document.getElementById('editOppForm').action = '/opportunities/' + id + '/update';
        document.getElementById('editOppModal').classList.remove('hidden');
    }
    function closeEditOpp() {
        document.getElementById('editOppModal').classList.add('hidden');
    }
    document.getElementById('editOppModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditOpp();
    });
    </script>
    @endif
</body>
</html>
