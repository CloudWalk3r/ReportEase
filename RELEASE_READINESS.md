# ReportEase — Release Readiness

_Last updated: 2026-10-27_

This document captures the current state of the repository, the standardization
changes applied in this pass, remaining gaps, and a prioritized punch list to
reach public-release quality.

---

## 1. Current State

ReportEase is a Laravel 8 web application for university issue tracking with
role-based access control (RBAC), image management (Intervention Image), and
issue status workflow. The repository already has a complete Laravel structure
(`artisan`, `composer.json`/`.lock`, `package.json`, `config/`, `database/`,
`routes/`, `resources/`, `tests/`, `phpunit.xml`, `.env.example`,
`.editorconfig`, `.styleci.yml`, `.gitignore`, `LICENSE`, `README.md`).

**Build/run verification:** PHP and Composer are **not installed** on the
machine used for this standardization pass, so `composer install`,
`php artisan`, and `phpunit` could **not** be executed here. Node.js (v25) and
npm (v11) are available. All code-level issues below were found by static
review. The newly added GitHub Actions workflow will exercise
`composer install`, `key:generate`, `php artisan test`, and `pint --test` on
every push/PR.

---

## 2. Changes Made (this pass)

All changes are **uncommitted** (no `git add`/`commit`/`push` performed).

### Documentation
- **`README.md`**
  - Removed leftover default-Laravel boilerplate appended at the end (Laravel
    Sponsors, duplicate Contributing/Code of Conduct/Security/License sections).
  - Fixed a malformed/unclosed code block in the installation steps and added
    the missing `npm install` / `npm run dev` front-end step.
  - Corrected the clone URL (was `WAD-Project`) and the "max images per issue"
    value (was `1`, code allows `3`).
  - Corrected the seeded role IDs to match `RoleSeeder` (Admin=1, Faculty=2,
    Maintenance=3, Student=4).
  - Added **Tech Stack**, **Screenshots** (placeholders), **Environment
    Variables**, **Testing**, and **Deployment** sections, and an author
    attribution line.
- **`CONTRIBUTING.md`** — added (short contribution guide: setup, workflow,
  Pint/PHPUnit checks, bug reports, PRs).
- **`RELEASE_READINESS.md`** — this file.

### Configuration / Tooling
- **`pint.json`** — added Laravel Pint config (laravel preset) so `pint --test`
  has a defined ruleset. `.styleci.yml` is kept as-is.
- **`.github/workflows/ci.yml`** — added CI pipeline (PHP 7.4/8.0/8.1 matrix):
  copy `.env`, `composer install`, `key:generate`, `php artisan test` against a
  SQLite database, and `pint --test`.
- **`.env.example`** — set `APP_NAME=ReportEase`, `APP_URL=http://localhost:8000`,
  and `DB_DATABASE=ReportEase` (were generic Laravel defaults). No secrets are
  present; `.env` remains git-ignored.

### Bug / Code Fixes
- **`app/Http/Controllers/ReportController.php`** — previously referenced a
  non-existent `App\Models\Report` and non-existent views
  (`previous-report.*`). Any hit to `/reports` or `/report/{id}` would throw a
  fatal "class not found" error. Rewritten to read from the existing `Issue`
  model and render existing `shared.*` views.
- **`database/factories/UserFactory.php`** — previously used columns that do not
  exist on the `Users` table (`name`, `email_verified_at`) and omitted required
  NOT NULL columns (`full_name`, `role_id`, `phone_number`, `ID`). This would
  break any factory-based test. Rewritten to match the actual schema.
- **`app/Http/Controllers/AuthController.php`** — removed a debug
  `\Log::info('User login debug', ...)` statement that logged user/role details
  on every login.

---

## 3. Remaining Gaps & Risks

### 🔴 High — Security
1. **Privilege escalation at registration.** `AuthController::RegisterCustom`
   validates `role_id` only with `exists:Roles,role_id`, and the registration
   form (`resources/views/auth/register.blade.php`) lists **every** role
   including **Admin**. Any visitor can self-register as an Admin. Restrict
   self-registration to non-privileged roles (e.g. Student only) and move
   Admin/Faculty/Maintenance provisioning behind an admin-approval flow.
   _(Left unchanged to avoid altering intended UX silently — must be addressed
   before public release.)_
2. **No authorization middleware / policies.** Access control relies on
   `session('user_role')` checks scattered in controllers. There is no route
   middleware enforcing roles and no Laravel Policies/Gates. Users could reach
   dashboards/actions of other roles by URL if session role checks are missing
   (e.g. dashboard routes do not verify the role matches the dashboard). Add
   role middleware and/or policies.
3. **Dev/test leftovers exposed.** `/test-register` route renders a
   `test-registration` view that does not exist (fatal error) and should be
   removed for production. The `/all-pages` index links to `/reset-password-dev`
   which is not a real route.

### 🟠 Medium — Correctness / Consistency
4. **Seeders use MySQL-specific `INSERT IGNORE`.** `RoleSeeder` and
   `SectionSeeder` will fail on SQLite/Postgres. Use `updateOrInsert`/Eloquent
   `firstOrCreate` for portability (also needed if CI seeds the DB).
5. **Password length inconsistency.** Registration/login require `min:6`;
   password reset requires `min:8`. Standardize (recommend `min:8`).
6. **New-issue status vs. documented workflow.** `IssueController::store` sets
   status to `Under Review`, but the documented workflow starts at `Pending`.
   Align the code or the docs.
7. **`phone_number` stored as INTEGER.** Leading zeros are lost and longer/
   international numbers can overflow. Store as `string`.
8. **Broad `$request->all()` in registration** passed into `create()`. The
   `create()` method maps fields explicitly (so it is not a raw mass-assignment
   hole today), but it is fragile — prefer `$request->validated()` and explicit
   field mapping.

### 🟡 Low — Polish
9. **No real tests.** Only the default `ExampleTest` (Unit + Feature) stubs
   exist. Add feature tests for auth, RBAC, and issue CRUD.
10. **Thumbnail path nesting.** `IssueController::store` builds
    `evidence/thumbnails/...` (nested) — harmless but worth tidying.
11. **No `SECURITY.md` / `CHANGELOG.md` / issue & PR templates.**
12. **Screenshots are placeholders** — add real images under
    `docs/screenshots/`.

---

## 4. Prioritized Punch List (to public-release quality)

1. **[P0]** Lock down registration roles (Student-only self-serve) + add role
   middleware/policies for all protected routes and dashboards.
2. **[P0]** Remove dev-only routes/views (`/test-register`, `reset-password-dev`
   link) and ensure `APP_DEBUG=false` for production.
3. **[P1]** Make seeders DB-agnostic (`firstOrCreate`/`updateOrInsert`).
4. **[P1]** Verify the build end-to-end on a machine with PHP/Composer:
   `composer install`, `php artisan migrate --seed`, `php artisan test`,
   `pint --test`, `npm run prod`.
5. **[P1]** Standardize password rules; change `phone_number` to string.
6. **[P2]** Add real feature tests (auth, RBAC, issue lifecycle) and raise
   coverage.
7. **[P2]** Add `SECURITY.md`, `CHANGELOG.md`, GitHub issue/PR templates, and
   real screenshots.
8. **[P3]** Reconcile issue-status workflow between code and docs.
