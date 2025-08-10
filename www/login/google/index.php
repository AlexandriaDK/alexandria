<?php
define('LANGNOREDIRECT', true);
require_once __DIR__ . '/../../../vendor/autoload.php';

session_start();
require_once('../../connect.php');
require_once('../../base.inc.php');
set_session_redirect_url();

$client = new Google_Client();
$client->setAuthConfigFile('../../../includes/client.google.json');
$client->addScope('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email');

if ($_GET['logout']) {
  $client->revokeToken();
  unset($_SESSION['access_token']);
  print "Logged out";
  exit;
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  // already logged in?
  header("Location: /");
} else {
  $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/login/google/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
