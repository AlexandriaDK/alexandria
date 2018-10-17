<!DOCTYPE html>
<html>
	<head>
		<title>
			WIP: {if $typename != ""}{$typename|escape} - {/if}{if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="index, follow" />
		{if $ogimage}
		<meta property="og:image" content="http://alexandria.dk/{$ogimage}" />
		{/if}
		<link rel="stylesheet" type="text/css" href="alexstyle.wip.css" />
		<link rel="alternate" type="application/rss+xml" title="Alexandria" href="http://alexandria.dk/rss.php" />
		<link rel="SHORTCUT ICON" href="favicon.ico" />
		<link rel="search" type="application/opensearchdescription+xml" title="Alexandria" href="/opensearch.xml" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script type="text/javascript" src="quicksearch.js"></script>
		<style type="text/css">
			.ui-autocomplete {
				max-height: 600px;
				max-width: 400px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				font-size: 0.9em;
				margin-right: 30px;
			}

			.ui-autocomplete .note {
				font-size: 0.7em;
			}
			
		</style>
		{if $editmode || $type == 'jostgame' }
		<script type="text/javascript">
		$(function() {
			var availableNames = {$json_people};
			$( ".tags" ).autocomplete({
				source: availableNames,
				autoFocus: true,
				delay: 10
			});
		});
		</script>
		{/if}

	</head>

	<body style="margin: 0px; padding: 0px;">

		<div style="display: block;">
			<form action="find" style="display: inline;">
				<input type="text" name="find" value="{$find|escape}" size="15" style="width: 100%; border: 0; margin: 0; padding: 0;" class="find" placeholder="Søg" />
			</form>
		</div>

		{* Logo: Traditional Arabic, 30px, bold *}
		<div id="leftmenu">
			<p style="margin: 2px 2px 20px 10px;">
				<a href="./"><img src="gfx/texture_logo.gif" alt="Alexandria" title="Alexandria" style="border: 0px;"/></a>
			</p>
			<div class="leftmenucontent">
{if not $user_id}
{*
				<a href="login?remote=liveforum">Log ind [via LiveForum]</a><br />
*}
				<a href="fblogin" accesskey="l">Log ind [via Facebook]</a><br />
{else}
				Du er logget på som:<br /><span title="{$user_id} - {$user_name|escape}">{$user_name|truncate:20|escape}</span><br />
{*
				(via {$user_site})<br />
*}
				<br />
{*
				<span style="font-size: 0.8em;">
				Hvis du tidligere har været logget på via RPGFORUM eller LiveForum,
				så findes dine gamle oplysninger stadigvæk. Det vil snart
				være muligt at hente dem frem igen.</span><br /><br />
*}
				<span style="padding-left: 10px;"><a href="myhistory">Min oversigt</a></span><br />
				<span style="padding-left: 10px;"><a href="login?logout">Log ud</a></span><br />
	{if $user_admin}
				<br />
				<span style="padding-left: 10px;"><a href="adm/" accesskey="a">Admin</a></span><br />
	{elseif $user_editor}
				<br />
				<span style="padding-left: 10px;"><a href="adm/" accesskey="a">Redaktør</a></span><br />
	{/if}
{/if}				
			</div>

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
				<br />
				<a href="awards">Priser</a><br />
				{*
				<br />
				<a href="http://flattr.com/thing/1992/Rollespilsbiblioteket-Alexandria" target="_blank"><img src="http://api.flattr.com/button/button-compact-static-100x17.png" title="Flattr this" border="0" /></a>
				*}
			</div>


{if $user_id}

	{if $type eq "sce"}
			<div class="leftmenucontent">
				Dette {if $boardgame}brætspil{else}scenarie{/if} har jeg:<br /><br />
				<span id="data_read">
				{if $user_read}- Læst <a href="javascript:changedata('data_read','remove','sce','{$id}','read')">(skift)</a>{/if}
				{if not $user_read}- Ikke læst <a href="javascript:changedata('data_read','add','sce','{$id}','read')">(skift)</a>{/if}
				</span><br />
				{if !$boardgame}
				<span id="data_gmed">
				{if $user_gmed}- Kørt <a href="javascript:changedata('data_gmed','remove','sce','{$id}','gmed')">(skift)</a>{/if}
				{if not $user_gmed}- Ikke kørt <a href="javascript:changedata('data_gmed','add','sce','{$id}','gmed')">(skift)</a>{/if}
				</span><br />
				{/if}
				<span id="data_played">
				{if $user_played}- Spillet <a href="javascript:changedata('data_played','remove','sce','{$id}','played')">(skift)</a>{/if}
				{if not $user_played}- Ikke spillet <a href="javascript:changedata('data_played','add','sce','{$id}','played')">(skift)</a>{/if}
				</span>
			</div>

		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				Popularitet:<br /><br />

				Læst: {$users_entries.read + 0} brugere<br />
				{if ! $boardgame}
				Kørt: {$users_entries.gmed + 0} brugere<br />
				{/if}
				Spillet: {$users_entries.played + 0} brugere
				<br><br>
				<a href="adm/userlog.php?category=sce&amp;data_id={$id}">Flere detaljer</a>
				
			</div>
		{/if}


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
		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				Popularitet:<br><br>

				Besøgt: {$users_entries.visited + 0} brugere
				<br><br>
				<a href="adm/userlog.php?category=convent&amp;data_id={$id}">Flere detaljer</a>
				
			</div>
		{/if}
	{/if}

	{if $user_editor && $recentlog}
		<div class="leftmenucontent">
			Seneste ændringer:
			<br><br>
			{section name=l loop=$recentlog}
			{$recentlog[l].linkhtml}<br />
			<span class="noteindtast">
			{$recentlog[l].note|escape}<br />
			{$recentlog[l].pubtime}<br />
			af {$recentlog[l].user|escape}<br />
			<br></span>
			{/section}
			<a href="adm/showlog.php" accesskey="l">Alle ændringer</a>

		</div>
	{/if}

	{if $user_scenario_missing_players}
			<div class="leftmenucontent">
				Hey - giv en hånd!
				<br /><br />
				Du har lavet scenarier, som vi mangler informationer om
				spillerantal på:
				<br /><br />
				{section name=s loop=$user_scenario_missing_players}
				<a href="data?scenarie={$user_scenario_missing_players[s].id}" class="scenarie">{$user_scenario_missing_players[s].title|escape}</a><br />
				{/section}
				<br />
				Klik ind på et scenarie og derefter på "Tilføj antal spillere" i toppen.
			</div>
	{/if}

{/if}


		</div>

		<div id="topmenu">
			<form action="find" style="display: inline;">
				<div class="topmenublock">
					<a href="personer" class="person">Personer</a>
				</div>
				<div class="topmenublock">
					<a href="scenarier" class="scenarie">Scenarier</a>
				</div>
				<div class="topmenublock">
					<a href="boardgames" class="scenarie">Brætspil</a>
				</div>
				<div class="topmenublock">
					<a href="cons" class="con">Kongresser</a>
				</div>
				<div class="topmenublock">
					<a href="systemer" class="system">Systemer</a>
				</div>
				<div class="topmenublock" style="white-space: nowrap; width: 185px; padding-top: 2px; border: 0">
				{*
					<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="20" class="find" onkeyup="javascript:check_search(getElementById('ffind').value)" autocomplete="off" /></label>
				*}
					<label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="15" style="width: 140px;" class="find" /></label>
				</div>
			</form>
		</div>

		<div id="categorymenu">
			<form action="find" style="display: inline;">
				<ul>
					<li><a href="personer" class="person">Personer</a></li>
					<li><a href="scenarier" class="scenarie">Scenarier</a></li>
					<li><a href="boardgames" class="scenarie">Brætspil</a></li>
					<li><a href="cons" class="con">Kongresser</a></li>
					<li><a href="systemer" class="system">Systemer</a></li>
					<li><label for="ffind" accesskey="s"><span style="text-decoration: underline;">S</span>øg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="15" style="width: 140px;" class="find" /></label></li>
				</ul>
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

