<?php

$months = array("","januar","februar","marts","april","maj","juni","juli","august","september","oktober","november","december");

function sh ($tekst) {
	return stripslashes(htmlentities($tekst));
}

function topbar () {
	global $find;
	
	$html = '
		<table width="100%" cellspacing="0" cellpadding="0"><tr valign="top"><td align="center">
		<table cellspacing="0" cellpadding="2" style="border: solid 1px; border-color: black; position: center;">
			<tr valign="middle">
				<td class="topmenublock" style="background-color: #fbb;" onclick="location.href=\'personer\';"><a href="personer" class="person" style="text-decoration: none;">Personer</a></td>
				<td class="topmenublock" style="background-color: #ffb;" onclick="location.href=\'scenarier\';"><a href="scenarier" class="scenarie" style="text-decoration: none;">Scenarier</a></td>
				<td class="topmenublock" style="background-color: #bfb;" onclick="location.href=\'cons\';"><a href="cons" class="con" style="text-decoration: none;">Cons</a></td>
				<td class="topmenublock" style="background-color: #bbf;" onclick="location.href=\'systemer\';"><a href="systemer" class="system" style="text-decoration: none;">Systemer</a></td>
				<td style="background-color: #fff; text-align: center; white-space: nowrap;"><form action="find" style="margin-bottom: 0px;"><p style="display: inline;">&nbsp;<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="'.sh($_REQUEST['find']).'" size="20" class="find"  /></label></p></form></td>
			</tr>
		</table>
		</td></tr></table>
	';
	return $html;
// <!--				<td  class="topmenublock" style="background-color: #fbf;"><a href="/statistik" style="text-decoration: none;">Statistik</a></td>-->
}


function pagebegin ($title, $backurl = "") {

/*
//	$html = '<?xml version="1.0" encoding="iso-8859-1"?>'
// <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

// 				background: top left no-repeat url(\''.$backurl.'\') #ffffff;

*/


	$html = '<?xml version="1.0" encoding="iso-8859-1" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<title>
			'.htmlspecialchars($title).'
		</title>
		<link rel="stylesheet" type="text/css" href="/alexstyle.css" />
		<link rel="SHORTCUT ICON" href="/favicon.ico" />
		<style type="text/css">
			BODY {
				background: top left no-repeat #fff;
				margin-right: 0%;
			}
		</style>

	</head>

	<body>
		<p style="text-align: center;">
			<a href="/" style="text-decoration: none;">
				<img src="/gfx/alexandria-title.gif" alt="Overskrift: Alexandria" style="border: 0px;" />
			</a>
		</p>
	'.topbar().'
';

	return $html;
}

function pagehead ($title = "Alexandria") {
	$html = '
		<h2 class="pagetitle">
			'.$title.':
		</h2>
';

	return $html;
}

function centerstart ($size = 0) {
	$css = ($size = intval($size) ? 'style="width: '.$size.'px"' : '');
	$html = "\t<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"center\" valign=\"top\">\n";
	$html .= "\t<table border=\"0\" cellspacing=\"5\"><tr valign=\"top\"><td $css>\n";
	return $html;
}

function centerend () {
	$html = "\t</td></tr></table>\n\n";
	return $html;
}

function pageend () {
	$html = '
	</body>
</html>
';

	return $html;
}

function updatelink ($category, $data_id) {
	$html = "<br /><br /><a href=\"rettelser?cat=$category&amp;data_id=$data_id\"><span style=\"font-size: 75%;\"><i>Indsend rettelser for denne side</i></span></a>\n";
	if ($_SERVER['REMOTE_ADDR'] == "192.168.1.30" || $_SERVER['REMOTE_ADDR'] == "192.168.1.32" || $_SERVER['REMOTE_ADDR'] == "192.168.1.5") {
		$html .= " - <a href=\"".getdatalink($category, $data_id, TRUE)."\" accesskey=\"r\"><span style=\"font-size: 75%;\"><i>Ret data</i></span></a>\n";
	}
	return $html;
	
#	return "<p style=\"font-size: 75%;\"><a href=\"rettelser?cat=$category&amp;data_id=$data_id\"><i>Indsend rettelser for denne side</i></a>\n";
}

function getnews() {
	$result = getall("SELECT id, text, published, online FROM news WHERE online = 1 ORDER BY id DESC");
	return $result;
}

?>
