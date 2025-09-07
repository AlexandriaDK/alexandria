<?php
define('LANGNOREDIRECT', true);
require_once __DIR__ . '/../../../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('../../../includes/client.google.json');
$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/login/google/oauth2callback.php');
$client->addScope('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email');

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else { // logged in
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  chdir('../../');
  require_once "./connect.php";
  require_once "base.inc.php";

  $userinfo_url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $_SESSION['access_token']['access_token'];
  $userinfo_json = file_get_contents($userinfo_url);
  $userinfo = json_decode($userinfo_json);
  print "User info: ";
  $siteuserid = $userinfo->id;
  $name = $userinfo->name;
  $locale = $userinfo->locale;

  $user_id = do_google_login($siteuserid, $name);

  $redirect_uri = get_redirect_url();
  #  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
  header('Location: https://alexandria.dk/login/google/index.php');
}
