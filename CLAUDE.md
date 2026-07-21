# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**University Talent Hub** — A gamified student talent ecosystem MVP. Students build profiles with skills/certificates/portfolios, submit them for admin verification, earn points, and redeem rewards. A leaderboard tracks top performers.

Two independent applications communicate via HTTP:
- **Frontend** (`frontend/laravel/`): Laravel 12 + Blade + TailwindCSS 4 (CDN) + Vite. Session-based, proxies all API calls to NestJS backend. Artisan serves on port 8000.
- **Backend** (`backend/nestjs/`): NestJS 11 + TypeORM + MySQL. REST API on port 3001. JWT auth with role-based guards (`admin` / `student`).

### Data Flow
```
Browser → Laravel (Blade + Session) → HTTP POST/GET → NestJS API (JWT Auth) → MySQL
```

### Business Flow
1. Student registers → auto-creates `UserEntity` + `StudentEntity` (linked by matching auto-increment ID, no foreign key)
2. Student fills profile (name, major, skills, certificates, portfolios) via `PUT /me/profile`
3. Student creates a submission (`POST /submissions`) with title, description, evidence URL, submissionType
4. Admin reviews submission → approve (awards configurable points) or reject
5. Points accumulate on both `UserEntity` and `StudentEntity` (not always in sync — see Notable)
6. Leaderboard (`GET /leaderboard`) ranks students by `UserEntity.points`
7. Students redeem rewards using points (`POST /rewards/:id/redeem`)

## Architecture

### Backend — [`backend/nestjs/src/`](backend/nestjs/src/)

**Module structure:** `AppModule` is the global root (`@Global()`) module. It directly registers core controllers/services (App, Auth, Submissions, Students) and imports standalone feature modules (Opportunities, Notifications, Recommendations, PointConfigurations, Upload). All 7 entities are registered both in TypeORM `forRoot` and `forFeature` at the root level.

**Controller routing:**
- `app.controller.ts` — Mixed routes: health, dashboard stats (`GET /dashboard`), profile CRUD (`GET|PUT /me/profile`), rewards CRUD + redeem, leaderboard
- `auth/auth.controller.ts` — `POST /auth/login`, `POST /auth/register`
- `submissions/submissions.controller.ts` — `GET|POST /submissions`, `PATCH /submissions/:id?decision=approved|rejected`
- `students/students.controller.ts` — Admin-only CRUD at `GET|POST|PUT|DELETE /students/:id`, plus `GET /students/search?q=`
- `upload/upload.controller.ts` — `POST /upload/file` (multer, 10 MB limit, stores to `uploads/`)
- `opportunities/` — Admin CRUD for internship/job postings; `GET /opportunities` and `GET /opportunities/:id` accessible to all authenticated users
- `notifications/` — `GET /notifications`, `GET /notifications/unread-count`, `PATCH /notifications/:id/read`, `PATCH /notifications/read-all`
- `recommendations/` — `GET /recommendations/opportunities` (skill-match scoring), `GET /recommendations/skills` (peer-based skill suggestions)
- `point-configurations/` — Admin-only `GET /point-configurations`, `PUT /point-configurations/:id`

**Auth guard pipeline:** Two-step guard chain. `AuthGuard` verifies the Bearer token via `AppService.verifyToken()` (jsonwebtoken), attaches decoded user to `req.user`. `RolesGuard` reads the `@Roles()` decorator via the NestJS `Reflector` and checks against `req.user.role`. The `@User()` param decorator (in `auth/user.decorator.ts`) extracts `req.user` for controller methods. Password hashing uses `bcryptjs` via `auth/bcrypt.ts`.

**Key entities** ([backend/nestjs/src/entities/](backend/nestjs/src/entities/)):
- `UserEntity` — Login identity with email, name, role, points, passwordHash (points also on StudentEntity — see Notable)
- `StudentEntity` — Profile data (name, major, bio, skills, certificates, portfolios, points)
- `SubmissionEntity` — Verification workflow (studentId, title, description, evidence, submissionType, status: pending|approved|rejected, pointsAwarded)
- `RewardEntity` — Redeemable catalog items with name, pointsRequired, description
- `OpportunityEntity` — Job/internship postings with title, description, company, location, type, isActive, createdAt, updatedAt
- `NotificationEntity` — Per-user notifications with title, message, isRead, optional link, createdAt
- `PointConfigurationEntity` — Configurable point values keyed by unique `type` (project/certificate/competition/internship) with `points`

**Data flow for submissions/points:**
1. Student creates submission → stored as `pending`
2. Admin approves → `SubmissionsService` looks up `PointConfigurationEntity[submissionType]` for point value (default 50), then awards to both `UserEntity.points` and `StudentEntity.points`
3. `AppService.reviewSubmission()` duplicates some of this logic but does NOT notify on review

**Seed scripts:**
- `PointConfigurationsService.onModuleInit()` seeds 4 default point types (project: 50, certificate: 30, competition: 100, internship: 200) on first run
- `backend/nestjs/src/scripts/seed_rewards.js` — standalone Node.js script to seed rewards directly into MySQL (runs separately, not via NestJS)

### Frontend — [`frontend/laravel/`](frontend/laravel/)

**Routing:** All routes defined as inline closures in `routes/web.php` (no separate controllers). Each route proxies form requests to the NestJS API via Laravel's `Http` facade. API base URL is `config('services.api_base_url')` (reads `API_BASE_URL` env var, defaults to `http://localhost:3001`).

