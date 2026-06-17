# CMU migrations

## Active migrations (`2026_05_22_*`)

Fresh schema for local / new installs — attendance, patrons, staff, and Laravel system tables.

| File | Tables |
|------|--------|
| `000001` | `users` |
| `000002` | `password_reset_tokens` |
| `000003` | `sessions` |
| `000004` | `cache`, `cache_locks` |
| `000005` | `jobs`, `job_batches`, `failed_jobs` |
| `000006` | `roles` |
| `000007` | `students` |
| `000008` | `pending_students` |
| `000009` | `employees` |
| `000010` | `pending_employees` |
| `000011` | `attendance_logs` (includes `section`, `IN`/`OUT`) |
| `000012` | `attendance_feedback` |
| `000013` | `settings` |
| `000014` | `students.normalized_name` |
| `000015` | `programs` (course dropdowns for patrons) |
| `000016` | `program_years`, `program_courses` (prospectus manager) |
| `000018` | Drops `student_id`, uses `district_id` as school ID; aligns `pending_students` |
| `000019` | Renames `id_number` → `district_id`, adds `barcode` on students / pending_students |

**Student IDs:** `district_id` = district ID (e.g. `2024-00001`). `barcode` = legacy ID barcode (older cards). `qrcode` = internal scan code (`S-00000001`). Attendance accepts either `district_id` or `barcode`. `attendance_logs.student_id` is the internal row FK to `students.id`.

## Retired migrations

Older `2025_*` / `2026_02_*` files are in `_retired/`. Do not run them on a new database.

## Local setup

```bash
# Create database (MySQL)
# CREATE DATABASE cmu_local;

cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan db:seed --class=AdminUserSeeder
php artisan serve
```

Use `migrate:fresh` only on a **new** or **throwaway** database — it drops all tables.

## Notes

- `attendance_logs.status` uses `IN` / `OUT` (matches `AttendanceController`).
- Library tables (books, ebooks, rooms, etc.) are **not** in this set; add later if needed.
- `Setting` model expects the `settings` table (`scan_sms`, etc.).
