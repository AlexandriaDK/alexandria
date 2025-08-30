<?php
session_start();
require_once('../../connect.php');
require_once('../../base.inc.php');
require_once '../../../includes/social.php';
set_session_redirect_url();

require_once('includes/sa_init.php');

$steam_web_api_key = STEAM_WEB_API_KEY;

if (!$auth->IsUserLoggedIn()) {
  header("Location: " . $auth->GetLoginURL());
} else {
  $siteuserid = $auth->SteamID;
  $userdataurl = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $steam_web_api_key . '&steamids=' . $auth->SteamID;
  $json = json_decode(file_get_contents($userdataurl));

  $username = $json->response->players[0]->personaname;
  $realname = $json->response->players[0]->realname;

  if ($realname) {
    $name = $realname;
  } elseif ($username) {
    $name = $username;
  } else {
    $name = "Steam User";
  }

  $user_id = do_steam_login($siteuserid, $name);

  $redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
  header("Location: $redirect_url");
}
