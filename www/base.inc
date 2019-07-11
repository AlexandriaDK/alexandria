<?php
mb_internal_encoding("UTF-8");
// enable session for all pages
// set cache_limiter to 'private' for sake of Firefox
// update: set cache_limiter to 'none' for sake of Firefox *and* IE
session_cache_limiter('none');
session_start();

header("X-Achievement: https://alexandria.dk/myhistory?achievement=createaguiinterfaceusingvisualbasic");
header("X-Clacks-Overhead: GNU Alex Uth, GNU Michael Erik Næsby");


// detect language
#$alexlanguages = [ 'da', 'en', 'nb', 'de', 'sv', 'es', 'ru', 'fr', 'be' ];
#$alexlanguages = [ 'da', 'en', 'nb', 'de', 'sv' ];
$alexlanguages = [ 'da', 'en', 'nb', 'sv' ];
$lang = "";
if (preg_match( '_^/(da|en|nb|de|sv|es|ru|fr|be|fi|xx)/_', $_SERVER["REQUEST_URI"], $matches) ) {
	$lang = $urllang = $matches[1];
	$baseuri = substr($_SERVER['REQUEST_URI'],4);
} else {
	$lang = 'da';
}
define( 'LANG', $lang );

if ( LANG == 'da') {
	setlocale(LC_TIME, "da_DK.UTF-8"); 
} elseif ( LANG == 'en' ) {
	setlocale(LC_TIME, "en_GB.UTF-8"); 
} elseif ( LANG == 'sv' ) {
	setlocale(LC_TIME, "sv_SE.UTF-8"); 
} elseif ( LANG == 'nb' ) {
	setlocale(LC_TIME, "nb_NO.UTF-8"); 
} elseif ( LANG == 'de' ) {
	setlocale(LC_TIME, "de_DE.UTF-8"); 
} elseif ( LANG == 'fr' ) {
	setlocale(LC_TIME, "fr_FR.UTF-8"); 
} elseif ( LANG == 'es' ) {
	setlocale(LC_TIME, "es_ES.UTF-8"); 
} elseif ( LANG == 'ru' ) {
	setlocale(LC_TIME, "ru_RU.UTF-8");
} elseif ( LANG == 'be' ) {
	setlocale(LC_TIME, "be_BY.UTF-8");
} elseif ( LANG == 'fi' ) {
	setlocale(LC_TIME, "fi_FI.UTF-8");
}

// general
define('ALEXFILES','/home/penguin/web/loot.alexandria.dk/files/');

// Smarty 
define('SMARTY_DIR', __DIR__ . '/../smarty/libs/');
require_once(SMARTY_DIR . 'Smarty.class.php');

$t = new Smarty;
$t->template_dir = __DIR__ . '/../smarty/templates/';
$t->compile_dir =  __DIR__ . '/../smarty/templates_c/';
$t->config_dir =   __DIR__ . '/../smarty/configs/';
$t->cache_dir =    __DIR__ . '/../smarty/cache/';
$t->registerPlugin("function","achievements_shown","achievements_shown"); 

// language template folder?
$template_dir = __DIR__ . '/../smarty/templates/';
if (is_dir($template_dir . LANG) ) {
	$t->template_dir = $template_dir . LANG . '/';
} else {
	$t->template_dir = $template_dir .  'generic/';
}

// variables used all over the place

if (!defined('DBERROR') ) {
	$t->assign('stat_all_aut',getone("SELECT COUNT(*) FROM aut"));
	$t->assign('stat_all_sce',getone("SELECT COUNT(*) FROM sce WHERE boardgame = 0"));
	$t->assign('stat_all_board',getone("SELECT COUNT(*) FROM sce WHERE boardgame = 1"));
	$t->assign('stat_all_convent',getone("SELECT COUNT(*) FROM convent"));
	$t->assign('stat_all_sys',getone("SELECT COUNT(*) FROM sys"));
	
	$t->assign('user_id',$_SESSION['user_id']);
	$t->assign('user_author_id',$_SESSION['user_author_id']);
	$t->assign('user_site',$_SESSION['user_site']);
	$t->assign('user_site_id',$_SESSION['user_site_id']);
	$t->assign('user_name',$_SESSION['user_name']);
	$t->assign('user_editor',$_SESSION['user_editor']);
	$t->assign('user_admin',$_SESSION['user_admin']);
	$t->assign('user_achievements_to_display', get_achievements_to_display() );
	$t->assign('ALEXLANGUAGES', $alexlanguages );
	if ($lang) {
		$t->assign('LANG', LANG );
	}
	if ( $urllang ) {
		$t->assign('URLLANG', $urllang );
		$t->assign('BASEURI', $baseuri );
	}
}

// Import language strings
$import_languages = array_unique( [ 'en', LANG ] );
foreach( $import_languages AS $language ) {
	$strings = getall("SELECT label, text, language FROM weblanguages WHERE language = '" . dbesc( $language ) ."'");
	foreach ( $strings AS $string ) {
		$t->assign( '_' . $string['label'], $string['text'] );
	}
}

// Startup variables

