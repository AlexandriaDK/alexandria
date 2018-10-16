<?php

if(session_id() == '') {
	session_start();
}

include("openid.php");

class TwitchAuth
{

	private $OpenID;
	private $OnLoginCallback;
	private $OnLoginFailedCallback;
	private $OnLogoutCallback;

	public $TwitchID;

	public function __construct($Server = 'DEFAULT')
	{
		if($Server = 'DEFAULT') $Server = $_SERVER['SERVER_NAME'];
		$this->OpenID = new LightOpenID($Server);
		$this->OpenID->identity = 'https://id.twitch.tv/oauth2/authorize';

		$this->OnLoginCallback = function(){};
		$this->OnLoginFailedCallback = function(){};
		$this->OnLogoutCallback = function(){};
	}

	public function __call($closure, $args)
	{
	        return call_user_func_array($this->$closure, $args);
	}

	public function Init()
	{
		if($this->IsUserLoggedIn())
		{
			$this->TwitchID = $_SESSION['twitchid'];
			return;
		}

		if($this->OpenID->mode == 'cancel')
		{

			$this->OnLoginFailedCallback();

		}
		else if($this->OpenID->mode)
		{
			if($this->OpenID->validate())
			{
				$this->TwitchID = basename($this->OpenID->identity);
				if($this->OnLoginCallback($this->TwitchID))
				{
					$_SESSION['twitchid'] = $this->TwitchID;
				}
			}
			else
			{
				$this->OnLoginFailedCallback();
			}
		}
	}

	public function IsUserLoggedIn()
	{
		return isset($_SESSION['twitchid']) && strpos($_SESSION['twitchid'], "7656") === 0 ? true : false;
	}

	public function RedirectLogin()
	{
		header("Location: " . $this->GetLoginURL());
	}

	public function GetLoginURL()
	{
		return $this->OpenID->authUrl();
	}

	public function Logout()
	{
		$this->OnLogoutCallback($this->TwitchID);

		unset($_SESSION['twitchid']);
		header("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	}

	public function SetOnLoginCallback($OnLoginCallback)
	{
		$this->OnLoginCallback = $OnLoginCallback;
	}

	public function SetOnLogoutCallback($OnLogoutCallback)
	{
		$this->OnLogoutCallback = $OnLogoutCallback;
	}

	public function SetOnLoginFailedCallback($OnLoginFailedCallback)
	{	
		$this->OnLoginFailedCallback = $OnLoginFailedCallback;
	}
}

?>
