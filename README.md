# Alexandria
First online code version of Alexandria.dk, the online gaming library of role-playing scenarios, designer boardgames, LARPs, conventions and much more.

## Caveat
The project was started in 2000. Some of the code might be from that era.

## Install
The code requires PHP 7. MySQL 5+ is needed as RDBMS.

The code is currently Apache based. The site includes an installer feature for setting up the database and fetching content when accessed the first time.

Please note:
- Apache directive `AllowOverride all` needs to be set.
- The web site requires template system Smarty, which is currently not included per default: https://www.smarty.net/
- Remember to check out the config files under `includes/`. The database file (db.auth.php) is the only crucial file to have configured.
- Need login through third party sites? Remember to install OAuth for PHP (`sudo apt install php-oauth`)

## TODO
* Config file
  * Dynamic domain name
  * Paths to downloadable files
* Bulk download of downloadable files
* And much more ...