if ($_SESSION['user_id']) { // set last active
	doquery("
		UPDATE users
		SET
		active_days_in_row = CASE DATE(last_active) WHEN CURDATE() THEN active_days_in_row WHEN CURDATE() - INTERVAL 1 DAY THEN active_days_in_row + 1 ELSE 0 END,
		last_active = NOW()
		WHERE id = " . $_SESSION['user_id']
	);
	if ($_SESSION['user_author_id']) {
		$scenario_missing_participants = getall("SELECT a.id, a.title FROM sce a INNER JOIN asrel b ON a.id = b.sce_id AND b.aut_id = " . $_SESSION['user_author_id'] . " AND b.tit_id IN (1,4) INNER JOIN csrel c ON a.id = c.sce_id AND c.pre_id = 1 WHERE a.players_min IS NULL GROUP BY a.id ORDER BY RAND() LIMIT 3");
		$scenario_missing_tags = getall("SELECT a.id, a.title FROM sce a INNER JOIN asrel b ON a.id = b.sce_id AND b.aut_id = " . $_SESSION['user_author_id'] . " AND b.tit_id IN (1,4) LEFT JOIN tags c ON a.id = c.sce_id AND c.tag != 'English' WHERE c.sce_id IS NULL GROUP BY a.id ORDER BY RAND() LIMIT 3");
		if ($scenario_missing_participants && $scenario_missing_tags) {
			// choose random set to display
			if (rand(0,1) ) {
				$t->assign('user_scenario_missing_players', $scenario_missing_participants);
			} else {
				$t->assign('user_scenario_missing_tags', $scenario_missing_tags);
			}
		} else {
			$t->assign('user_scenario_missing_players', $scenario_missing_participants);
			$t->assign('user_scenario_missing_tags', $scenario_missing_tags);
		}
	}

	$active_days_in_row = getone("SELECT active_days_in_row FROM users WHERE id = '" . $_SESSION['user_id'] . "'");
	if ($active_days_in_row >= 6)  award_user_achievement($user_id,16); //  7 days in row
	if ($active_days_in_row >= 29) award_user_achievement($user_id,17); // 30 days in row

	check_begin_page_achievements();
}



// login and user specific functions

function get_redirect_url ($url = '') {
	if ($url == '') {
		$url = $_SERVER['HTTP_REFERER'];
	}
	// redirect to referer if alexandria.dk - or frontpage otherwise
	$referer = parse_url($url);
	if (preg_match('/^(.*\.)?alexandria\.dk$/i',$referer['host'])) {
		$redirect_url = $url;
	} else {
		$redirect_url = '/'; // hardcoded
	}
	return $redirect_url;
}


