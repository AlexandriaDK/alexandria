<?php

require("SteamAuth/SteamAuth.class.php");

$auth = new SteamAuth();
$auth->SetOnLoginCallback(function ($steamid) {
  return true;
});
$auth->SetOnLoginFailedCallback(function () {
  return true;
});
$auth->Init();
