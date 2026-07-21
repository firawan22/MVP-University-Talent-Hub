<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - University Talent Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-8 md:p-12 mb-8">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-violet-300/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white">Welcome back, {{ $user['name'] ?? 'User' }}!</h1>
                        <p class="mt-2 text-indigo-200 text-lg">{{ ($user['role'] ?? '') === 'admin' ? 'Manage your talent ecosystem' : 'Build your professional profile' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-5 py-3 border border-white/10">
                            <p class="text-xs text-indigo-200 font-medium uppercase tracking-wider">Points</p>
                            <p class="text-2xl font-bold text-white">{{ $user['points'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-5 py-3 border border-white/10">
                            <p class="text-xs text-indigo-200 font-medium uppercase tracking-wider">Role</p>
                            <p class="text-2xl font-bold text-white capitalize">{{ $user['role'] ?? 'student' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md hover:border-indigo-200 transition-all duration-200">
                <p class="text-sm font-medium text-slate-500">Total Students</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['totalStudents'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <p class="text-sm font-medium text-slate-500">Total Skills</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['totalSkills'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md hover:border-amber-200 transition-all duration-200">
                <p class="text-sm font-medium text-slate-500">Total Projects</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['totalProjects'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-md hover:border-rose-200 transition-all duration-200">
                <p class="text-sm font-medium text-slate-500">Pending Reviews</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['pendingReviews'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Feature Grid -->
        @php $isAdmin = !empty($user) && ($user['role'] ?? '') === 'admin'; @endphp

        @if ($isAdmin)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <a href="{{ route('admin.students') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-indigo-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">Students</h3>
                <p class="mt-1 text-sm text-slate-500">View and manage all registered students.</p>
            </a>
            <a href="{{ route('admin.reviews') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-rose-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-rose-700 transition-colors">Reviews</h3>
                <p class="mt-1 text-sm text-slate-500">Review submissions, approve/reject, and award points.</p>
                @if (($stats['pendingReviews'] ?? 0) > 0)
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        {{ $stats['pendingReviews'] }} pending
                    </div>
                @endif
            </a>
            <a href="{{ route('rewards') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-amber-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-amber-700 transition-colors">Rewards</h3>
                <p class="mt-1 text-sm text-slate-500">Manage rewards and leaderboard.</p>
            </a>
            <a href="{{ route('opportunities') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-sky-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-sky-700 transition-colors">Opportunities</h3>
                <p class="mt-1 text-sm text-slate-500">Post and manage internships, jobs, and more.</p>
            </a>
            <a href="{{ route('students.search') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-slate-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-slate-700 transition-colors">Search</h3>
                <p class="mt-1 text-sm text-slate-500">Find students by name, major, or skills.</p>
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <a href="{{ route('profile') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-indigo-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">My Profile</h3>
                <p class="mt-1 text-sm text-slate-500">Manage your skills, certificates, and portfolio.</p>
            </a>
            <a href="{{ route('submissions') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-emerald-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors">Submissions</h3>
                <p class="mt-1 text-sm text-slate-500">Submit achievements for verification and earn points.</p>
            </a>
            <a href="{{ route('rewards') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-amber-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-amber-700 transition-colors">Rewards & Leaderboard</h3>
                <p class="mt-1 text-sm text-slate-500">Redeem points and see the rankings.</p>
            </a>
            <a href="{{ route('recommendations') }}" class="group bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 hover:shadow-lg hover:border-violet-300 hover:-translate-y-0.5 transition-all duration-200">
                <h3 class="text-lg font-semibold text-slate-800 group-hover:text-violet-700 transition-colors">AI Recommendations</h3>
                <p class="mt-1 text-sm text-slate-500">Personalized opportunities and skill suggestions.</p>
            </a>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
            <h3 class="text-lg font-semibold text-slate-800">Quick Actions</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                @if ($isAdmin)
                    <a href="{{ route('admin.reviews') }}" class="rounded-xl border border-slate-200 p-4 hover:border-rose-300 hover:bg-rose-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-rose-700">Review Submissions</span>
                    </a>
                    <a href="{{ route('opportunities') }}" class="rounded-xl border border-slate-200 p-4 hover:border-sky-300 hover:bg-sky-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-sky-700">+ New Opportunity</span>
                    </a>
                    <a href="{{ route('rewards') }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:bg-amber-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-amber-700">Manage Rewards</span>
                    </a>
                @else
                    <a href="{{ route('submissions') }}" class="rounded-xl border border-slate-200 p-4 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-indigo-700">+ New Submission</span>
                    </a>
                    <a href="{{ route('rewards') }}" class="rounded-xl border border-slate-200 p-4 hover:border-amber-300 hover:bg-amber-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-amber-700">View Leaderboard</span>
                    </a>
                    <a href="{{ route('recommendations') }}" class="rounded-xl border border-slate-200 p-4 hover:border-violet-300 hover:bg-violet-50/50 transition-all duration-200">
                        <span class="text-sm font-medium text-slate-700 hover:text-violet-700">AI Recommendations</span>
                    </a>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
