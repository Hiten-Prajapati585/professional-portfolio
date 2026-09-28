# Professional Portfolio — PHP + MySQL Dynamic Admin CMS

This version keeps the original **Professional Portfolio** visual design from the first project, but changes the data layer to **PHP + MySQL** so one online portfolio is shared by every visitor.

## Main behavior

- Public visitors can only view the portfolio.
- Admin uses `/admin/login.php` to sign in.
- Admin can update personal information, education, skills, work experience, projects, screenshots, project PDFs, resume, social links and Canva URL without editing source code.
- Changes are saved in MySQL and immediately appear on the public website.
- Uploaded files are stored on the server under `uploads/`.
- Project cards and sections are generated dynamically from database records.
- Bootstrap 5 + the original portfolio CSS design are used for responsive desktop/tablet/mobile UI.

## Requirements

- PHP 8.1+ recommended
- MySQL 5.7+ / MariaDB 10.4+
- PHP extensions: mysqli, fileinfo, session
- HTTPS strongly recommended for live hosting

## Local setup with XAMPP

1. Install XAMPP and start Apache + MySQL.
2. Copy the `professional-portfolio` folder to `htdocs`.
3. Open phpMyAdmin.
4. Create/import the database using `database.sql`.
5. Open `includes/config.php` and set:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '');
```

6. Visit:

`http://localhost/portfolio-php/`

7. Admin login:

`http://localhost/portfolio-php/admin/login.php`

Default credentials from the SQL seed:

- Email: `admin@example.com`
- Password: `portfolio123`

**Change the password and email immediately after the first login.**

## Live hosting

Use a hosting provider that supports PHP + MySQL (shared hosting/cPanel, VPS, etc.).

1. Create a MySQL database and database user.
2. Import `database.sql` using phpMyAdmin.
3. Upload all project files to `public_html` (or your chosen web root).
4. Edit `includes/config.php` with the hosting database host, database name, username and password.
5. Make sure these directories are writable by PHP:
   - `uploads/profile/`
   - `uploads/projects/`
   - `uploads/resume/`
6. Visit `/admin/login.php` and sign in.
7. Replace the default profile data and add your real projects.

## Upload limits

The application validates MIME types and file sizes in PHP. Current application limits are:

- Profile/project images: 5 MB
- PDFs/resume: 10 MB

Your hosting PHP settings (`upload_max_filesize`, `post_max_size`) must also allow the files.

## Security included

- PHP sessions for admin authentication
- `password_hash()` / `password_verify()`
- CSRF tokens for POST actions
- Prepared MySQL statements for user-controlled database writes
- Server-side MIME validation with `finfo`
- Random server-side filenames for uploads
- Protected upload directory against PHP script execution
- Public site has no write forms
- Admin pages redirect to login when unauthenticated
- Admin logout and password/email change

## Important production recommendations

- Use HTTPS.
- Change the seeded admin credentials immediately.
- Use a strong unique admin password.
- Keep regular database and `uploads/` backups.
- Keep PHP and the hosting server updated.
- Do not put database credentials into public frontend JavaScript.

## Public/admin URLs after hosting

Public:

`https://your-domain.com/`

Admin:

`https://your-domain.com/admin/login.php`


