<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.navbar')

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 p-6 md:p-8">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold text-white">My Profile</h1>
                <p class="text-indigo-200 mt-1">Manage your skills, certificates, and portfolio</p>
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

        <!-- Profile Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 md:p-8">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Edit Profile</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $profile['name'] ?? '') }}" required
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Major / Study Program</label>
                        <input type="text" name="major" value="{{ old('major', $profile['major'] ?? '') }}" required
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors" />
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bio</label>
                    <textarea name="bio" rows="4" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors">{{ old('bio', $profile['bio'] ?? '') }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Skills</label>
                    <input type="text" name="skills" value="{{ old('skills', is_array($profile['skills'] ?? null) ? implode(', ', $profile['skills']) : ($profile['skills'] ?? '')) }}"
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors"
                        placeholder="e.g. JavaScript, Python, UI Design" />
                    <p class="mt-1.5 text-xs text-slate-400">Separate skills with commas</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Certificates</label>
                        <input type="file" name="certificate_files[]" multiple accept=".pdf,.jpg,.png,.jpeg"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors cursor-pointer" />
                        @if (!empty($profile['certificates']) && is_array($profile['certificates']))
                            <div class="mt-2 text-xs text-slate-500 space-y-1">
                                @foreach ($profile['certificates'] as $cert)
                                    <span class="block truncate">{{ $cert }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Portfolios</label>
                        <input type="file" name="portfolio_files[]" multiple accept=".pdf,.jpg,.png,.jpeg,.zip,.doc,.docx"
                            class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 transition-colors cursor-pointer" />
                        @if (!empty($profile['portfolios']) && is_array($profile['portfolios']))
                            <div class="mt-2 text-xs text-slate-500 space-y-1">
                                @foreach ($profile['portfolios'] as $port)
                                    <span class="block truncate">{{ $port }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700 transition-all duration-200">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