function do_fb_login ($siteuserid,$name) { // presume valid fbuserid!
	$site = 'facebook';
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	$login_today_allusers = getone("SELECT COUNT(*) FROM users WHERE last_login >= CURDATE()");
	if (list($user_id, $logintime) = getrow("SELECT user_id, logintime FROM loginmap WHERE site = '$site' AND siteuserid = '$siteuserid'")) {
		doquery("UPDATE loginmap SET logintime = '$time', ip = '$ip' WHERE site = '$site' AND siteuserid = '$siteuserid'");	
		$last_login_days = getone("SELECT DATEDIFF(CURDATE(), last_login) FROM users WHERE id = '$user_id'");
		doquery("
			UPDATE users
			SET login_count = (login_count + 1),
			login_days_in_row = CASE DATE(last_login) WHEN CURDATE() THEN login_days_in_row WHEN CURDATE() - INTERVAL 1 DAY THEN login_days_in_row + 1 ELSE 0 END,
			last_login = NOW()
			WHERE id = '$user_id'
		");
		list($login_days_in_row, $login_count) = getrow("SELECT login_days_in_row, login_count FROM users WHERE id = '$user_id'");

	} else { // create user
		doquery("INSERT INTO users (id, created, log, ip, name, last_login, login_days_in_row, login_count) VALUES (NULL, NOW(), 'Created: $site $siteuserid\nName: ".dbesc($name)."\nRemote user: {$_SERVER['REMOTE_ADDR']}\n', '$ip', '" . dbesc($name) . "', NOW(), 0, 1)");
		$user_id = dbid();
		if ($user_id) {
			doquery("INSERT INTO loginmap (site, siteuserid, user_id, name, logintime, ip) VALUES ('$site', '$siteuserid', '$user_id', '".dbesc($name)."', '$time', '$ip')");
		}
	}

	// achievements
	if ($last_login_days >= 7)      award_user_achievement($user_id,19); // no login in a week
	if ($login_count >= 25)         award_user_achievement($user_id,71); // login 25 times
	if ($login_today_allusers == 0) award_user_achievement($user_id,76); // first login today
	award_user_achievement($user_id, 1); // login
	return $user_id;
}

function do_twitter_login($siteuserid, $name) {
	do_sso_login($siteuserid, $name, 'twitter');
}

function do_twitch_login($siteuserid, $name) {
	do_sso_login($siteuserid, $name, 'twitch');
}

function do_google_login($siteuserid, $name) {
	do_sso_login($siteuserid, $name, 'google');
}


function do_steam_login($siteuserid, $name) {
	do_sso_login($siteuserid, $name, 'steam');
}

function do_sso_login ($siteuserid, $name, $site) { // presume valid siteuserid!
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	$login_today_allusers = getone("SELECT COUNT(*) FROM users WHERE last_login >= CURDATE()");
	if (list($user_id, $logintime) = getrow("SELECT user_id, logintime FROM loginmap WHERE site = '$site' AND siteuserid = '$siteuserid'")) {
		doquery("UPDATE loginmap SET logintime = '$time', ip = '$ip' WHERE site = '$site' AND siteuserid = '$siteuserid'");	
		$last_login_days = getone("SELECT DATEDIFF(CURDATE(), last_login) FROM users WHERE id = '$user_id'");
		doquery("
			UPDATE users
			SET login_count = (login_count + 1),
			login_days_in_row = CASE DATE(last_login) WHEN CURDATE() THEN login_days_in_row WHEN CURDATE() - INTERVAL 1 DAY THEN login_days_in_row + 1 ELSE 0 END,
			last_login = NOW()
			WHERE id = '$user_id'
		");
		list($login_days_in_row, $login_count) = getrow("SELECT login_days_in_row, login_count FROM users WHERE id = '$user_id'");

	} else { // create user
		doquery("INSERT INTO users (id, created, log, ip, name, last_login, login_days_in_row, login_count) VALUES (NULL, NOW(), 'Created: $site $siteuserid\nName: ".dbesc($name)."\nRemote user: {$_SERVER['REMOTE_ADDR']}\n', '$ip', '" . dbesc($name) . "', NOW(), 0, 1)");
		$user_id = dbid();
		if ($user_id) {
			doquery("INSERT INTO loginmap (site, siteuserid, user_id, name, logintime, ip) VALUES ('$site', '$siteuserid', '$user_id', '".dbesc($name)."', '$time', '$ip')");
		}
	}

	// achievements
	if ($last_login_days >= 7)      award_user_achievement($user_id,19); // no login in a week
	if ($login_count >= 25)         award_user_achievement($user_id,71); // login 25 times
	if ($login_today_allusers == 0) award_user_achievement($user_id,76); // first login today
	award_user_achievement($user_id, 1); // login

	$_SESSION['user_id'] = $user_id;
	$name = getone("SELECT name FROM users WHERE id = '$user_id'");
	$_SESSION['user_name'] = $name;
	$_SESSION['user_site'] = ucfirst($site);
	$_SESSION['user_site_id'] = $siteuserid;
	$_SESSION['user_author_id'] = (int) getone("SELECT aut_id FROM users WHERE id = '$user_id'");
	$_SESSION['user_editor'] = (bool) getone("SELECT editor FROM users WHERE id = '$user_id'");
	$_SESSION['user_admin'] = (bool) getone("SELECT admin FROM users WHERE id = '$user_id'");
	$_SESSION['user_achievements'] = getcol("SELECT achievement_id FROM user_achievements WHERE user_id = '$user_id'");
	$_SESSION['token'] = md5('abcdef' . uniqid() ); // unused, move salt to config if implemented

	return $user_id;
}

function validate_remote_login ($siteuserid,$hash,$time,$site,$name) {
	$siteuserid = intval($siteuserid);
	$time = intval($time);
	if (abs($time - time()) > 1200) return false; // check for timeout - both servers are currently (nov 2010) five minutes off in each direction
	$salt = getsitesalt($site);
	if (!$salt) return false; // remote site not found
	if (md5($siteuserid.$time.$salt) != $hash) return false;

	// Valid user. Existing user?

	if (list($user_id, $logintime) = getrow("SELECT user_id, logintime FROM loginmap WHERE site = '$site' AND siteuserid = '$siteuserid'")) {
		if ($logintime == $time) return false; // protect from replay attack
		doquery("UPDATE loginmap SET logintime = '$time' WHERE site = '$site' AND siteuserid = '$siteuserid'");	
		return $user_id;
	} else { // create user
		doquery("INSERT INTO users (id, created, log) VALUES (NULL, NOW(), 'Created: $site $siteuserid\nName: ".dbesc($name)."\nRemote user: {$_SERVER['REMOTE_ADDR']}\n')");
		$user_id = dbid();
		doquery("INSERT INTO loginmap (site, siteuserid, user_id, name, logintime) VALUES ('$site', '$siteuserid', '$user_id', '".dbesc($name)."', '$time')");
		return $user_id;
	}
	
}

function award_achievement ($achievement_id, $shown = 0) {
	if (!$_SESSION['user_id'] || !$achievement_id) {
		return false;
	}
	return award_user_achievement($_SESSION['user_id'], $achievement_id, $shown);
}

function award_user_achievement ($user_id, $achievement_id, $shown = 0) {
	global $t;
	if (!$user_id || !$achievement_id) {
		return false;
	}
	if (in_array($achievement_id, (array) $_SESSION['user_achievements']) && ($user_id == $_SESSION['user_id']) ) { // already in session
		return false;
	}
	$user_id = (int) $user_id;
	$achievement_id = (int) $achievement_id;
	$shown = (int) $shown;
	$_SESSION['user_achievements'][] = $achievement_id;
	$id = getone("SELECT id FROM user_achievements where user_id = $user_id AND achievement_id = $achievement_id"); // check if already got achievement - not transaction safe
	if (!$id) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$id = doquery("INSERT INTO user_achievements (user_id, achievement_id, completed, shown, ip) VALUES ($user_id, $achievement_id, NOW(), $shown, '$ip')");
		// update current list
		$t->assign('user_achievements_to_display', get_achievements_to_display() );
		return $id;
	} else {
		return false;
	}
}

function check_login_achievements() {
	award_achievement(1);
	$time = date("H:i");
	if ($time == '13:37') award_achievement(47);
//	if ($date == '04-01') award_achievement(50); // april fool's... should first show the day after?
	if ($_SESSION['user_editor'] == TRUE) award_achievement(22); // editor
	if ($_SESSION['user_admin'] == TRUE) award_achievement(23); // admin
}

function get_achievements_to_display() {
	$user_id = (int) $_SESSION['user_id'];
	doquery("SET @c = 0");
	$achievements = getall("SELECT @c:=@c+1 AS c, achievements.id, label, description FROM achievements INNER JOIN user_achievements ON achievements.id = user_achievements.achievement_id AND user_achievements.user_id = $user_id WHERE completed IS NOT NULL AND shown = 0");
	return $achievements;
	
}

function check_begin_page_achievements() {
	// user agent achievements
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($user_agent, ' Edge/') !== FALSE) award_achievement(90);

	// referer achievements
	$referer = $_SERVER['HTTP_REFERER'];
	if ($referer) {
		$url = parse_url($referer);
		$segments = array_reverse(explode('.', $url['host']) );
		$domain = $segments[1].".".$segments[0];
		
		// check
		if ($domain == 'google.com') award_achievement(37);
		if ($domain == 'bing.com') award_achievement(38);
		if ($domain == 'twitter.com') award_achievement(39);
		if ($domain == 'youtube.com') award_achievement(40);
		if ($domain == 'blogspot.com') award_achievement(41);
		if ($domain == 'ello.co' || $domain == 'mewe.com') award_achievement(42);
		if ($domain == 'jubii.dk') award_achievement(43);
		if ($domain == 'yahoo.com') award_achievement(46);
		if ($domain == 'facebook.com') award_achievement(58);
	}
	if (count((array)$_SESSION['user_achievements']) >= 10) award_achievement(63); // at least 10 achievements
	if (count((array)$_SESSION['user_achievements']) >= 20) award_achievement(64); // at least 20 achievements
	if (count((array)$_SESSION['user_achievements']) >= 30) award_achievement(65); // at least 30 achievements
	if (count((array)$_SESSION['user_achievements']) >= 40) award_achievement(66); // at least 40 achievements
	if (count((array)$_SESSION['user_achievements']) >= 50) award_achievement(67); // at least 50 achievements
	if (rand(1,1000) == 1000) award_achievement(78);
	if (rand(1,1000000) == 1000000) award_achievement(99);
	if ($_SERVER['REMOTE_ADDR'] == '185.26.63.24' || $_SERVER['REMOTE_ADDR'] == '87.57.104.210' || $_SERVER['REMOTE_ADDR'] == '87.57.30.70' || $_SERVER['REMOTE_ADDR'] == '85.27.148.12' || $_SERVER['REMOTE_ADDR'] == '85.27.148.13' || $_SERVER['REMOTE_ADDR'] == '185.26.63.8') award_achievement(86); // login from Fastaval Wifi or Hobro Idrætscenter Wifi or Danhostel

	$date = date("m-d");
	if ($date == '10-31') award_achievement(50); // halloween
	if ($date == '12-31' || $date == '01-01') award_achievement(15); // New year's eve
	if ($date == '12-24' || $date == '12-25') award_achievement(13); // Christmas

	if (!filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) { // access site through IPv6
		award_achievement(97);
	}

}

