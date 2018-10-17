<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			{if $typename != ""}{$typename|escape} - {/if}{if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
		</title>
		<link rel="stylesheet" type="text/css" href="alexstyle.css" />
		<link rel="alternate" type="application/rss+xml" title="Alexandria" href="http://alexandria.dk/rss.php" />
		<script type="text/javascript" src="quicksearch.js"></script>
    	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script src="js/alexandria.afd.2010.js" type="text/javascript"></script>
		<link rel="SHORTCUT ICON" href="favicon.ico" />
		<meta name="robots" content="noindex, follow" />
	</head>

	<body style="margin: 0px; padding: 0px;">

		{* Logo: Traditional Arabic, 30px, bold *}
		<div id="leftmenu">
			<p style="margin: 2px 2px 20px 10px;">
				<a href="./"><img src="gfx/texture_logo.gif" alt="Alexandria" title="Alexandria" style="border: 0px;"/></a>
			</p>
			<div class="leftmenucontent">
				<a href="oss">Om Alexandria</a><br />
				<a href="rettelser">Indsend rettelser</a><br />
				<a href="kontakt">Kontakt os</a><br />
				<br />
				<a href="findspec">Søg efter scenarie</a><br />
				<a href="statistik">Alexandria i tal</a><br />
				<br />
				<a href="calendar">Kalender</a><br />
				<a href="jostspil">Jost-spillet</a><br />
				<a href="feeds">Blog-feeds</a><br />
				<br />
				<a href="english">In English</a><br />
				{*
				<br />
				<a href="http://flattr.com/thing/1992/Rollespilsbiblioteket-Alexandria" target="_blank"><img src="http://api.flattr.com/button/button-compact-static-100x17.png" title="Flattr this" border="0" /></a>
				*}
			</div>

			<div class="leftmenucontent">
{if not $user_id}
				<a href="login?remote=rpgforum">Log ind [via RPGFORUM]</a><br />
				<a href="login?remote=liveforum">Log ind [via LiveForum]</a><br />
{else}
				Du er logget på som:<br /><span title="{$user_id} - {$user_name|escape}">{$user_name|truncate:20|escape}</span><br />
				(via {$user_site|strtoupper})<br />
				<span style="padding-left: 10px;"><a href="myhistory">Min oversigt</a></span><br />
				<span style="padding-left: 10px;"><a href="login?logout">Log ud</a></span><br />
	{if $user_admin}
				<br />
				<span style="padding-left: 10px;"><a href="adm/">Admin</a></span><br />
	{/if}
{/if}				
			</div>


{if $user_id}

	{if $type eq "sce"}
			<div class="leftmenucontent">
				Dette scenarie har jeg:<br /><br />
				<span id="data_seen">
				{if $user_seen}- Læst <a href="javascript:changedata('data_seen','remove','sce','{$id}','seen')">(skift)</a>{/if}
				{if not $user_seen}- Ikke læst <a href="javascript:changedata('data_seen','add','sce','{$id}','seen')">(skift)</a>{/if}
				</span><br />
				<span id="data_gmed">
				{if $user_gmed}- Kørt <a href="javascript:changedata('data_gmed','remove','sce','{$id}','gmed')">(skift)</a>{/if}
				{if not $user_gmed}- Ikke kørt <a href="javascript:changedata('data_gmed','add','sce','{$id}','gmed')">(skift)</a>{/if}
				</span><br />
				<span id="data_played">
				{if $user_played}- Spillet <a href="javascript:changedata('data_played','remove','sce','{$id}','played')">(skift)</a>{/if}
				{if not $user_played}- Ikke spillet <a href="javascript:changedata('data_played','add','sce','{$id}','played')">(skift)</a>{/if}
				</span>
			</div>

		{*
			<div class="leftmenucontent">
				Dette scenarie har jeg:<br /><br />
				<span id="data_seen">
				{$user_seen_html}
				</span>
				<span id="data_gmed">
				{$user_gmed_html}
				</span>
				<span id="data_played">
				{$user_played_html}
				</span>
			</div>
		*}
	{/if}

	{if $type eq "convent"}
			<div class="leftmenucontent">
				Denne kongres har jeg:<br /><br />
				<span id="data_visited">
				{if $user_visited}
				- Besøgt <a href="javascript:changedata('data_visited','remove','convent','{$id}','visited')">(skift)</a>
				{else}
				- Ikke besøgt <a href="javascript:changedata('data_visited','add','convent','{$id}','visited')">(skift)</a>
				{/if}
				</span>

			</div>
	{/if}

{/if}

		</div>

		<div id="topmenu">
			<form action="find" style="display: inline;">
				<div class="topmenublock" onclick="location.href='personer';">
					<a href="personer" class="person" style="text-decoration: none;">Personer</a>
				</div>
				<div class="topmenublock" onclick="location.href='scenarier';">
					<a href="scenarier" class="scenarie" style="text-decoration: none;">Scenarier</a>
				</div>
				<div class="topmenublock" onclick="location.href='cons';">
					<a href="cons" class="con" style="text-decoration: none;">Kongresser</a>
				</div>
				<div class="topmenublock" onclick="location.href='systemer';">
					<a href="systemer" class="system" style="text-decoration: none;">Systemer</a>
				</div>
				<div class="topmenublock" style="white-space: nowrap; width: 185px; padding-top: 2px; border: 0">
				{*
					<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="20" class="find" onkeyup="javascript:check_search(getElementById('ffind').value)" autocomplete="off" /></label>
				*}
					<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="20" class="find" /></label>
				</div>
			</form>
		</div>

<div id="resultbox">
</div>

{*
			<div class="topmenublock" style="background-color: #fff; white-space: nowrap; width: 185px; padding-top: 2px;">
				<form action="find" style="display: inline;">&nbsp;<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="20" class="find"  /></label></form>
			</div>

*}

{if $ad}
<div class="rightad">
<script type="text/javascript"><!--
google_ad_client = "pub-0751412748425167";
google_ad_width = 160;
google_ad_height = 600;
google_ad_format = "160x600_as";
google_ad_type = "text";
google_ad_channel ="";
google_color_border = "000000";
google_color_link = "880088";
google_color_bg = "FDFAED";
google_color_text = "000000";
google_color_url = "880088";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>
{/if}
