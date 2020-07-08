<?php
/*

Copyright 2017 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License"). You may not use this file except in compliance with the License. A copy of the License is located at

    http://aws.amazon.com/apache2.0/

or in the "license" file accompanying this file. This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

*/

require '../../../includes/social.php';
require 'twitch.php';

$provider = new TwitchProvider([
    'clientId'                => TWITCH_CLIENT_ID,     // The client ID assigned when you created your application
    'clientSecret'            => TWITCH_CLIENT_SECRET, // The client secret assigned when you created your application
    'redirectUri'             => 'https://alexandria.dk/login/twitch/',  // Your redirect URL you specified when you created your application
    'scopes'                  => ['user_read']                // The scopes you would like to request 
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

    // Fetch the authorization URL from the provider, and store state in session
    $authorizationUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();

    // Display link to start auth flow
    header("Location: $authorizationUrl");
    echo "<html><a href=\"$authorizationUrl\">Click here to link your Twitch Account</a><html>";
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    
    exit('Invalid state');

} else {

    try {

        // Get an access token using authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Using the access token, get user profile
        $resourceOwner = $provider->getResourceOwner($accessToken);
        $user = $resourceOwner->toArray();

	/*
        echo '<html><table>';
        echo '<tr><th>Access Token</th><td>' . htmlspecialchars($accessToken->getToken()) . '</td></tr>';
        echo '<tr><th>Refresh Token</th><td>' . htmlspecialchars($accessToken->getRefreshToken()) . '</td></tr>';
        echo '<tr><th>Username</th><td>' . htmlspecialchars($user['display_name']) . '</td></tr>';
        echo '<tr><th>Bio</th><td>' . htmlspecialchars($user['bio']) . '</td></tr>';        
        echo '<tr><th>Image</th><td><img src="' . htmlspecialchars($user['logo']) . '"></td></tr>';
        echo '</table></html>';

	*/
	session_start();
	chdir('../../');
	require_once('./connect.php');
	require_once('base.inc.php');

	$siteuserid = $user['_id'];
	$name = $user['display_name'];

	$user_id = do_twitch_login($siteuserid, $name);

	$redirect_url = get_redirect_url($_SERVER['HTTP_REFERER']);
	header("Location: $redirect_url");
	
	
        // You can now create authenticated API requests through the provider.
        //$request = $provider->getAuthenticatedRequest(
        //    'GET',
        //    'https://api.twitch.tv/kraken/user',
        //    $accessToken
        //);

    } catch (Exception $e) {
        exit('Caught exception: '.$e->getMessage());
    }
}
