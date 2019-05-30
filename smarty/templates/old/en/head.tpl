<!DOCTYPE html>
<html>
	<head>
		<title>
			{if $typename != ""}{$typename|escape} - {/if}{if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
		</title>
<meta name="viewport" content="width=1024">
		<meta name="robots" content="index, follow" />
{if $ogimage}
		<meta property="og:image" content="https://alexandria.dk/{$ogimage}" />
{/if}
		<link rel="stylesheet" type="text/css" href="/alexstyle.css" />
		<link rel="alternate" type="application/rss+xml" title="Alexandria" href="https://alexandria.dk/rss.php" />
		<link rel="SHORTCUT ICON" href="favicon.ico" />
		<link rel="search" type="application/opensearchdescription+xml" title="Alexandria" href="/opensearch.xml" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
		<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="/quicksearch.js"></script>
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
				<a href="oss">About Alexandria</a><br>
				<a href="rettelser">Submit corrections</a><br>
				<a href="kontakt">Contact us</a><br>
				<br>
				<a href="findspec">Search for a game</a><br>
				<a href="tags">Tags</a><br>
				<a href="statistik">Alexandria in numbers</a><br>
				<br>
				<a href="calendar">Calendar</a><br>
				<a href="jostspil">The Jost Game</a><br>
				<a href="feeds">Blog feeds</a><br>
				<br>
				<a href="awards">Awards</a><br>
				<br>
				<a href="english">In English</a><br>
			</div>

			<div class="leftmenucontent">
{if not $user_id}
				<span class="menulogin">
				Login:
				</span>
				<ul class="remotelogin">
				<li><a href="fblogin" accesskey="l">[Facebook]</a></li>
				<li><a href="twitterlogin" accesskey="t">[Twitter]</a></li>
				<li><a href="steamlogin" accesskey="e">[Steam]</a></li>
				<li><a href="login/twitch/" accesskey="e">[Twitch]</a></li>
				</ul>
				<br>
{else}
				You are logged in as:<br><span title="{$user_id} - {$user_name|escape}">{$user_name|truncate:20|escape}</span><br>
				<br>
				<div class="mylinks">
				<a href="myhistory">My page</a><br>
	{if $user_editor}
				<a href="settings">Settings</a><br>
	{/if}
				<a href="login?logout">Logout</a><br>
	{if $user_admin}
				<br>
				<a href="/adm/" accesskey="a">Admin</a><br>
	{elseif $user_editor}
				<br>
				<a href="/adm/" accesskey="a">Editor</a><br>
	{/if}
				</div>
{/if}				
			</div>


{if $user_id}

	{if $type eq "sce"}
			<div class="leftmenucontent">
				I have<br><br>
				<span id="data_read">
				{if $user_read}- Read <a href="javascript:changedata('data_read','remove','sce','{$id}','read')">(switch)</a>{/if}
				{if not $user_read}- Not read <a href="javascript:changedata('data_read','add','sce','{$id}','read')">(switch)</a>{/if}
				</span><br>
				{if !$boardgame}
				<span id="data_gmed">
				{if $user_gmed}- GM'ed <a href="javascript:changedata('data_gmed','remove','sce','{$id}','gmed')">(switch)</a>{/if}
				{if not $user_gmed}- Not GM'ed <a href="javascript:changedata('data_gmed','add','sce','{$id}','gmed')">(switch)</a>{/if}
				</span><br>
				{/if}
				<span id="data_played">
				{if $user_played}- Played <a href="javascript:changedata('data_played','remove','sce','{$id}','played')">(switch)</a>{/if}
				{if not $user_played}- Not played <a href="javascript:changedata('data_played','add','sce','{$id}','played')">(switch)</a>{/if}
				<br><br>this {if $boardgame}board game{else}scenario{/if}.
				</span>
			</div>

		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				Popularity:<br><br>

				Read: {$users_entries.read + 0} users<br>
				{if ! $boardgame}
				GM'ed: {$users_entries.gmed + 0} users<br>
				{/if}
				Played: {$users_entries.played + 0} users
				<br><br>
				<a href="/adm/userlog.php?category=sce&amp;data_id={$id}">More details</a>
			</div>
		{/if}


	{/if}

	{if $type eq "convent"}
			<div class="leftmenucontent">
				I have<br><br>
				<span id="data_visited">
				{if $user_visited}
				- Visited <a href="javascript:changedata('data_visited','remove','convent','{$id}','visited')">(switch)</a>
				{else}
				- Not visited <a href="javascript:changedata('data_visited','add','convent','{$id}','visited')">(switch)</a>
				{/if}
				<br><br>
				this convention.
				</span>

			</div>
		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				Popularity:<br><br>

				Visited: {$users_entries.visited + 0} users
				<br><br>
				<a href="adm/userlog.php?category=convent&amp;data_id={$id}">More details</a>
				
			</div>
		{/if}
	{/if}

	{if $user_editor && $recentlog}
		<div class="leftmenucontent">
			Recent updates:
			<br><br>
			{section name=l loop=$recentlog}
			{$recentlog[l].linkhtml}<br>
			<span class="noteindtast">
			{$recentlog[l].note|escape}<br>
			{$recentlog[l].pubtime}<br>
			af {$recentlog[l].user|escape}<br>
			<br></span>
			{/section}
			<a href="/adm/showlog.php" accesskey="l">All updates</a>

		</div>
	{/if}

	{if $user_scenario_missing_players}
			<div class="leftmenucontent">
				Hey - help us out!
				<br><br>
				You have written scenarios for which we lack information about
				participants:
				<br><br>
				{section name=s loop=$user_scenario_missing_players}
				<a href="data?scenarie={$user_scenario_missing_players[s].id}" class="scenarie">{$user_scenario_missing_players[s].title|escape}</a><br>
				{/section}
				<br>
				Visit a scenario and click on "Add number of participants".
			</div>
	{/if}

	{if $user_scenario_missing_tags}
			<div class="leftmenucontent">
				Hey - give a tag!
				<br><br>
				You have written scenarios for which we lack tags:
				<br><br>
				{section name=s loop=$user_scenario_missing_tags}
				<a href="data?scenarie={$user_scenario_missing_tags[s].id}" class="scenarie">{$user_scenario_missing_tags[s].title|escape}</a><br>
				{/section}
				<br>
				Tags are keywords which makes it easier for visitors to find your game.
				<br><br>
				Visit a scenario and click on the "+" sign at the top right. 
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
					<a href="personer" class="person">People</a>
				</div>
				<div class="topmenublock">
					<a href="scenarier" class="scenarie">Scenarios</a>
				</div>
				<div class="topmenublock">
					<a href="boardgames" class="scenarie">Board games</a>
				</div>
				<div class="topmenublock">
					<a href="cons" class="con">Conventions</a>
				</div>
				<div class="topmenublock">
					<a href="systemer" class="system">RPG systems</a>
				</div>
				<div class="topmenublockfind">
					<label for="ffind" accesskey="s">Search: <input id="ffind" type="text" name="find" value="{$find|escape}" size="15" class="find" itemprop="query-input"></label>
				</div>
			</form>
		</div>

<div id="resultbox">
</div>

<div class="clear"></div>
