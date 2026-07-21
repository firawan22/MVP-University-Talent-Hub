<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TalentHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-violet-50 flex items-center justify-center p-6">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-indigo-100/50 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-violet-100/50 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <span class="text-white font-bold text-lg">T</span>
                </div>
                <span class="font-bold text-slate-800 text-xl">Talent<span class="text-indigo-600">Hub</span></span>
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/60 p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Welcome Back</h1>
                <p class="text-slate-500 mt-1">Sign in to continue to TalentHub</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="post" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors"
                        placeholder="you@example.com" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-colors"
                        placeholder="••••••••" />
                </div>
                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-semibold text-white shadow-md hover:from-indigo-700 hover:to-violet-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-slate-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Create one</a>
                </p>
            </div>
        </div>

        <p class="text-center mt-6 text-sm text-slate-400">
            &copy; {{ date('Y') }} TalentHub. All rights reserved.
        </p>
    </div>
</body>
</html>
