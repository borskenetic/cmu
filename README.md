# CMU Library Attendance System

Laravel application for library **attendance scanning**, patron (student) records, employee records, ID cards, attendance logs, and admin tools.

Repository: [github.com/borskenetic/cmu](https://github.com/borskenetic/cmu)

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ (or MariaDB)
- GD extension (QR codes on ID cards and student edit preview)
- Optional: Imagick (not required)

## Quick start (new database)

```bash
git clone https://github.com/borskenetic/cmu.git
cd cmu

composer install
cp .env.example .env
php artisan key:generate
```

Create an empty MySQL database, then set `.env`:

```env
DB_DATABASE=cmu_local
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and optional sample data:

```bash
php artisan migrate:fresh --seed
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Default seeded data

- Sample **programs**, **5 students**, **5 employees** (see `database/seeders/`)
- Roles: `student`, `faculty` (for employee records)

Create your first **login user** (not seeded by default):

```bash
php artisan db:seed --class=AdminUserSeeder
```

Default (change after first login): `admin@library.local` / `password`

Or use **Admin → Create Account** after creating one user manually in tinker.

## Student identifiers

| Field | Purpose |
|-------|---------|
| `district_id` | District ID on newer cards (e.g. `23-3617`) |
| `barcode` | Legacy patron barcode on older cards |
| `qrcode` | Internal ID card code (`S-00000001`) |

Attendance scanning accepts either **district ID** or **barcode**.

Admins can bulk-fill barcodes on existing students via **Students → Import → Update Barcodes** (upload spreadsheet with `Barcode - Patron` and `District ID` columns).

## Migrations

Active migrations live in `database/migrations/` as `2026_05_22_000001` … `000019`.

Older library/book migrations are kept in `database/migrations/_retired/` and are **not** run by Laravel (subfolder is ignored).

| Step | Command |
|------|---------|
| Fresh install | `php artisan migrate` |
| Reset local DB | `php artisan migrate:fresh` (drops all tables) |
| With samples | `php artisan migrate:fresh --seed` |

See [database/migrations/README.md](database/migrations/README.md) for the table list.

**Verified:** `php artisan migrate:fresh --seed` completes successfully on a clean database.

## Main routes

| URL | Description |
|-----|-------------|
| `/` | Public home / FAQ |
| `/attendance` | Kiosk scanner |
| `/login` | Staff / admin login |
| `/students` | Student list |
| `/employees` | Employee list |
| `/attendance-logs` | Attendance logs & reports |
| `/view-users` | User accounts (admin) |

## Branding

Edit colors and fonts in `public/branding/branding.css` or set `BRANDING_CSS` in `.env`.

## ID card assets

Place templates under `images/id_templates/` (e.g. `front.png`, `back.png`, `back_employee.png`). These paths are gitignored; copy from your design files after clone.

## Optional SMS modem

Set in `.env`:

```env
SMS_MODEM_URL=http://127.0.0.1:5000/send-sms
SMS_MODEM_API_KEY=your-secret-api-key
```

## Push / deploy notes

- Never commit `.env`
- Run `composer install --no-dev` on production
- Set `APP_DEBUG=false`, `APP_ENV=production`
- After deploy: `php artisan migrate --force`, then `php artisan config:cache` and `php artisan route:cache`
- If routes change locally, run `php artisan route:clear` before caching again

## License

MIT (Laravel framework components retain their respective licenses.)
