<?php
session_start();
chdir('../../');
require_once './connect.php';
require_once 'base.inc.php';
require_once '../includes/social.php';
set_session_redirect_url();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 300); //300 seconds = 5 minutes. In case if your CURL is slow and is loading too much (Can be IPv6 problem)

error_reporting(E_ALL);

define('OAUTH2_CLIENT_ID', DISCORD_CLIENT_ID);
define('OAUTH2_CLIENT_SECRET', DISCORD_CLIENT_SECRET);

$authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
$tokenURL = 'https://discordapp.com/api/oauth2/token';
$apiURLBase = 'https://discordapp.com/api/users/@me';

// Start the login process by sending the user to Discord's authorization page
if (get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'https://alexandria.dk/login/discord/',
    'response_type' => 'code',
    'scope' => 'identify'
  );

  // Redirect the user to Discord's authorization page
  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}


// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if (get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'https://alexandria.dk/login/discord/',
    'code' => get('code')
  ));
  $_SESSION['discord_access_token'] = $token->access_token;
  header('Location: ' . $_SERVER['PHP_SELF']);
}

if (session('discord_access_token')) {
  $user = apiRequest($apiURLBase);
  $siteuserid = $user->id;
  $siteusername = $user->username;
  do_discord_login($siteuserid, $siteusername);
  $redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
  header("Location: $redirect_url");
} else {
  header("Location: ./?action=login");
}


if (get('action') == 'logout') {
  $params = array(
    'access_token' => $_SESSION['discord_access_token']
  );

  // Redirect the user to Discord's revoke page
  header('Location: https://discordapp.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
  die();
}

function apiRequest($url, $post = FALSE, $headers = array())
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

  $response = curl_exec($ch);


  if ($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

  $headers[] = 'Accept: application/json';

  if (session('discord_access_token'))
    $headers[] = 'Authorization: Bearer ' . session('discord_access_token');

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  return json_decode($response);
}

function get($key, $default = NULL)
{
  return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default = NULL)
{
  return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}
