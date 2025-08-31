# Alexandria

Alexandria.dk – the online gaming library of role‑playing scenarios, designer board games, LARPs, conventions, and more.

## Caveat

The project originated in 2000. Some legacy code remains.

## Quick start (Docker)

Prerequisites:

- Docker Desktop

Getting started:

1. **Configure database settings:**

   Copy the file `includes/default.db.auth.php` to `includes/db.auth.php`.

   Here is an example of default values that align with the docker-compose file. If you want to change your setup, it should be changed in db.auth.php and in the docker-compose as well.

```php
<?php
define('DB_NAME', 'alexandria');
define('DB_USER', 'alexuser');
define('DB_PASS', 'alexpass');
define('DB_HOST', 'db');
define('DB_CONNECTOR', 'mysqli');
```

2. Start services

```cmd
docker-compose up -d --build
```

What this does:

- Starts MariaDB 11.8 with dev credentials
- Starts PHP-FPM 8.4 on Alpine Linux for processing PHP files
- Starts Nginx with HTTP/3 (QUIC) and HTTP/2 support as the web server

### VS Code Development with Dev Containers

If you use Visual Studio Code, you can develop directly inside the Docker web container using the Dev Containers extension. This allows you to use the PHP and Composer installed in the container, without installing them on your own machine.

**How to use:**

1. Install the “Dev Containers” extension in VS Code.
2. Run `docker-compose up -d` to start the containers.
3. In VS Code, open the Command Palette (`Ctrl+Shift+P`) and select “Dev Containers: Attach to Running Container”.
4. Choose the web container for this project.

You’ll now have full editor support, autocompletion, and terminal access inside the container, using its PHP environment. This keeps your local system clean and matches your production setup.

3. Open the site

**HTTP (redirects to HTTPS):**
- http://localhost:8080

**HTTPS with HTTP/2:**
- https://localhost:8443

**HTTPS with HTTP/3 (QUIC):**
- https://localhost:8443 (use browsers with HTTP/3 support like Chrome, Firefox, or curl with --http3)

**Testing HTTP/3:**
- Browser dev tools: Check the "Protocol" column in Network tab
- Command line: `curl --http3 -k https://localhost:8443/en/`

Notes for development:

- `www/` is mounted into the container for live editing
- Smarty templates (`smarty/templates`) are mounted for live template work
- Compiled Smarty files live in `smarty/templates_c` (created in the container)
- Database data persists in the `db_data` Docker volume
- Self-signed SSL certificates are generated automatically for HTTPS/HTTP/3
- Nginx serves static files directly and proxies PHP requests to PHP-FPM

## Manual installation (without Docker)

Recommended stack:

- PHP 8.0+ (8.1+ recommended) with extensions: mysqli, mbstring, intl, gd, zip
- MariaDB/MySQL
- Nginx (for HTTP/3 support) or Apache (DocumentRoot should point to the `www/` folder)

Steps (outline):

- Copy `includes/default.db.auth.php` to `includes/db.auth.php` and set credentials
- Install Composer dependencies at the repository root (creates `vendor/`):

  - `composer install`

- Ensure Apache has `AllowOverride All` so `.htaccess` works
- Point your virtual host to the `www/` directory (code expects `vendor/` at repo root)
- Optional utilities: `php-oauth` (for some OAuth flows), `php-imagick` (PDF thumbnails)

Notes:

- Smarty 5 is installed via Composer (no manual download needed)
- The site has an installer/initializer on first run, but importing sample data/news is handled by the Docker helper scripts; outside Docker you can run `tools/dbimport.py` and `tools/import_news.py` manually if desired

## Cron jobs

Scripts in `tools/` can be scheduled (not required just to run the site):

- `feedfetcher.php` – fetch RSS content from blogs
- `update_popularity.php` – refresh popularity values for sorting
- `dailystats.php` – output counts of games/persons/conventions/users
- `fileindexer.php` – index uploaded PDFs for content search

Example crontab:

```
# m h  dom mon dow   command
26 * * * * php ~/web/alexandria/tools/feedfetcher.php >>~/alexfeed.log
10 0 * * * php ~/web/alexandria/tools/update_popularity.php
15 0 * * * php ~/web/alexandria/tools/dailystats.php >>~/rpgstats.txt
*/5 * * * * php ~/web/alexandria/tools/fileindexer.php 10 >>~/alexindex.log
```

## TODO

- Config file
  - Dynamic domain name
  - Paths to downloadable files
- Bulk download of downloadable files
- More installation checkup for optional modules, correct permissions, etc.
- And much more ...

## Troubleshooting

- Getting the error "attempt to perform an operation not allowed by the security policy PDF" when creating thumbnails on file page at editor section? Remove the line `<policy domain="coder" rights="none" pattern="PDF" />` in /etc/ImageMagick-6/policy.xml
- Can't upload files in the editor interface? Check the php.ini directive [upload_max_filesize](https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize) and [post_max_size](https://www.php.net/manual/en/ini.core.php#ini.post-max-size). Their default values are only 2M and 8M respectively meaning the maximum file size for an uploaded file is 2 MB. These values ought to be raised drastically to e.g. 128M. The settings have a changeable mode of [PHP_INI_DIR](https://www.php.net/manual/en/configuration.changes.modes.php) meaning they can be set in `php.ini`, `.htaccess`, `httpd.conf` or `.user.ini`.
