# USB project
Alexandria turned 20 year old in 2023. On that occation we bought 200 USB sticks and put a mirror of Alexandria on them to hand out to anyone interested.

## Purpose
The idea of the USB project is to reflect that Alexandria is a free, open resource. No login is required and the site will never require payments for the scenarios that people volunteerly put online - in fact, the authors of all the scenarios would also have to agree to such an arrangement if we ever wanted to have people to pay for the content.

With the USB project we would like to show the world how dedicated we are to our mission. People should be able to easily contain 

## Updates
As Alexandria is continuously updated it is important to have scripts to fetch new scenarios and updated data.

### Windows
A PowerScript file with a GUI will perform the updates.
* Should there be an option for automatic update without GUI?
* Should the GUI call some background script for updates?

### Mac/Linux
Not determined yet. Which tools are available on a common Mac or Linux system? Perhaps Python or PHP, probably without GUI. Or maybe an optional frontend GUI script to call the Python/PHP update script. Perhaps [AppleScript](https://developer.apple.com/library/archive/documentation/AppleScript/Conceptual/AppleScriptX/AppleScriptX.html)?

## Bootstrapping
Any user should be able to download a simple file to get started even without a USB stick to begin with. The scripts for mirroring content should also work on a clean folder without any files at all.

## Issues
When we update the underlying data model we risk breaking stuff. 

* Make the HTML read a config file (JSON or .js) to determine whether local storage for files are used. The config file should include a version number.
* Have the HTML reflect that updates are possible. Use some kind of version numbers?
  * Some browsers won't allow remote calls from a local HTML page. Make a link to an external resource (e.g. https://alexandria.dk/usb) with ?version=x.x in the URL.


# Sponsor
The USB sticks have been kindly sponsored by the wonderful Danish board game café [Bastard Café](https://bastardcafe.dk).