function achievements_shown() {
	$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : FALSE;
	if (!$user_id) return false;
	doquery("UPDATE user_achievements SET shown = 1 WHERE user_id = $user_id");
}

function create_login_url ($id,$site = "rpgforum") {
	$time = time();
	$salt = getsitesalt($site);
	if (!$salt) return false; // remote site not found
	$hash = md5($id.$time.$salt);
	$url = "login?id=$id&hash=$hash&time=$time&site=$site";
	return $url;
}

function getsitesalt ($site) {
	$salt = getone("SELECT salt FROM remotelogin WHERE site = '".$site."'");
	return $salt;
}

function getuserlog($user_id,$category,$data_id) {
	return getcol("SELECT type FROM userlog WHERE user_id = '$user_id' AND category = '$category' AND data_id = '$data_id'");

}

function adduserlog($user_id,$category,$data_id,$type) {
	if (!getone("SELECT 1 FROM userlog WHERE user_id = '$user_id' AND category = '$category' AND data_id = '$data_id' AND type = '$type'")) {
		doquery("INSERT INTO userlog (user_id, category, data_id, type, added) VALUES ('$user_id','$category','$data_id','$type', NOW())");
	}
}

function removeuserlog($user_id,$category,$data_id,$type) {
	doquery("DELETE FROM userlog WHERE user_id = '$user_id' AND category = '$category' AND data_id = '$data_id' AND type = '$type'");
}

