# Alexandria
First online code version of Alexandria.dk, the online gaming library of role-playing scenarios, designer boardgames, LARPs, conventions and much more.

## Caveat
The project was started in 2000. Some of the code might be from that era.

## Install
The code requires PHP 7. MySQL 5+ is needed as RDBMS.

The code is currently Apache based. You might need these AliasMatches in your server config or virtual host for stripping extensions.
```
AliasMatch ^/([a-z0-9_]+)$ /var/www/html/$1.php
AliasMatch ^/download/(.*)$ /var/www/html/download.php/$1
```

The site includes an installer feature when accessed the first time.

Remember to check out the config files under `includes/`. The database file (db.auth.php) is the only crucial file to have configured.

## TODO
* Config file
  * Dynamic domain name
  * Paths to downloadable files
* Bulk download of downloadable files
* And much more ...

