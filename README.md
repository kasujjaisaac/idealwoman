# Ideal Woman

A Laravel 13 website and content management panel for Ideal Woman Community Based Organisation in Greater Masaka, Uganda. Built with Blade, Tailwind CSS 4 and JavaScript.

## Features

- Public pages for home, about, programmes, stories, partnerships and contact.
- Protected admin panel with administrator and editor roles.
- Editors for page copy, programmes, leadership, partnership opportunities, navigation, contact details, SEO and social links.
- Story creation, draft previews, publishing, unpublishing and revision recovery.
- Image and PDF uploads, website image replacement and organisational profile downloads.
- Enquiry tracking, team management and account/password settings.
- Sitemap and configurable search indexing.

## Requirements

- PHP 8.5 is the project's development runtime. Check the installed dependencies with `composer check-platform-reqs` on your server.
- Composer 2.
- Node.js 22.12+ or a compatible newer version, and npm.
- SQLite with `pdo_sqlite` for local development/tests, or a configured MySQL database.

## First-time setup

These commands are for a **new checkout**. Keep the existing `.env`, application key and database when updating an installation.

```sh
composer install
cp .env.example .env
php artisan key:generate --no-interaction
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
npm ci --ignore-scripts
npm run build
php artisan serve
```

On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp`, and `npm.cmd` if the execution policy blocks `npm.ps1`.

The example environment uses SQLite, file sessions/cache and synchronous mail. The database is required for content, users, revisions and enquiries. The seeder imports the original stories without overwriting saved edits or creating default accounts.

For asset development, run `npm run dev` alongside the Laravel server.

## Administrator setup

Create your own account using an interactive password prompt:

```sh
php artisan admin:create admin@example.com --name="Website Administrator"
```

Alternatively, generate a secure initial password:

```sh
php artisan admin:create admin@example.com --name="Website Administrator" --generate-password --no-interaction
```

Open `/admin/login` on the running application. Administrators can add editors and administrators under **Team & access**. Change the initial password under **My account**. No account credentials or existing users are distributed with the source code.

Save content as a draft before publishing. Draft preview links require an authorised account. Restoring a revision creates a draft; publish it separately after review. Public content is escaped plain text. Story paragraphs are separated by a blank line.

Uploaded files are stored under `storage/app/private/cms` and served through a controlled media route; a public storage symlink is not required. Back up uploaded files with the database. Git does not include uploads or saved content changes.

## Email and indexing

Enquiry sending is disabled in `.env.example`. Configure the private server environment with your provider's SMTP settings and set `MAIL_MAILER=smtp`. Set `ENQUIRIES_TO` to the receiving mailbox. Enable `ENQUIRIES_ENABLED=true` only after testing SMTP acceptance and actual mailbox delivery.

The form includes CSRF protection, server validation, a honeypot and rate limiting. A successful mail-service submission is saved to the admin enquiry inbox. Failed submissions preserve input and show direct contact alternatives. Log and array mailers do not count as live delivery.

Keep `SITE_INDEXABLE=false` on local and staging installations. Set it to `true` on the approved production domain and configure `APP_URL` with the final HTTPS URL. Robots metadata, robots.txt and sitemap.xml use these settings.

After changing production environment variables, rebuild the configuration cache with `php artisan config:cache`.

## Checks

```sh
composer validate --strict
php artisan test --compact
php vendor/bin/pint --dirty --format agent
npm run build
```

Tests use an isolated in-memory SQLite database. They cover the public website, enquiries, authentication, roles, content editing, publishing, previews, revisions, uploads and account management. Real SMTP delivery and visual browser review are separate checks.

## Project layout

- `app/Http/Controllers/Admin` ? admin actions and access boundaries.
- `app/Services/SiteContent.php` ? public content loading and versioned saves.
- `config/content.php` ? editable page fields and fallback copy.
- `config/idealwoman.php` ? original programme/story defaults and site configuration.
- `resources/views/pages` ? public pages; `resources/views/admin` ? editing screens.
- `resources/css` and `resources/js` ? public and admin styles/scripts.
- `public/images`, `public/downloads`, `resources/fonts` ? distributed website assets.
- `database/migrations`, `database/seeders`, `tests` ? schema, original story import and automated checks.

## GitHub and releases

Commit the source, dependency lockfiles, migrations, tests, original public assets and `.env.example`. The ignore rules exclude private environments, credentials, databases, uploads, logs, dependency directories and generated builds. Review `git diff --cached --stat` and staged files before pushing.

Create an empty GitHub repository, then connect it using its actual URL:

```sh
git remote add origin https://github.com/YOUR-ACCOUNT/idealwoman.git
git push -u origin main
```

Use a private repository if source/assets are not intended for public distribution. GitHub contains source code; database content and uploaded media need a separate backup and transfer process.

## Production deployment

1. Back up the existing database, `.env` and uploaded files before updating.
2. Install PHP dependencies with `composer install --no-dev --prefer-dist --optimize-autoloader`.
3. Run `npm ci --ignore-scripts` and `npm run build` in the build environment. Deploy the complete generated `public/build` directory with the application.
4. Configure the production database and private `.env`. Set `APP_ENV=production`, `APP_DEBUG=false`, the HTTPS `APP_URL` and `SESSION_SECURE_COOKIE=true`. Generate `APP_KEY` once for a new installation and preserve it on updates.
5. Point the web server document root to the application's `public` directory, including its `.htaccess` when using Apache. Keep the application root outside `public_html` on cPanel.
6. Allow the hosting account to write to `storage` and `bootstrap/cache`. Run `php artisan migrate --force --no-interaction` and `php artisan db:seed --force --no-interaction`.
7. Create a production administrator if this is a new installation. Existing local accounts are not copied through Git.
8. Run `php artisan optimize:clear` followed by `php artisan optimize`. Verify public pages, admin sign-in, images, downloads, draft previews and mail delivery on the deployed host.

No continuous Node process or queue worker is required with the supplied synchronous setup. Preserve `storage` and the database across releases.
