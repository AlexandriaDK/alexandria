<?php
# THIS FILE DOES NOT WORK
# USE login/twitch/ instead!

exit;

session_start();
require_once('./connect.php');
require_once('base.inc');

require_once('includes/tw_init.php');

$twitch_client_id = 'rftlcx9bdmd0l8fvfqf4di336uvu2g';
$twitch_client_secret = '7kqniyp4ix2kynmsfbbd4ole6488rk';
$redirect_uri = 'https://alexandria.dk/twitchlogin';
$response_type = 'code';
$grant_type = 'authorization_code';
$scope = 'openid';

$code = (string) $_GET['code'];

$authorize_url = 'https://id.twitch.tv/oauth2/authorize' .
                 '?client_id=' . $twitch_client_id .
                 '&redirect_uri=' . rawurlencode($redirect_uri) .
                 '&response_type=' . rawurlencode($response_type) . 
                 '&scope=' . rawurlencode($scope)
                 ;

$userdata_url = 'https://id.twitch.tv/oauth2/authorize' .
                 '?client_id=' . $twitch_client_id .
                 '&client_secret=' . $twitch_client_secret .
                 '&redirect_uri=' . rawurlencode($redirect_uri) .
                 '&grant_type=' . rawurlencode($grant_type) . 
                 '&code=' . rawurlencode($code)
                 ;

if ($code) {
	var_dump($code);
	var_dump($userdata_url);
	$ch = curl_init($userdata_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);
	curl_close($ch);
	var_dump($response);
	

} elseif(true) {
	print $authorize_url;
} else {
	var_dump($auth);
	exit;
	$siteuserid = $auth->SteamID;	
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

?>
