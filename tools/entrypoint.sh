#!/bin/sh
set -e

# Run import script if needed
php /usr/local/bin/db_and_news_import.php
exec apache2-foreground
