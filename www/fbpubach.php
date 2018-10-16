<?php
session_start();
ini_set("display_errors",TRUE);
$app_id = 6044298682;

$admonly = TRUE;
require "rpgconnect.inc.php";
require "base.inc";

require_once __DIR__ . '/facebook-php-sdk-v4-5.0-dev/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '6044298682',
  'app_secret' => '1da33fef158dde6bec1c5c520f18ab01',
  'default_graph_version' => 'v2.2',
  ]);

$fbApp = $fb->getApp();

$at = $fbApp->getAccessToken();

/* make the API call */
$request = $fb->request(
  'POST',
  '/me/achievements',
  array (
    'achievement' => '1101189009900964'
  ),
  $at
);

try {
  $response = $fb->getClient()->sendRequest($request);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

// $response = $request->execute();
// $graphObject = $response->getGraphObject();
/* handle the result */

print "<pre>";
print "Response: ";
print_r($response);
print "</pre>\n";
flush();
?>