function getdynamicscehtml($sce_id,$type,$active) {
	global $t;
	$scenariotext = [
		"read" => htmlspecialchars($t->getTemplateVars('_top_read_pt') ),
		"gmed" => htmlspecialchars($t->getTemplateVars('_top_gmed_pt') ),
		"played" => htmlspecialchars($t->getTemplateVars('_top_played_pt') ),
	];
	$span_id = "sce_".$sce_id."_".$type;
	$html = 
		"<span id=\"".$span_id."\">".
		"<a href=\"javascript:switchicon('".$span_id."','".($active?'remove':'add')."','sce',".$sce_id.",'".$type."')\">".
		"<img src=\"gfx/".$type."_".($active?'active':'passive').".jpg\" alt=\"".$scenariotext[$type]." ".($active?'active':'passive')."\" title=\"".$scenariotext[$type]."\" border=\"0\" />".
		"</a>".
		"</span>"
	;
	return $html;
}

function getdynamicconventhtml($sce_id,$type,$active) {
	global $t;
	$conventtext = [ "visited" => htmlspecialchars( $t->getTemplateVars('_top_visited_pt') ) ];
	$span_id = "convent_".$sce_id."_".$type;
	$html = 
		"<span id=\"".$span_id."\">".
		"<a href=\"javascript:switchicon('".$span_id."','".($active?'remove':'add')."','convent',".$sce_id.",'".$type."')\">".
		"<img src=\"gfx/".$type."_".($active?'active':'passive').".jpg\" alt=\"".$conventtext[$type]."\" title=\"".$conventtext[$type]."\" border=\"0\" />".
		"</a>".
		"</span>"
	;
	return $html;
}


// generic functions

function getcategorydir($category) {
	$paths = array(
		"sce" => "scenario",
		"convent" => "convent",
		"conset" => "conset"
	);
	return $paths[$category];
}

function getimageifexists($id, $category_dir) {
	$path = "gfx/" . $category_dir . "/l_" . $id . ".jpg";
	if (file_exists($path) ) {
		return $path;
	}
	return FALSE;
}

function pubdateprint($datetime) {
	$pubdatetime = strtotime($datetime);
	$start = strftime('%e. %B, %R',$pubdatetime);
	if (date("Y-m-d",$pubdatetime) == date("Y-m-d")) {
		$start = "I dag, ".date("H:i",$pubdatetime);
	} elseif (date("Y-m-d",$pubdatetime) == date("Y-m-d",strtotime("- 1 day")) ) {
		$start = "I går, ".date("H:i",$pubdatetime);
	} elseif (date("Y-m-d",$pubdatetime) == date("Y-m-d",strtotime("- 2 days")) ) {
		$start = "I forgårs, ".date("H:i",$pubdatetime);
	} elseif (date("Y", $pubdatetime) != date("Y") ) {
		$start = strftime('%e. %B %Y, %R',$pubdatetime);
	} else {
		$start = strftime('%e. %B, %R',$pubdatetime);
	}
	return $start;
}


function birthlong ($date, $day = FALSE) {
	$time = strtotime($date);
	$date_format = strftime('%e. %B %Y',$time);
	return $date_format;
}

function birthshort ($dato) {
  $birth = explode("-",$dato);
  $foedt = intval($birth[2]) . "/" . intval($birth[1]) . " " . $birth[0];
  return $foedt;
}

function birthage ($dato, $death = "") {
	$birth = explode("-",$dato);
	if ($death) {
		$death = explode("-",$death);
	}
	$date_y = ($death ? $death[0] : date("Y") );
	$date_md = ($death ? $death[1] . "-" . $death[2] : date("m-d") );
	$age = $date_y - $birth[0];
	if ($date_md < $birth[1]."-".$birth[2]) $age--;
	return $age;
}

function shortweekday ($dato) {
	$week = array("Søn","Man","Tir","Ons","Tor","Fre","Lør");
	list($my, $mm, $md) = explode("-",$dato);
	$weekday = $week[date("w",mktime(0,0,0,$mm,$md,$my))];
	return date("w",mktime(0,0,0,$mm,$md,$my));
}

function monthname ( $monthNo ) {
	$monthNo = (int) $monthNo;
	if ( $monthNo < 1 || $monthNo > 12) {
		return false;
	}
	$month = strftime('%B', mktime(0,0,0,$monthNo,10) );
	return $month;
}

function _textlink ($string, $absolute_url = 0) {
	$urlpart = "/find";
	if (preg_match("/^(c|s|p|cs|sys|t|tag)(\d*)\|(.*)$/i",$string,$regs)) {
		$pref = $regs[1];
		$data_id = $regs[2];
		$text = $regs[3];
		switch ($pref) {
			case "c": $cat = "convent"; $class = "con"; $search = "con"; break;
			case "s": $cat = "sce"; $class = "scenarie"; $search = "sce"; break;
			case "p": $cat = "aut"; $class = "person"; $search = "aut"; break;
			case "cs": $cat = "conset"; $class = "con"; $search = "conset"; break;
			case "sys": $cat = "sys"; $class = "system"; $search = "sys"; break;
			case "t": 
			case "tag": $cat = "tag"; $class = "tag"; $search = "tag"; break;
		}
		if ($data_id) {
			$link = getdatalink($cat,$data_id);
			if ($absolute_url) $link = "https://alexandria.dk".$link;
			$html = "<a href=\"$link\" class=\"$class\">$text</a>";
			return $html;
		}
		if ($cat == "tag") {
			$html = "<a href=\"data?tag=" . rawurlencode($text) . "\" class=\"tag\">" . htmlspecialchars($text) . "</a>";
			return $html;
		}
		$html = "<a href=\"/find?find=".rawurlencode($text)."&amp;cat=$search\" class=\"$class\">$text</a>";
		return $html;
	}
	$text = "<a href=\"$urlpart?find=".rawurlencode($string)."\">$string</a>";
	return $text;	
}

