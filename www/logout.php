<?php
require_once "./connect.php";
require_once "base.inc.php";

award_achievement(49);
session_destroy();
$redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
header("Location: " . $redirect_url);
exit;
