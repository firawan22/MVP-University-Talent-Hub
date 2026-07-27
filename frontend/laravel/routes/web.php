<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('login');
});

Route::post('/login', function (Request $request) {
    try {
        $response = Http::post(config('services.api_base_url') . '/auth/login', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if ($response->successful() && isset($response->json()['token'])) {
            // persist token and user in session
            session(['token' => $response->json()['token'], 'user' => $response->json()['user']]);
            return redirect('/dashboard');
        }

        $json = $response->json();
        $status = $response->status();
        $errorMsg = is_array($json['message'] ?? null) 
            ? implode(', ', $json['message']) 
            : ($json['message'] ?? $json['error'] ?? ($response->body() ? "API Error ($status): " . substr(strip_tags($response->body()), 0, 100) : "Login failed ($status)."));
        return back()->withErrors(['login' => $errorMsg])->withInput();
    } catch (\Throwable $e) {
        $apiHost = config('services.api_base_url');
        return back()->withErrors(['login' => "Backend API server ($apiHost) is unreachable: " . $e->getMessage()])->withInput();
    }
})->name('login.submit');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', function (Request $request) {
    try {
        $response = Http::post(config('services.api_base_url') . '/auth/register', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => 'student',
        ]);

        if ($response->successful() && isset($response->json()['token'])) {
            session(['token' => $response->json()['token'], 'user' => $response->json()['user']]);
            return redirect('/dashboard');
        }

        $json = $response->json();
        $status = $response->status();
        $errorMsg = is_array($json['message'] ?? null) 
            ? implode(', ', $json['message']) 
            : ($json['message'] ?? $json['error'] ?? ($response->body() ? "API Error ($status): " . substr(strip_tags($response->body()), 0, 100) : "Registration failed ($status)."));
        return back()->withErrors(['register' => $errorMsg])->withInput();
    } catch (\Throwable $e) {
        $apiHost = config('services.api_base_url');
        return back()->withErrors(['register' => "Backend API server ($apiHost) is unreachable: " . $e->getMessage()])->withInput();
    }
})->name('register.submit');

Route::match(['get', 'post'], '/logout', function (Request $request) {
    $request->session()->flush();
    return redirect('/');
})->name('logout');

Route::get('/dashboard', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    $stats = null;
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/dashboard');
        if ($resp->successful()) {
            $stats = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('dashboard', ['user' => $user, 'stats' => $stats, 'unreadCount' => $unreadCount]);
});

Route::get('/profile', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');

    // Admin tidak punya fitur profile
    if (!$token || ($user['role'] ?? '') === 'admin') {
        return redirect('/dashboard');
    }

    $profile = null;
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/me/profile');
        if ($resp->successful()) {
            $profile = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('profile', ['user' => $user, 'profile' => $profile, 'unreadCount' => $unreadCount]);
})->name('profile');

Route::get('/submissions', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');

    // Admin tidak punya fitur submissions
    if (!$token || ($user['role'] ?? '') === 'admin') {
        return redirect('/dashboard');
    }

    $items = [];
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/submissions');
        if ($resp->successful()) {
            $items = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('submissions', ['user' => $user, 'submissions' => $items, 'unreadCount' => $unreadCount]);
})->name('submissions');

Route::post('/submissions', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') === 'admin') {
        return redirect('/dashboard');
    }

    // Upload evidence file if present
    $evidence = '';
    if ($request->hasFile('evidence_file')) {
        $file = $request->file('evidence_file');
        $uploadResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post(config('services.api_base_url') . '/upload/file');
        if ($uploadResp->successful()) {
            $evidence = $uploadResp->json()['url'];
        }
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->post(config('services.api_base_url') . '/submissions', [
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'evidence' => $evidence,
        'submissionType' => $request->input('submissionType', 'project'),
    ]);

    if ($response->successful()) {
        return redirect('/submissions')->with('success', 'Submission sent for review.');
    }

    return back()->withErrors(['submission' => 'Unable to submit.'])->withInput();
})->name('submissions.create');

Route::get('/admin/reviews', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    $items = [];
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/submissions');
        if ($resp->successful()) {
            $items = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('admin-reviews', ['user' => $user, 'submissions' => $items, 'unreadCount' => $unreadCount]);
})->name('admin.reviews');