function textlinks ($text,$absolute_url = 0) {
# :TODO: absolute_url bliver *ikke* ført med over, giver Notice
	$text = preg_replace_callback(
		'_\[\[\[([^]]+)\]\]\]_',
		function ($matches) {
			return _textlink($matches[1],$absolute_url);
		},
		$text
	);
	return $text;
}

function triviabullet ($fact) {
	$html = "<li>".textlinks(htmlspecialchars($fact))."</li>\n";
	return $html;
}

function datalink ($url, $description) {
	$link = textlinks(htmlspecialchars($description)).": \n<a href=\"".htmlspecialchars($url)."\" title=\"".htmlspecialchars($description)."\">".htmlspecialchars($url)."</a>";
	return $link;
}

function nicedateset ($begin, $end) {
	if ( ($begin && $begin != "0000-00-00") || ($end && $end != "0000-00-00") ) {
		list($y1,$m1,$d1) = explode("-",$begin);
		list($y2,$m2,$d2) = explode("-",$end);
		
		if ($m1 == "00" && $d1 == "00") {
			$out = sprintf("%d",$y1);
		} elseif ($d1 == "00") {
			$out = sprintf("%s %d", monthname( intval($m1) ), $y1);
		} elseif (!$end || ($begin == $end)) {
			$out = sprintf("%d. %s %d", $d1, monthname ( intval($m1) ), $y1);
		} elseif (!$begin) {
			$out = sprintf("%d. %s %d", $d2, monthname ( intval($m2) ), $y2);
		} elseif ($y1 == $y2 && $m1 == $m2) {
			$out = sprintf("%d. - %d. %s %d", $d1, $d2, monthname( intval($m2) ), $y2);
		} elseif ($y1 == $y2) {
			$out = sprintf("%d. %s - %d. %s %d", $d1, monthname( intval($m1) ), $d2, monthname( intval($m2) ), $y2);
		} else {
			$out = sprintf("%d. %s %d - %d. %s %d", $d1, monthname( intval($m1) ), $y1, $d2, monthname( intval($m2) ), $y2);
		}
	}
	return $out;
}

function getuserlogoptions($type) {
	if ($type == 'boardgame') {
		return [ 'read', NULL, 'played' ];
	} elseif ($type == 'scenario') {
		return [ 'read', 'gmed', 'played' ];
	} else {
		return [ NULL, NULL, NULL ];
	}
}

function getalluserentries($category, $data_id) {
	$result = [];
	if ($category == 'sce') {
		$result = getrow("SELECT SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played FROM userlog WHERE category = 'sce' AND data_id = " . (int) $data_id );
	} elseif ($category == 'convent') {
		$result = getrow("SELECT SUM(type = 'visited') AS `visited` FROM userlog WHERE category = 'convent' AND data_id = " . (int) $data_id );

	}
	return $result;
}

function getdataurl ($cat, $data_id, $text, $admin = FALSE) {
	$link = getdatalink($cat, $data_id, $admin);

	switch ($cat) {
		case 'aut':
		$css = "person";
		break;
	
		case 'sce':
		$css = "scenarie";
		break;
	
		case 'conset':
		$css = "con";
		break;

		case 'sys':
		$css = "system";
		break;
	
		case 'convent':
		$css = "con";
		break;
	
		default:
		case 'aut':
		$css = "person";
		break;
	}

	$html = "<a href=\"$link\" class=\"$css\">".htmlspecialchars($text)."</a>";

	return $html;
}

function getrecentlog($limit = 10) {
	$limit = (int) $limit;
	$query = "SELECT data_id, category, time, user, note FROM log ORDER BY log.id DESC LIMIT $limit";
	$log = getall($query);
	foreach($log AS &$row) {
		$row['name'] = getentry($row['category'], $row['data_id']);	
		$row['link'] = getdatalink($row['category'], $row['data_id']);
		$row['linkhtml'] = getdataurl($row['category'], $row['data_id'], $row['name']);
		$row['pubtime'] = pubdateprint($row['time']);
	}

	return $log;

}


function getdatalink ($cat, $data_id, $admin = FALSE) {

	switch ($cat) {
		case 'aut':
		$value = ($admin ? "/adm/person.php?person=$data_id" : "data?person=$data_id");
		break;
	
		case 'sce':
		$value = ($admin ? "/adm/scenarie.php?scenarie=$data_id" : "data?scenarie=$data_id");
		break;
	
		case 'conset':
		$value = ($admin ? "/adm/conset.php?conset=$data_id" : "data?conset=$data_id");
		break;

		case 'sys':
		$value = ($admin ? "/adm/system.php?system=$data_id" : "data?system=$data_id");
		break;
	
		case 'convent':
		$value = ($admin ? "/adm/convent.php?con=$data_id" : "data?con=$data_id");
		break;
	
		case 'tag':
		$tag = getone("SELECT tag FROM tag WHERE id = $data_id");
		$value = ($admin ? "/adm/tag.php?tag_id=$data_id" : "data?tag=" . rawurlencode($tag) );
		break;
	
		default:
		case 'aut':
		$value = ($admin ? "/adm/person.php?person=$data_id" : "data?person=$data_id");
		break;
	}

	return $value;
}

