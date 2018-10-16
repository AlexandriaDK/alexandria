<?php

    require("SteamAuth/TwitchAuth.class.php");

    $auth = new TwitchAuth();
    $auth->SetOnLoginCallback(function($twitchid){
        return true;
    });
    $auth->SetOnLoginFailedCallback(function(){
        return true;
    });
    $auth->Init();

?>
