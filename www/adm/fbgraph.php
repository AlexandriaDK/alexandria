<?php
$admonly = TRUE;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$id = (int) $_GET['user_id'];
if (!$id) exit;
$graph_id = getone("SELECT siteuserid FROM loginmap WHERE user_id = $id AND site = 'facebook'");
if (!$graph_id) exit;

require_once __DIR__ . '/../facebook-php-sdk-v4-5.0-dev/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '6044298682',
  'app_secret' => '1da33fef158dde6bec1c5c520f18ab01',
  'default_graph_version' => 'v2.2',
  ]);

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/' . $graph_id . '?fields=id,name', $_SESSION['fb_token'] );
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();

echo 'Name: ' . $user['name'];
?>
