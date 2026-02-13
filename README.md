# Secret Valentine Confession (PHP + MySQL)

Anonymous text confession website with private admin inbox.

## 1) Setup Database
1. For XAMPP local: you can import `schema.sql` once in phpMyAdmin.
2. For first local run, app can auto-create `valentine_confession` + `messages` + `feedback_comments` if MySQL user has permission.
3. If you still deploy to hosting later, update `config.php` with your host DB credentials.

## 2) Configure App
Edit `config.php`:
- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `ADMIN_USERNAME`
- `ADMIN_PASSWORD_HASH`

Default local values are already set for XAMPP:
- `DB_HOST = 127.0.0.1`
- `DB_USER = root`
- `DB_PASS = ''`
- `DB_NAME = valentine_confession`
- `ADMIN_USERNAME = willy`
- `ADMIN_PASSWORD = willy29` (via `ADMIN_PASSWORD_HASH`)

Generate a password hash locally:

```bash
php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT), PHP_EOL;"
```

Paste the output into `ADMIN_PASSWORD_HASH`.

## 3) Upload Files
Upload all files to your hosting root (for this project directory).

## 4) Optional Music
Place your MP3 file at:

`folder-audio/romantic.mp3`

## Public/Admin Routes
- Public landing: `index.php`
- Confession form: `send.php`
- Feedback submit endpoint: `submit-feedback.php`
- Hidden admin login: `login.php` (or `admin-login.php`)
- Admin dashboard: `dashboard.php`

## Included Tables
- `messages` for confessions
- `feedback_comments` for user feedback (improvements/feature updates)

## Security Notes
- Uses `password_hash` + `password_verify`
- Uses prepared statements
- Uses session auth + CSRF tokens
- Includes basic submit rate limit
- Auto deletes old messages and feedback entries older than configured days