Route::get('/rewards', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    $rewards = [];
    $leaderboard = [];
    $unreadCount = 0;

    if ($token) {
        $rewardsResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/rewards');
        $leaderboardResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/leaderboard');
        if ($rewardsResp->successful()) {
            $rewards = $rewardsResp->json();
        }
        if ($leaderboardResp->successful()) {
            $leaderboard = $leaderboardResp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('rewards', ['user' => $user, 'rewards' => $rewards, 'leaderboard' => $leaderboard, 'unreadCount' => $unreadCount]);
})->name('rewards');

Route::post('/rewards/{id}/redeem', function (Request $request, $id) {
    $token = $request->session()->get('token');
    if (!$token) {
        return redirect('/')->withErrors(['login' => 'Please log in first.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->post(config('services.api_base_url') . '/rewards/' . $id . '/redeem');

    if ($response->successful()) {
        return redirect('/rewards')->with('success', 'Reward redeemed.');
    }

    return back()->withErrors(['reward' => 'Unable to redeem reward.']);
})->name('rewards.redeem');

Route::post('/admin/reviews/{id}', function (Request $request, $id) {
    $token = $request->session()->get('token');
    if (!$token) {
        return redirect('/')->withErrors(['login' => 'Please log in first.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->patch(config('services.api_base_url') . '/submissions/' . $id . '?decision=' . $request->input('decision'));

    if ($response->successful()) {
        return redirect('/admin/reviews')->with('success', 'Review updated.');
    }

    return back()->withErrors(['review' => 'Unable to update review.']);
})->name('admin.reviews.update');

Route::post('/profile', function (Request $request) {
    $token = $request->session()->get('token');
    if (!$token) {
        return redirect('/')->withErrors(['login' => 'Please log in first.']);
    }

    // Upload certificate files if present
    $certificates = $request->input('certificates', '');
    if ($request->hasFile('certificate_files')) {
        $uploaded = [];
        foreach ($request->file('certificate_files') as $file) {
            $uploadResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.api_base_url') . '/upload/file');
            if ($uploadResp->successful()) {
                $uploaded[] = $uploadResp->json()['url'];
            }
        }
        if (!empty($uploaded)) {
            $certificates = implode(',', $uploaded);
        }
    }

    // Upload portfolio files if present
    $portfolios = $request->input('portfolios', '');
    if ($request->hasFile('portfolio_files')) {
        $uploaded = [];
        foreach ($request->file('portfolio_files') as $file) {
            $uploadResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.api_base_url') . '/upload/file');
            if ($uploadResp->successful()) {
                $uploaded[] = $uploadResp->json()['url'];
            }
        }
        if (!empty($uploaded)) {
            $portfolios = implode(',', $uploaded);
        }
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->put(config('services.api_base_url') . '/me/profile', [
        'name' => $request->input('name'),
        'major' => $request->input('major'),
        'bio' => $request->input('bio'),
        'skills' => $request->input('skills'),
        'certificates' => $certificates,
        'portfolios' => $portfolios,
    ]);

    if ($response->successful()) {
        return redirect('/profile')->with('success', 'Profile updated successfully.');
    }

    return back()->withErrors(['profile' => 'Unable to update profile.'])->withInput();
})->name('profile.update');

// Search Students (admin only — per PRD)
Route::get('/students/search', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/dashboard');
    }
    $results = [];
    $query = $request->input('q', '');
    $unreadCount = 0;

    if ($query) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get(config('services.api_base_url') . '/students/search?q=' . urlencode($query));
        if ($resp->successful()) {
            $results = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('search-students', ['user' => $user, 'results' => $results, 'query' => $query, 'unreadCount' => $unreadCount]);
})->name('students.search');

// Opportunities (admin only — per PRD)
Route::get('/opportunities', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/dashboard');
    }
    $opportunities = [];
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/opportunities');
        if ($resp->successful()) {
            $opportunities = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('opportunities', ['user' => $user, 'opportunities' => $opportunities, 'unreadCount' => $unreadCount]);
})->name('opportunities');

Route::post('/opportunities', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') return redirect('/dashboard');

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->post(config('services.api_base_url') . '/opportunities', [
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'company' => $request->input('company'),
        'location' => $request->input('location'),
        'type' => $request->input('type'),
    ]);

    if ($response->successful()) {
        return redirect('/opportunities')->with('success', 'Opportunity posted successfully.');
    }
    return back()->withErrors(['opportunity' => 'Unable to create opportunity.'])->withInput();
})->name('opportunities.create');

Route::post('/opportunities/{id}/delete', function (Request $request, $id) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') return redirect('/dashboard');

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->delete(config('services.api_base_url') . '/opportunities/' . $id);
    if ($response->successful()) {
        return redirect('/opportunities')->with('success', 'Opportunity deleted.');
    }
    return back()->withErrors(['opportunity' => 'Unable to delete opportunity.']);
})->name('opportunities.delete');

Route::post('/opportunities/{id}/update', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->put(config('services.api_base_url') . '/opportunities/' . $id, [
        'title' => $request->input('title'),
        'description' => $request->input('description'),
        'company' => $request->input('company'),
        'location' => $request->input('location'),
        'type' => $request->input('type'),
    ]);

    if ($response->successful()) {
        return redirect('/opportunities')->with('success', 'Opportunity updated.');
    }
    return back()->withErrors(['opportunity' => 'Unable to update opportunity.']);
})->name('opportunities.update');

// Notifications (admin only)
Route::get('/notifications', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/dashboard');
    }
    $notifications = [];
    $unreadCount = 0;

    if ($token) {
        $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications');
        if ($resp->successful()) {
            $notifications = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('notifications', ['user' => $user, 'notifications' => $notifications, 'unreadCount' => $unreadCount]);
})->name('notifications');

Route::post('/notifications/{id}/read', function (Request $request, $id) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') return redirect('/dashboard');

    Http::withHeaders(['Authorization' => 'Bearer ' . $token])->patch(config('services.api_base_url') . '/notifications/' . $id . '/read');
    return redirect('/notifications');
})->name('notifications.read');

Route::post('/notifications/read-all', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') !== 'admin') return redirect('/dashboard');

    Http::withHeaders(['Authorization' => 'Bearer ' . $token])->patch(config('services.api_base_url') . '/notifications/read-all');
    return redirect('/notifications');
})->name('notifications.read-all');

// Recommendations (student only)
Route::get('/recommendations', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    if (!$token || ($user['role'] ?? '') === 'admin') {
        return redirect('/dashboard');
    }
    $opportunities = [];
    $recommendedSkills = [];
    $unreadCount = 0;

    if ($token) {
        $oppResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get(config('services.api_base_url') . '/recommendations/opportunities');
        $skillResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get(config('services.api_base_url') . '/recommendations/skills');
        if ($oppResp->successful()) $opportunities = $oppResp->json();
        if ($skillResp->successful()) $recommendedSkills = $skillResp->json();
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('recommendations', ['user' => $user, 'opportunities' => $opportunities, 'recommendedSkills' => $recommendedSkills, 'unreadCount' => $unreadCount]);
})->name('recommendations');

// Admin — Student List
Route::get('/admin/students', function (Request $request) {
    $user = $request->session()->get('user');
    $token = $request->session()->get('token');
    $students = [];
    $query = $request->input('q', '');
    $unreadCount = 0;

    if ($token && ($user['role'] ?? '') === 'admin') {
        if ($query) {
            $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->get(config('services.api_base_url') . '/students/search?q=' . urlencode($query));
        } else {
            $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->get(config('services.api_base_url') . '/students');
        }
        if ($resp->successful()) {
            $students = $resp->json();
        }
        $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
        if ($notifResp->successful()) {
            $unreadCount = $notifResp->json();
        }
    }

    return view('admin-students', ['user' => $user, 'students' => $students, 'query' => $query, 'unreadCount' => $unreadCount]);
})->name('admin.students');

// Admin — Reward Management
Route::post('/admin/rewards/create', function (Request $request) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->post(config('services.api_base_url') . '/rewards', [
        'name' => $request->input('name'),
        'description' => $request->input('description'),
        'pointsRequired' => (int) $request->input('pointsRequired'),
    ]);

    if ($response->successful()) {
        return redirect('/rewards')->with('success', 'Reward created successfully.');
    }
    return back()->withErrors(['reward' => 'Unable to create reward.']);
})->name('admin.rewards.create');

