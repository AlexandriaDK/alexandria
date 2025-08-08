<?php
session_start();
ini_set("display_errors", TRUE);
// require_once __DIR__ . '/facebook-php-sdk-v4-5.0-dev/src/Facebook/autoload.php';
require_once('./connect.php');
require_once('base.inc.php');

require_once __DIR__ . '/../includes/social.php';
require_once __DIR__ . '/php-graph-sdk/src/Facebook/autoload.php';

set_session_redirect_url();

$fb = new Facebook\Facebook([
  'app_id' => FACEBOOK_APP_ID,
  'app_secret' => FACEBOOK_APP_SECRET,
  'default_graph_version' => 'v2.2',
]);

$helper = $fb->getRedirectLoginHelper();

//$permissions = ['email']; // Optional permissions
$permissions = [];
$loginUrl = $helper->getLoginUrl('https://alexandria.dk/fb-newcallback.php', $permissions);

header("Location: " . $loginUrl);
//echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
