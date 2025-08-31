<?php
require_once "./connect.php";
require_once "base.inc.php";

# Allow version to be sent with the URL, e.g. "https://alexandria.dk/usb?client=offline&version=$version"

$t->display('usb.tpl');