function getaliaslist ($data_id, $cat) {
	$aliases = array();
	$q = getcol("SELECT label FROM alias WHERE data_id = '$data_id' AND category = '$cat' AND visible = 1");
	foreach($q AS $label) {
		$aliases[] = $label;
	}
	if ($aliases) {
		$aliaslist = join(', ',$aliases);
	} else {
		$aliaslist = '';
	}
	return $aliaslist;
}

function getfilelist ($data_id, $cat) {
	$files = getall("SELECT filename, description FROM files WHERE data_id = '$data_id' AND category = '$cat' AND downloadable = 1 ORDER BY id");
	foreach($files AS $id => $file) {
		$path = ALEXFILES.getcategorydir($cat).'/'.$data_id.'/'.$file['filename'];
		$files[$id]['path'] = $path;
		$files[$id]['extension'] = strtolower(substr(strrchr($file['filename'], "."), 1));
		if (file_exists($path)) {
			$files[$id]['filesize'] = filesize($path);
			$files[$id]['filesizetext'] = number_format(max(0.1,round($files[$id]['filesize']/1024/1024*10)/10),1,',','.');
		}
	}
	return $files;
}

function gettrivialist ($data_id, $cat) {
	$trivialist = "";
	$q = getall("SELECT fact FROM trivia WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id");
	foreach($q AS $rs) {
		$trivialist .= triviabullet($rs['fact']);
	}
	return $trivialist;
}

function getlinklist ($data_id, $cat) {
	$linklist = "";
	$q = getall("SELECT url, description FROM links WHERE data_id = '$data_id' AND category = '$cat' ORDER BY id");
	foreach($q AS $rs) {
		$linklist .= datalink($rs['url'],$rs['description']);
		$linklist .= "<br />\n";
	}
	return $linklist;
}

function gettaglist ($data_id, $cat) {
	$data_id = (int) $data_id;
	$tags = getcolid("SELECT tags.id, tags.tag, c.count FROM tags INNER JOIN (SELECT COUNT(*) AS count, tag FROM tags GROUP BY tag) c ON tags.tag = c.tag WHERE tags.sce_id = $data_id ORDER BY c.count DESC");
	return $tags;
}

function getalltags ($count = FALSE) {
	if (!$count) {
		$tags = getcol("SELECT DISTINCT tag FROM tags ORDER BY tag");
	} else {
		$tags = getall("SELECT COUNT(*) AS count, tag FROM tags GROUP BY tag ORDER BY tag");
	}
	return $tags;
}


function getorganizerlist ($data_id, $category, $order = FALSE) {
	if (!$order) {
		$order = "a.role";
	}
	$organizerlist = [];
	if ($category == 'convent') {
		$organizerlist = getall("SELECT a.id, CONCAT(b.firstname, ' ', b.surname) AS name, a.aut_id, a.aut_extra, a.role FROM acrel a LEFT JOIN aut b ON a.aut_id = b.id WHERE a.convent_id = '$data_id' ORDER BY $order");
	} elseif ($category == 'aut') {
		$organizerlist = getall("SELECT a.id, a.convent_id, a.role, b.name, b.year FROM acrel a INNER JOIN convent b ON a.convent_id = b.id WHERE a.aut_id = '$data_id' ORDER BY b.year, b.begin, b.end, a.id");

	}
	return $organizerlist;
}


