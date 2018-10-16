<?php
session_start();
require_once('./connect.php');
require_once('base.inc');
ini_set("display_errors",TRUE);
$app_id = 6044298682;

require_once __DIR__ . '/facebook-php-sdk-v4-5.0-dev/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '6044298682',
  'app_secret' => '1da33fef158dde6bec1c5c520f18ab01',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);

// Validation (these will throw FacebookSDKException's when they fail)
//$tokenMetadata->validateAppId($config['app_id']);
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
  }

//  echo '<h3>Long-lived</h3>';
//  var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');



// Custom for Alexandria app:

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name', $_SESSION['fb_access_token']);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();

$user_id = do_fb_login($user['id'], $user['name'] );

$_SESSION['user_id'] = $user_id;
$name = getone("SELECT name FROM users WHERE id = '$user_id'");
$_SESSION['user_name'] = $name;
$_SESSION['user_site'] = 'Facebook';
$_SESSION['user_site_id'] = $fbuserid;
$_SESSION['user_author_id'] = (int) getone("SELECT aut_id FROM users WHERE id = '$user_id'");
$_SESSION['user_editor'] = (bool) getone("SELECT editor FROM users WHERE id = '$user_id'");
$_SESSION['user_admin'] = (bool) getone("SELECT admin FROM users WHERE id = '$user_id'");
$_SESSION['user_achievements'] = getcol("SELECT achievement_id FROM user_achievements WHERE user_id = '$user_id'");
$redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
$_SESSION['token'] = md5('¤3"#xS' . uniqid() );

check_login_achievements();

header("Location: $redirect_url");

exit;

?>
