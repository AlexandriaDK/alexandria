<!DOCTYPE html>
<html>
	<head>
		<title>
			{if $typename != ""}{$typename|escape} - {/if}{if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="index, follow" />
{if $ogimage}
		<meta property="og:image" content="https://alexandria.dk/{$ogimage}" />
{/if}
		<link rel="stylesheet" type="text/css" href="alexstyle.css" />
		<link rel="alternate" type="application/rss+xml" title="Alexandria" href="https://alexandria.dk/rss.php" />
		<link rel="SHORTCUT ICON" href="favicon.ico" />
		<link rel="search" type="application/opensearchdescription+xml" title="Alexandria" href="/opensearch.xml" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="quicksearch.js"></script>
		<style>
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
		{if $json_alltags}
		<script>
		$(function() {
			var availableTags = {$json_alltags};
			$( ".newtag" ).autocomplete({
				source: availableTags,
				autoFocus: true,
				delay: 10
			});
		});
		</script>

		{/if}

		{if $editmode || $type == 'jostgame' }
		<script>
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

	<body>

		{* Logo: Traditional Arabic, 30px, bold *}
		<div id="leftmenu">
			<p>
				<a href="./"><img src="gfx/texture_logo.gif" alt="Alexandria" title="Alexandria" width="151" height="28"></a>
			</p>
			<div class="leftmenucontent">
				<a href="oss">Om Alexandria</a><br />
				<a href="rettelser">Indsend rettelser</a><br />
				<a href="kontakt">Kontakt os</a><br />
				<br />
				<a href="findspec">Søg efter scenarie</a><br />
				<a href="tags">Tags</a> (ny!)<br />
				<a href="statistik">Alexandria i tal</a><br />
				<br />
				<a href="calendar">Kalender</a><br />
				<a href="jostspil">Jost-spillet</a><br />
				<a href="feeds">Blog-feeds</a><br />
				<br />
				<a href="awards">Priser</a><br />
				<br />
				<a href="english">In English</a><br />
			</div>

			<div class="leftmenucontent">
{if not $user_id}
				<span class="menulogin">
				Log ind:
				</span>
				<ul class="remotelogin">
				<li><a href="fblogin" accesskey="l">[Facebook]</a></li>
				<li><a href="twitterlogin" accesskey="t">[Twitter]</a></li>
				<li><a href="steamlogin" accesskey="e">[Steam]</a></li>
				<li><a href="login/twitch/" accesskey="e">[Twitch]</a></li>
				</ul>
				<br />
{else}
				Du er logget på som:<br /><span title="{$user_id} - {$user_name|escape}">{$user_name|truncate:20|escape}</span><br />
				<br />
				<div class="mylinks">
				<a href="myhistory">Min oversigt</a><br />
	{if $user_editor}
				<a href="settings">Indstillinger</a><br />
	{/if}
				<a href="login?logout">Log ud</a><br />
	{if $user_admin}
				<br />
				<a href="adm/" accesskey="a">Admin</a><br />
	{elseif $user_editor}
				<br />
				<a href="adm/" accesskey="a">Redaktør</a><br />
	{/if}
				</div>
{/if}				
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

		<div id="topmenu" itemscope itemtype="http://schema.org/WebSite">
			<meta itemprop="url" content="https://alexandria.dk/" />
			<form action="find" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
				{literal}
				<meta itemprop="target" content="https://alexandria.dk/find?find={find}"/>
				{/literal}
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
				<div class="topmenublockfind">
					<label for="ffind" accesskey="s">Søg: <input id="ffind" type="text" name="find" value="{$find|escape}" size="15" class="find" itemprop="query-input" /></label>
				</div>
			</form>
		</div>

<div id="resultbox">
</div>

<div class="clear"></div>