Route::post('/admin/rewards/{id}/update', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->put(config('services.api_base_url') . '/rewards/' . $id, [
        'name' => $request->input('name'),
        'description' => $request->input('description'),
        'pointsRequired' => (int) $request->input('pointsRequired'),
    ]);

    if ($response->successful()) {
        return redirect('/rewards')->with('success', 'Reward updated successfully.');
    }
    return back()->withErrors(['reward' => 'Unable to update reward.']);
})->name('admin.rewards.update');

Route::post('/admin/rewards/{id}/delete', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->delete(config('services.api_base_url') . '/rewards/' . $id);

    if ($response->successful()) {
        return redirect('/rewards')->with('success', 'Reward deleted successfully.');
    }
    return back()->withErrors(['reward' => 'Unable to delete reward.']);
})->name('admin.rewards.delete');

// Admin — Student Management
Route::get('/admin/students/{id}/view', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $resp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/students/' . $id);
    if (!$resp->successful()) {
        return redirect('/admin/students')->withErrors(['student' => 'Student not found.']);
    }

    $notifResp = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('services.api_base_url') . '/notifications/unread-count');
    $unreadCount = $notifResp->successful() ? $notifResp->json() : 0;

    return view('admin-students', ['user' => $user, 'students' => [$resp->json()], 'query' => '', 'unreadCount' => $unreadCount, 'viewStudent' => $resp->json()]);
})->name('admin.students.view');

Route::post('/admin/students/{id}/update', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->put(config('services.api_base_url') . '/students/' . $id, [
        'name' => $request->input('name'),
        'major' => $request->input('major'),
        'skills' => $request->input('skills', ''),
    ]);

    if ($response->successful()) {
        return redirect('/admin/students')->with('success', 'Student updated successfully.');
    }
    return back()->withErrors(['student' => 'Unable to update student.']);
})->name('admin.students.update');

Route::post('/admin/students/{id}/delete', function (Request $request, $id) {
    $token = $request->session()->get('token');
    $user = $request->session()->get('user');
    if (!$token || ($user['role'] ?? '') !== 'admin') {
        return redirect('/')->withErrors(['login' => 'Unauthorized.']);
    }

    $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->delete(config('services.api_base_url') . '/students/' . $id);

    if ($response->successful()) {
        return redirect('/admin/students')->with('success', 'Student deleted successfully.');
    }
    return back()->withErrors(['student' => 'Unable to delete student.']);
})->name('admin.students.delete');
