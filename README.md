# Tanka Prasad Adhikari — Portfolio Website

A dynamic, database-driven personal portfolio & CV website for Tanka Prasad Adhikari, Founder & CEO of Aakash Digital Pvt. Ltd. Built with vanilla PHP + MySQL, no frameworks required.

## Features

- **Dynamic content management** via admin panel — no code editing needed to update content
- **Dark / Light theme** toggle with localStorage persistence
- **Responsive sidebar layout** with smooth section navigation and scroll spy
- **Contact form** with honeypot spam protection + rate limiting (3 messages/minute)
- **Email notifications** for new contact form submissions
- **Schema.org structured data** for SEO (Person schema + social links)
- **Open Graph + Twitter Card** meta tags for social sharing
- **Admin panel** with CSRF-protected forms, bcrypt password hashing, and 30-min session timeout
- **Image upload** for awards, projects, news via admin panel

## Requirements

- PHP 7.4+ (PDO, mysqli)
- MySQL 5.7+ or MariaDB 10.3+
- Apache with `mod_rewrite` (optional — for clean URLs)

## Quick Setup

### 1. Upload files

Upload all files to your web host (e.g., cPanel public_html or htdocs).

### 2. Create database

Log into **cPanel → phpMyAdmin**, create a new database, then click the **Import** tab and upload `setup.sql`.

### 3. Configure credentials

Edit `config.php` and set your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

### 4. Set admin password

Edit `superadmin.php` and change the password:

```php
define('ADMIN_PASSWORD', 'YourSecurePasswordHere');
```

### 5. Configure site URL (optional)

In `config.php`, set `SITE_URL` if your site is not at the domain root:

```php
define('SITE_URL', 'https://www.yoursite.com');
```

### 6. Upload profile image

Upload a headshot to `img/avatar.jpg` (recommended: 400×400px square).

---

## Admin Panel

Access at: `https://yoursite.com/admin/`

| Section | Description |
|---|---|
| **Profile** | Name, title, bio, contact info, social links, CV file path |
| **Education** | Degree, institution, period, sort order |
| **Experience** | Company, role, period, description, accent color |
| **Training** | Certificate name, organizer, year, optional file |
| **Awards** | Title, organization, year, description, images |
| **Research** | Paper title, publication, year, description, URL |
| **News** | News headline, source, date, description, image |
| **Skills** | Skill name, category (Code/Software/Language), proficiency % |
| **Projects** | Project title, description, URL, images, tags |
| **Portfolio** | Screenshots with title and links for About section |
| **Services** | Icon (FontAwesome), name, description for About section |
| **Interests** | Icon + name for About section |
| **Messages** | View/read contact form submissions |
| **Upload** | Upload images (JPEG/PNG/WebP/GIF) and PDF files |
| **Password** | Change admin password securely (bcrypt hashing) |

### Setting Up Contact Email Notifications

1. Go to **Admin → Profile**
2. Scroll to **Admin Email for Contact Notifications**
3. Enter the email where you want to receive contact form submissions
4. Save profile

> **Note:** PHP `mail()` function must be enabled on your server for email notifications to work.

## Security Features

- **CSRF tokens** on all admin POST forms
- **Bcrypt/Argon2 password hashing** (auto-upgraded on first login)
- **Honeypot spam protection** on contact form
- **Rate limiting** — max 3 messages per minute
- **30-minute session timeout** (auto-logout)
- **Account lockout** after 5 failed login attempts (5-minute lockout)
- **SQL injection protection** via PDO prepared statements
- **XSS protection** via `htmlspecialchars()` output escaping

## File Structure

```
tankaadhikaricv/
├── index.php          # Main portfolio page (public)
├── contact.php        # Contact form API endpoint
├── 404.php            # Custom 404 error page
├── config.php         # Database credentials (DO NOT COMMIT)
├── superadmin.php     # Admin password (DO NOT COMMIT)
├── db.php             # Database connection & helper functions
├── setup.sql          # Full database schema + seed data
├── sitemap.xml        # SEO sitemap
├── robots.txt         # Search engine directives
├── admin/
│   ├── login.php      # Admin login page
│   ├── index.php      # Admin dashboard
│   ├── profile.php    # Edit profile
│   ├── auth.php       # Session & CSRF helpers
│   ├── upload.php     # Image/file upload
│   ├── changepassword.php
│   ├── messages.php
│   ├── education.php
│   ├── experience.php
│   ├── training.php
│   ├── awards.php
│   ├── research.php
│   ├── news.php
│   ├── skills.php
│   ├── projects.php
│   ├── portfolio_sites.php
│   ├── services_about.php
│   └── interests.php
├── img/               # Profile images, project screenshots
└── files/             # CV PDFs, certificates, research papers
```

## Deployment Notes

### cPanel

1. Upload files via File Manager or FTP
2. Create MySQL database in cPanel → MySQL Databases
3. Import `setup.sql` via phpMyAdmin
4. Edit `config.php` with database credentials
5. Edit `superadmin.php` with your password
6. Set file permissions: `chmod 644 config.php superadmin.php`

### SSL / HTTPS

Ensure your site forces HTTPS. In `.htaccess` (create if not exists):

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Troubleshooting

| Issue | Solution |
|---|---|
| Database connection failed | Verify DB_HOST, DB_NAME, DB_USER, DB_PASS in `config.php` |
| Images not uploading | Set `img/` and `files/` to `chmod 755` |
| Emails not sending | Ensure PHP `mail()` is enabled; check server spam filters |
| Admin login not working | Verify `superadmin.php` password is correct; check session save path |
| 403 on admin pages | Ensure CSRF token is present in all POST forms |

## Customization Tips

- **Change accent colors**: Edit CSS variables in `index.php` (`:root` / `[data-theme]`)
- **Add social links**: Edit Profile in admin panel (Facebook, LinkedIn, YouTube, TikTok, WhatsApp)
- **Add Google Analytics**: Add tracking code before `</head>` in `index.php`
- **Custom fonts**: Modify Google Fonts link in `index.php` `<head>`

