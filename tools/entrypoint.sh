#!/bin/sh
set -e

# Run import script if needed
php /usr/local/bin/db_and_news_import.php

# Start supervisord to run both php-fpm and nginx
exec /usr/bin/supervisord -c /etc/supervisord.conf
