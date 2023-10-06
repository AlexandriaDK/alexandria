# Alexandria
First online code version of Alexandria.dk, the online gaming library of role-playing scenarios, designer boardgames, LARPs, conventions and much more.

## Caveat
The project was started in 2000. Some of the code might be from that era.

## Install
Make sure you have Docker and Docker Compose installed. 
1. Run `docker compose build`
2. Run `docker compose up -d`
3. Run `composer install` 
4. Open [http://localhost:81/](http://localhost:81/)

### Cron jobs
Some scripts located in [tools](tree/master/tools) are meant to be running on a hourly or daily basis. None of these scripts are strictly required just to have the site running.

* `feedfetcher.php` fetches RSS content from blogs
* `update_popularity.php` updates the popularity field for games for sorting purposes
* `dailystats.php` outputs a CSV line with a count of games, persons, conventions, users and so on.
* `fileindexer.php` checks uploaded PDF files and indexes them for content search purposes.

An example of crontab entries follows:

```
# m h  dom mon dow   command
26 * * * * php ~/web/alexandria/tools/feedfetcher.php >>~/alexfeed.log
10 0 * * * php ~/web/alexandria/tools/update_popularity.php
15 0 * * * php ~/web/alexandria/tools/dailystats.php >>~/rpgstats.txt
*/5 * * * * php ~/web/alexandria/tools/fileindexer.php 10 >>~/alexindex.log
```

## TODO
* Config file
  * Dynamic domain name
  * Paths to downloadable files
* Bulk download of downloadable files
* More installation checkup for optional modules, correct permissions, etc.
* And much more ...

## Troubleshooting
- Getting the error "attempt to perform an operation not allowed by the security policy PDF" when creating thumbnails on file page at editor section? Remove the line `<policy domain="coder" rights="none" pattern="PDF" />` in /etc/ImageMagick-6/policy.xml
- Can't upload files in the editor interface? Check the php.ini directive [upload_max_filesize](https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize) and [post_max_size](https://www.php.net/manual/en/ini.core.php#ini.post-max-size). Their default values are only 2M and 8M respectively meaning the maximum file size for an uploaded file is 2 MB. These values ought to be raised drastically to e.g. 128M. The settings have a changeable mode of [PHP_INI_DIR](https://www.php.net/manual/en/configuration.changes.modes.php) meaning they can be set in `php.ini`, `.htaccess`, `httpd.conf` or `.user.ini`.
