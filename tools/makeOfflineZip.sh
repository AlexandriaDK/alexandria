#!/bin/sh
date
echo "Updating JSON"
php updateOfflineJS.php
echo "Zipping"
zip -r ../loot.alexandria.dk/AlexandriaOffline.zip AlexandriaOffline/ --exclude '*/.gitignore'
rm -rf ../loot.alexandria.dk/AlexandriaOffline
cp -r AlexandriaOffline/ ../loot.alexandria.dk/
