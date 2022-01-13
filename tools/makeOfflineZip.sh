#!/bin/sh
date
echo "Updating JSON"
php updateOfflineJS.php
echo "Zipping"
zip -r ../loot.alexandria.dk/AlexandriaOffline.zip AlexandriaOffline/ --exclude '*/.gitignore'
