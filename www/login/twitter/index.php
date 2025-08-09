<?php
session_start();
require_once('../../connect.php');
require_once('../../base.inc.php');
require '../../../includes/social.php';

set_session_redirect_url();

// example from:
// http://php.net/manual/en/oauth.examples.fireeagle.php
$req_url = 'https://api.twitter.com/oauth/request_token';
$authurl = 'https://api.twitter.com/oauth/authenticate'; // /authorize asks for permission each time
$acc_url = 'https://api.twitter.com/oauth/access_token';
// $api_url = 'https://fireeagle.yahooapis.com/api/0.1';
$api_url = 'https://api.twitter.com/oauth/token';
$conskey = TWITTER_KEY;
$conssec = TWITTER_SECRET;

// In state=1 the next request should include an oauth_token.
// If it doesn't go back to 0
if (!isset($_GET['oauth_token']) && $_SESSION['state'] == 1) $_SESSION['state'] = 0;
if (isset($_GET['restart'])) $_SESSION['state'] = 0;

try {
  $oauth = new OAuth($conskey, $conssec, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
  $oauth->enableDebug();
  if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
    $request_token_info = $oauth->getRequestToken($req_url);
    $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['state'] = 1;
    header('Location: ' . $authurl . '?oauth_token=' . $request_token_info['oauth_token']);
    exit;
  } else if ($_SESSION['state'] == 1) {
    $oauth->setToken($_GET['oauth_token'], $_SESSION['secret']);
    $access_token_info = $oauth->getAccessToken($acc_url);
    $user_id = do_twitter_login($access_token_info['user_id'], $access_token_info['screen_name']);
    $_SESSION['state'] = 2;
    $_SESSION['token'] = $access_token_info['oauth_token'];
    $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
  }

  $oauth->setToken($_SESSION['token'], $_SESSION['secret']);
  $redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
  header("Location: $redirect_url");
} catch (OAuthException $E) {
  print_r($E);
}