function getnexteventstable () { // both cons and scenarios
	// All cons that haven't ended (or begun) yet - manual CURDATE due to querycache
	// $r = getall("SELECT id, name, year, begin, end FROM convent WHERE end >= '".date("Y-m-d")."' ORDER BY begin, end, name");

	// :TODO: Currently three queries are performed for "all", "only cons" and "only scenarios". This could be optimized into a single query.

	$r = getall("
		(SELECT 'convent' AS type, convent.id, convent.name, convent.year, convent.description, begin, end, place, conset_id, conset.name AS cname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE end >= '" . date("Y-m-d") . "')
		UNION
		(SELECT 'sce' AS type, sce.id, sce.title AS name, YEAR(scerun.begin) AS year, sce.description, scerun.begin, scerun.end, scerun.location, sce.id AS conset_id, sce.title AS cname FROM scerun INNER JOIN sce ON scerun.sce_id = sce.id WHERE scerun.end >= '" . date("Y-m-d") . "' AND cancelled = 0)
		ORDER BY begin, end, name
	");

	$calout = '<table class="tableoverview" id="eventsall">' . parseupcomingevents($r) . '</table>' . PHP_EOL;

	$r = getall("
		(SELECT 'convent' AS type, convent.id, convent.name, convent.year, convent.description, begin, end, place, conset_id, conset.name AS cname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE end >= '" . date("Y-m-d") . "')
		ORDER BY begin, end, name
	");

	$calout .= '<table class="tableoverview" id="eventsconvent" style="display: none;">' . parseupcomingevents($r) . '</table>' . PHP_EOL;

	$r = getall("
		(SELECT 'sce' AS type, sce.id, sce.title AS name, YEAR(scerun.begin) AS year, sce.description, scerun.begin, scerun.end, scerun.location, sce.id AS conset_id, sce.title AS cname FROM scerun INNER JOIN sce ON scerun.sce_id = sce.id WHERE scerun.end >= '" . date("Y-m-d") . "' AND cancelled = 0)
		ORDER BY begin, end, name
	");

	$calout .= '<table class="tableoverview" id="eventsscenario" style="display: none;">' . parseupcomingevents($r) . '</table>' . PHP_EOL;

	return $calout;
}

function parseupcomingevents($eventset) {
	$calout = "";
	$lastbegin = "";
	foreach($eventset AS $nextevent) {
		list($type, $id, $name, $year, $description, $begin, $end) = $nextevent;
		if ($end == '0000-00-00' || !$end) {
			$end = $begin;
		}
		$beginpart = substr($begin,0,7);
		if ($lastbegin != $beginpart) {
			$lastbegin = $beginpart;
			list($conyear,$conmonth) = explode("-",$beginpart);
			$calout .= "<tr><th colspan=\"2\">".ucfirst(monthname($conmonth))." $conyear</th></tr>\n";
		}
		$coninfo = nicedateset($begin,$end);
		if ($begin == $end) {
			$daypart = intval(substr($begin,8,2)).": ";
		} else {
			$daypart = intval(substr($begin,8,2)).".-".intval(substr($end,8,2)).": ";
		}
		$calout .= "<tr>\n<td>$daypart</td>\n";
		if ($type == 'convent') {
			$calout .= "<td><a href=\"data?con=$id\" title=\"$coninfo\" class=\"con\">$name</a></td>\n";
		} elseif ($type == 'sce') {
			$calout .= "<td><a href=\"data?scenarie=$id\" title=\"$coninfo\" class=\"scenarie\">$name</a></td>\n";
		}
		$calout .= "</tr>\n";
	}
	return $calout;

}


// MySQL-kald:

function getentry ($cat, $data_id, $with_category = FALSE) {

	switch ($cat) {
		case 'aut':
		$value = "CONCAT(firstname,' ',surname)";
		$fullcat = "Person";
		break;
	
		case 'sce':
		$value = "title";
		$fullcat = "Scenarie";
		break;
	
		case 'conset':
		$value = "name";
		$fullcat = "Con-serie";
		break;

		case 'sys':
		$value = "name";
		$fullcat = "System";
		break;
	
		case 'tag':
		$value = "tag";
		$fullcat = "Tag";
		break;
	
		case 'convent':
		$value = "CONCAT(name,' (',COALESCE(year,'?'),')')";
		$fullcat = "Con";
		break;
	
		default:
	}

	if ($value) {
		$label = getone("SELECT $value FROM $cat WHERE id = '$data_id'");
	}

	if ($label && $with_category) {
		return "$fullcat - $label";
	} elseif ($label) {
		return $label;
	}

	return false;
}

/*
 * Generic SQL
 */

function getone ($query) {
	global $dblink;
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	list($data) = mysqli_fetch_row($result);
	return $data;
}

function getrow ($query) {
	global $dblink;
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	$data = mysqli_fetch_array($result);
	return $data;
}

function getcol ($query) {
	global $dblink;
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	$data = array();
	while (list($field) = mysqli_fetch_row($result)) {
		$data[] = $field;
	}
	return $data;
}

function getcolid ($query) {
	global $dblink;
	// Fetch all rows. First field is key, second is value
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	$data = array();
	while (list($id,$field) = mysqli_fetch_row($result)) {
		$data[$id] = $field;
	}
	return $data;
}

function getall ($query, $array = TRUE) {
	global $dblink;
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	$data = array();
	if ($array) {
		while ($row = mysqli_fetch_array($result)) {
			$data[] = $row;
		}
	} else {
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = $row;
		}
	}

	return $data;
}

function doquery ($query) {
	global $dblink;
	$result = mysqli_query($dblink, $query);
	if (!$result) {
		trigger_error("Error in query: $query - error: ".mysqli_error($dblink)."<br>\n",E_USER_WARNING);
		return false;
	}
	if ($id = mysqli_insert_id($dblink)) {
		return $id;
	}
	return true;
}

function dbesc ($string) {
	global $dblink;
	return mysqli_real_escape_string($dblink,$string);
}

function dberror () {
	global $dblink;
	return mysqli_error($dblink);
}

function dbid () {
	global $dblink;
	return mysqli_insert_id($dblink);
}

/*
 * Image functions
 */

function image_rescale_save($source, $destination, $max_width, $max_height) {
	$src_image = @imagecreatefromjpeg($source);
	if (!$src_image) return false;
	$src_width = imagesx($src_image);
	$src_height = imagesy($src_image);
	$prop = min(
		($max_width/$src_width),
		($max_height/$src_height),
		1
	);
	$dst_width = max(1,floor($src_width*$prop));
	$dst_height = max(1,floor($src_height*$prop));
	$dst_image = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($dst_image,$src_image,0,0,0,0,$dst_width,$dst_height,$src_width,$src_height);
	@imagejpeg($dst_image,$destination);
	return $true;
}

?>