**Auth:** Session-based. JWT token and user object stored in session. Laravel delegates all auth to NestJS — no user table of its own. `SESSION_DRIVER=database` persists sessions in SQLite.

**Views** ([resources/views/](resources/views/)): Uses TailwindCSS 4 via CDN (`<script src="https://cdn.tailwindcss.com">`) — the `@tailwindcss/vite` plugin is configured but only the welcome page uses `@vite()`. Shared navbar in `partials/navbar.blade.php`. Key views:
- `login.blade.php` / `register.blade.php` — Auth pages (login is the landing page)
- `dashboard.blade.php` — Stats grid + role-based navigation cards
- `profile.blade.php` — Skill/certificate/portfolio management with file upload
- `submissions.blade.php` — Create and track submissions
- `admin-reviews.blade.php` — Admin review queue with approve/reject
- `rewards.blade.php` — Reward catalog + leaderboard (both students and admins)
- `opportunities.blade.php` — Admin opportunity CRUD
- `notifications.blade.php` — Admin notification inbox
- `recommendations.blade.php` — Opportunity + skill recommendations (student only)
- `search-students.blade.php` / `admin-students.blade.php` — Admin student search/management

## Commands

### Root (Monorepo)
```bash
npm run install:all          # Install all dependencies (backend npm + frontend npm + composer)
npm run setup                # Full frontend setup (.env, key, migrate, npm, build)
npm run dev                  # Run backend + frontend concurrently
npm run build                # Build both apps
npm run test                 # Run backend + frontend tests
```

### Docker
```bash
docker compose up -d         # Start all services (mysql, backend, frontend)
docker compose up -d --build # Rebuild and start
docker compose build         # Build all images
docker compose logs -f       # Follow logs
docker compose down          # Stop and remove containers
docker compose down -v       # Stop and remove volumes (wipes DB data)
# Docker volumes: mysql_data, uploads_data, laravel_storage
# Access: frontend at http://localhost:8000, backend at http://localhost:3001
```

### Backend (NestJS) — [`backend/nestjs/`](backend/nestjs/)
```bash
cd backend/nestjs
npm install                  # Install dependencies
npm run start:dev            # Watch mode (default port 3001)
npm run start:debug          # Debug + watch mode
npm run build                # Build to dist/
npm run start:prod           # node dist/main (production)
npm test                     # Run all Jest unit tests (*.spec.ts in src/)
npx jest --testPathPattern submissions  # Run a single test file by pattern
npm run test:watch           # Jest watch mode
npm run test:cov             # Coverage report
npm run test:e2e             # E2E tests (config: test/jest-e2e.json, spec: test/app.e2e-spec.ts)
npm run lint                 # ESLint
npm run format               # Prettier
# Seed rewards (standalone script):
node src/scripts/seed_rewards.js
```

### Frontend (Laravel) — [`frontend/laravel/`](frontend/laravel/)
```bash
cd frontend/laravel
composer install             # Install PHP dependencies
npm install                  # Install Node dependencies
composer run setup           # Full setup (.env, key, migrate, npm, build)
composer run dev             # Run: artisan serve + queue:listen + pail (logs) + vite (concurrently)
composer run dev:server      # Same without pail (used by root npm run dev:frontend)
npm run dev                  # Vite dev server only (HMR for Blade views)
npm run build                # Vite production build
composer run test            # PHPUnit (config: phpunit.xml, uses SQLite :memory:)
# PHP formatting (via Laravel Pint — included with Laravel 12):
./vendor/bin/pint            # Format PHP files
```

## Environment

### Backend ([backend/nestjs/.env](backend/nestjs/.env))
- `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME` — MySQL connection (default: 127.0.0.1:3306, root, talent_hub)
- `JWT_SECRET` — Token signing secret (default: `dev-secret`)
- `PORT` — API port (default: 3000; frontend calls port 3001)

### Frontend ([frontend/laravel/.env](frontend/laravel/.env))
- `DB_CONNECTION=sqlite` — Uses SQLite locally
- `SESSION_DRIVER=database` — Session-backed auth
- `API_BASE_URL=http://localhost:3001` — NestJS backend URL (Docker: `http://backend:3001`)

### Production notes
- TypeORM `synchronize: true` auto-creates/alters tables — dev only; disable in production

## Notable

### Architecture issues
- **`AppService` is a God service** — handles auth tokens, dashboard stats, profile CRUD, submissions, rewards, and leaderboard. The `submissions/`, `students/`, and `auth/` modules duplicate parts of this logic. New features should go into dedicated modules, not `AppService`.
- **No database-level foreign keys** — all relations (e.g. `submission.studentId`) are logical only. `StudentEntity` and `UserEntity` are linked by ID convention (matching auto-increment IDs), not by a TypeORM relation or FK constraint.
- **Frontend has no controllers** — all routing logic lives in `routes/web.php` closures. Consider extracting to controllers as the app grows.
- **Frontend `package.json` uses `"type": "module"`** — Vite config and any Node scripts must use ESM import/export syntax.

### Data integrity concerns
- **Points out of sync** — Points exist on both `UserEntity` and `StudentEntity`. `SubmissionsService.reviewSubmission()` updates both correctly. `redeemReward()` previously only deducted from `UserEntity` — **fixed** to update both.

### Dead code
- `AppService.authenticate()` — **removed** (was never called; `AuthService.login()` handles actual login with bcrypt verification).
