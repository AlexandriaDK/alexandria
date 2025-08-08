<?php
require("./connect.php");
require("base.inc.php");

# Allow version to be sent with the URL, e.g. "https://alexandria.dk/usb?client=offline&version=$version"

$t->display('usb.tpl');
