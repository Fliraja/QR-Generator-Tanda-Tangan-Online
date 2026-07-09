# Changelog

## Fase 0 — Setup (2026-07-08)

### Added
- `endroid/qr-code` package (v6.0.9) for QR generation
- `laravel/breeze` (v2.4.2) with Blade stack + Alpine.js for auth scaffolding
- Migration `create_signers_table`: `name`, `position`, `is_active`
- Migration `create_qr_generations_table`: `uuid`, `signer_id`, `letter_number`, `generated_by`, `ip_address`
- Model `Signer` with `$fillable` + `generations()` relationship
- Model `QrGeneration` with `$fillable` + `signer()` / `generator()` relationships
- Seeder `UserSeeder`: 1 admin account (NIP: 198501012010011001, password: `password`)
- Seeder `SignerSeeder`: 2 dummy signers (dr. Budi Santoso, dr. Siti Aminah)

### Modified
- Migration `create_users_table`: added `nip` (unique), `role` enum (staff/admin), `email` now nullable
- Model `User`: `$fillable` includes `nip`/`role`, `username()` returns `nip` for NIP-based login
- Request `LoginRequest`: validation + auth attempt uses `nip` instead of `email`
- View `auth/login.blade.php`: NIP field replaces Email field
- Factory `UserFactory`: added `nip` + `role` defaults
- Seeder `DatabaseSeeder`: calls `UserSeeder` and `SignerSeeder`

### Database
- MySQL database `tte` configured
- All migrations ran successfully (5 tables)
- Admin login via NIP verified working

## Fase 1 — Auth & Role (2026-07-08)

### Added
- Migration `add_is_active_to_users_table`: `is_active` boolean column
- Middleware `EnsureUserIsAdmin`: checks `role === admin`, 403 if not
- Middleware alias `admin` registered in `bootstrap/app.php`
- Controller `UserManagementController`: index, create, store, edit, update, destroy (nonaktifkan)
- Views: `admin/users/index.blade.php`, `create.blade.php`, `edit.blade.php`
- Route group `/admin/users` with `auth` + `admin` middleware
- 2 dummy staff accounts (Staf TU 1, Staf TU 2) in UserSeeder

### Modified
- `User` model: `$fillable` includes `is_active`, factory defaults to true
- `LoginRequest::authenticate()`: rejects login if `is_active === false` with message "Akun ini sudah dinonaktifkan."
- `LoginRequest` throttle error message: field changed from `email` to `nip`
- `UserFactory`: added `is_active` default

### Changed
- Branch: `fase-1-auth-role`

## Fase 2 — Core Generate & Download QR (2026-07-08)

### Added
- Controller `QrGenerationController`: `create()` (form), `store()` (generate + download PNG)
- Route `GET /` (auth) → qr.create, named `qr.create`
- Route `POST /qr/generate` (auth) → qr.generate
- View `resources/views/qr/create.blade.php` with dropdown, optional letter number
- SignerFactory + HasFactory trait on Signer model
- `SignerFactory` for test data generation
- Feature test `QrGenerationTest` (8 tests, all pass)

### Modified
- Routes `web.php`: `/` inside auth group points to QrGenerationController
- `AuthenticatedSessionController::store()`: redirect to `route('qr.create')` instead of dashboard

### Test Results (all pass)
- Guest → redirect to login
- Form shows only active signers
- Generate returns PNG with correct headers
- Validasi: signer required, inactive signer rejected
- Letter number saved to DB
- Without auth redirects to login
- Multiple generations have unique UUIDs (no collision)

## Fase 3 — Halaman Verifikasi Publik (2026-07-08)

### Added
- Controller `VerificationController::show()`: cari by uuid, return view
- Route `GET /verify/{uuid}` publik (di luar auth middleware)
- View `verify/show.blade.php`: tampilkan nama, jabatan, nomor surat (opsional), waktu generate
- View `verify/not-found.blade.php`: pesan "Kode Tidak Ditemukan"
- Factory `QrGenerationFactory` + HasFactory trait on QrGeneration model
- Feature test `VerificationTest` (6 tests, all pass)

### Security
- Halaman publik tidak menampilkan `generated_by` atau `ip_address` (data internal tetap privat)

### Test Results (all pass)
- Valid UUID → tampil nama + jabatan signer
- Nomor surat tampil jika diisi, tidak tampil jika null
- UUID invalid → halaman "Kode Tidak Ditemukan"
- Bisa diakses tanpa login (publik)
- Data internal tidak bocor ke publik
