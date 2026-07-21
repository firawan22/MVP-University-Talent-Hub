<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">Students</h1>
                <p class="text-slate-300 mt-1">Manage and search all registered students</p>
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

        <!-- Search -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <form method="GET" action="{{ route('admin.students') }}" class="flex gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, major, or skill..."
                    class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700 transition-all duration-200">
                    Search
                </button>
            </form>
        </div>

        <!-- Students List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-slate-800">All Students</h2>
                <span class="text-xs text-slate-400">{{ count($students) }} student{{ count($students) !== 1 ? 's' : '' }}</span>
            </div>
            <div class="space-y-3">
                @forelse ($students as $student)
                    <div class="rounded-xl border border-slate-200 p-5 hover:border-indigo-200 hover:shadow-sm transition-all duration-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-white font-bold text-lg shrink-0">
                                    {{ substr($student['name'] ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $student['name'] ?? 'Unknown' }}</p>
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
                            <div class="flex items-center gap-3 shrink-0">
                                @if (!empty($student['certificates']) && count($student['certificates']) > 0)
                                    <div class="text-center">
                                        <p class="text-lg font-bold text-slate-700">{{ count($student['certificates']) }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Certs</p>
                                    </div>
                                @endif
                                <div class="text-center">
                                    <p class="text-lg font-bold text-amber-600">{{ $student['points'] ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Points</p>
                                </div>
                                <button onclick="openEditModal({{ $student['id'] }}, '{{ addslashes($student['name'] ?? '') }}', '{{ addslashes($student['major'] ?? '') }}', '{{ addslashes(is_array($student['skills'] ?? null) ? implode(',', $student['skills']) : ($student['skills'] ?? '')) }}')"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.students.delete', ['id' => $student['id']]) }}" onsubmit="return confirm('Delete this student? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">{{ $query ? 'No students found matching "' . $query . '"' : 'No students registered yet.' }}</p>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Edit Student Modal -->
    <div id="editStudentModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden p-6">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">Edit Student</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>
            <form id="editStudentForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                    <input type="text" name="name" id="editStudentName" required
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Major</label>
                    <input type="text" name="major" id="editStudentMajor"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Skills</label>
                    <input type="text" name="skills" id="editStudentSkills" placeholder="Comma separated"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                    <p class="mt-1 text-xs text-slate-400">Separate skills with commas</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700 transition-all duration-200">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(id, name, major, skills) {
        document.getElementById('editStudentName').value = name;
        document.getElementById('editStudentMajor').value = major;
        document.getElementById('editStudentSkills').value = skills;
        document.getElementById('editStudentForm').action = '/admin/students/' + id + '/update';
        document.getElementById('editStudentModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editStudentModal').classList.add('hidden');
    }
    document.getElementById('editStudentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
    </script>
</body>
</html>
