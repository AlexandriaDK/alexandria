<!DOCTYPE html>
<html lang="{$LANG|escape}">
	<head>
		<title>
			{if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
		</title>
<meta name="viewport" content="width=1024">
		<meta name="robots" content="index, follow" />
{if isset($ogimage) && $ogimage != ''}
		<meta property="og:image" content="https://alexandria.dk/{$ogimage}" />
{else}
		<meta property="og:image" content="https://alexandria.dk/gfx/alexandria_logo_og_crush.png" />
{/if}
		<meta property="fb:admins" content="745283070">
		<link rel="stylesheet" type="text/css" href="/alexstyle.css" />
		<link rel="stylesheet" type="text/css" href="/uistyle.css" />
		<link rel="alternate" type="application/rss+xml" title="Alexandria" href="https://alexandria.dk/rss.php" />
		<link rel="icon" type="image/png" href="/gfx/favicon_ti.png">
		<link rel="search" type="application/opensearchdescription+xml" title="Alexandria" href="/opensearch.xml" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
{if isset($URLLANG) }
{foreach $ALEXLANGUAGES as $altlanguage => $altlanguagelocalname}
{if $URLLANG != $altlanguage}
		<link rel="alternate" hreflang="{$altlanguage}" href="https://alexandria.dk/{$altlanguage}/{$BASEURI}" />
{/if}
{/foreach}
{/if}
		<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
		<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="/helper.js"></script>
		{if isset($json_tags) }
		<script>
		$(function() {
			$( ".newtag" ).autocomplete({
				source: 'ajax.php?type=tag',
				autoFocus: true,
				delay: 10
			});
		});
		</script>
		{/if}

		{if isset($todo_tabs) } 
		<script>
		$( function() {
			$( "#tabslist" ).tabs();
			$( "#tabsguide" ).tabs();
			$( "#tabsmissing" ).tabs();
		} );
		</script>
		{/if}

		{if isset($editmode)}
		<script>
		$(function() {
			$( ".peopletags" ).autocomplete({
				source: 'ajax.php?type=person&with_id=1',
				autoFocus: true,
				delay: 10,
				minLength: 3
			});
		});
		</script>
		{/if}

		{if isset($type) && $type == 'jostgame' }
		<script>
		$(function() {
			$( ".peopletags" ).autocomplete({
				source: 'ajax.php?type=person',
				autoFocus: true,
				delay: 10,
				minLength: 3
			});
		});
		</script>
		{/if}
		{if isset($type) && $type == 'locations'}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
<link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

		{/if}
	</head>

	<body>
		{* Logo: Traditional Arabic, 30px, bold *}
		<div id="{if not isset($lgbtmenu)}leftmenu{else}leftmenulgbtq{/if}">
			<p>
				<a href="./" accesskey="q"><img src="/gfx/texture_logo.gif" alt="Alexandria" title="Alexandria" width="151" height="28" id="alexandrialogo"></a>
			</p>
			<div class="leftmenucontent">
				<a href="about">{$_top_aboutalex}</a><br>
				<a href="todo">{$_top_helpalexandria}</a><br>
				<a href="rettelser">{$_top_submit}</a><br>
				<br>
				<a href="findspec">{$_top_searchgame}</a><br>
				<a href="tags">{$_top_tags}</a><br>
				<a href="statistik">{$_top_statistics}</a><br>
				<br>
				<a href="locations">{$_top_locations}</a><br>
				<a href="calendar">{$_top_calendar}</a><br>
				<a href="feeds">{$_top_blogfeeds}</a><br>
				<a href="awards">{$_top_awards}</a><br>
				<a href="jostspil">{$_top_jostgame}</a><br>
				<br>
				<a href="kontakt">{$_top_contact}</a><br>
				<a href="privacy">{$_top_privacy}</a><br>
			</div>

{if ! isset($dberror) && ! isset($installation) }
			<div class="leftmenucontent">
{if ! isset($user_id)}
				<span class="menulogin">
				{$_top_login}:
				</span>
				<ul class="remotelogin">
				<li><a href="fblogin" accesskey="l">[Facebook]</a></li>
				<li><a href="../login/google/" accesskey="g">[Google]</a></li>
				<li><a href="../login/twitter/" accesskey="t">[Twitter]</a></li>
				<li><a href="../login/steam/" accesskey="e">[Steam]</a></li>
				<li><a href="../login/discord/" accesskey="d">[Discord]</a></li>
				</ul>
				<br>
{else}
				{$_top_loggedonas}:<br><span title="{$user_name|escape}">{$user_name|truncate:20|escape}</span><br>
				<br>
				<div class="mylinks">
				<a href="myhistory">{$_top_myoverview}</a><br>
	{if isset($user_editor)}
				<a href="profile">{$_top_profile}</a><br>
	{/if}
				<a href="logout">{$_top_logout}</a><br>
	{if isset($user_admin)}
				<br>
				<a href="adm/" accesskey="a">{$_top_admin}</a><br>
	{elseif isset($user_editor)}
				<br>
				<a href="adm/" accesskey="a">{$_top_editor}</a><br>
	{/if}
				</div>
{/if}				
			</div>


{if isset($user_id)}

	{if isset($type) && $type eq "game"}
			<div class="leftmenucontent">
				{if $boardgame}{$_top_dyn_boardgame}{else}{$_top_dyn_scenario}{/if}<br><br>
				<span id="data_read">
				{if $user_read}- {$_top_read_pt} <a href="javascript:changedata('data_read','remove','game','{$id}','read', '{$token}')">({$_switch})</a>{/if}
				{if not $user_read}- {$_top_not_read_pt} <a href="javascript:changedata('data_read','add','game','{$id}','read', '{$token}')">({$_switch})</a>{/if}
				</span><br>
				{if !$boardgame}
				<span id="data_gmed">
				{if $user_gmed}- {$_top_gmed_pt} <a href="javascript:changedata('data_gmed','remove','game','{$id}','gmed', '{$token}')">({$_switch})</a>{/if}
				{if not $user_gmed}- {$_top_not_gmed_pt} <a href="javascript:changedata('data_gmed','add','game','{$id}','gmed', '{$token}')">({$_switch})</a>{/if}
				</span><br>
				{/if}
				<span id="data_played">
				{if $user_played}- {$_top_played_pt} <a href="javascript:changedata('data_played','remove','game','{$id}','played', '{$token}')">({$_switch})</a>{/if}
				{if not $user_played}- {$_top_not_played_pt} <a href="javascript:changedata('data_played','add','game','{$id}','played', '{$token}')">({$_switch})</a>{/if}
				</span>
			</div>

		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				{$_top_popularity}:<br><br>

				{$_top_read_pt}: {$users_entries.read + 0} {$_users}<br>
				{if ! $boardgame}
				{$_top_gmed_pt}: {$users_entries.gmed + 0} {$_users}<br>
				{/if}
				{$_top_played_pt}: {$users_entries.played + 0} {$_users}
				<br><br>
				<a href="adm/userlog.php?category=game&data_id={$id}">{$_top_details}</a>
				
			</div>
		{/if}


	{/if}

	{if isset($type) && $type eq "convention"}
			<div class="leftmenucontent">
				{$_top_dyn_convention}<br><br>
				<span id="data_visited">
				{if $user_visited}
				- {$_top_visited_pt} <a href="javascript:changedata('data_visited','remove','convention','{$id}','visited', '{$token}')">({$_switch})</a>
				{else}
				- {$_top_not_visited_pt} <a href="javascript:changedata('data_visited','add','convention','{$id}','visited', '{$token}')">({$_switch})</a>
				{/if}
				</span>

			</div>
		{if $user_admin || $user_editor}
			<div class="leftmenucontent">
				{$_top_popularity}<br><br>

				{$_top_visited_pt}: {$users_entries.visited + 0} {$_users}
				<br><br>
				<a href="adm/userlog.php?category=convention&amp;data_id={$id}">{$_top_details}</a>
				
			</div>
		{/if}
	{/if}

	{if $user_editor && isset($recentlog) }
		<div class="leftmenucontent">
			{$_top_recentedits}:
			<div class="longblock">
			{foreach from=$recentlog item=$log}
			{$log.linkhtml}<br>
			<span class="noteindtast">
			{$log.note|escape}<br>
			{$log.pubtime}<br>
			{$_by} {$log.user|escape}<br>
			<br></span>
			{/foreach}
			</div>
			<a href="adm/showlog.php" accesskey="l">{$_top_alledits}</a>

		</div>
	{/if}

	{if $user_editor && isset($translations) }
		<div class="leftmenucontent">
			{$_top_translationprogress}:
			<br><br>
			{foreach from=$translations item=$translation}
			<a href="adm/language.php?setlang={$translation.isocode|rawurlencode}">{$translation.llanguage|ucfirst|escape}</a>: {$translation.percentagestring}<br>
			{/foreach}
		</div>
	{/if}

	{if isset($user_scenario_missing_players) && $user_scenario_missing_players }
			<div class="leftmenucontent">
				{$_top_help_sce_no|@nl2br}
				<br><br>
				{foreach from=$user_scenario_missing_players item=$usmc}
				<a href="data?scenarie={$usmc.id}" class="game">{$usmc.title|escape}</a><br>
				{/foreach}
				<br>
				{$_top_help_sce_no2|@nl2br}
			</div>
	{/if}

	{if isset($user_scenario_missing_tags) && $user_scenario_missing_tags }
			<div class="leftmenucontent">
				{$_top_help_sce_tag|@nl2br}
				<br><br>
				{foreach from=$user_scenario_missing_tags item=$usmt}
				<a href="data?scenarie={$usmt.id}" class="game">{$usmt.title|escape}</a><br>
				{/foreach}
				<br>
				{$_top_help_sce_tag2|@nl2br}
			</div>
	{/if}

{/if}

{if isset($URLLANG) }
			<div class="leftmenucontent selectlanguage">
			<img src="/gfx/icon_translator.svg" alt="Language icon">
			{$_chooselanguage}
			<br><br>
			{foreach $ALEXLANGUAGES as $altlanguage => $altlanguagelocalname}<a href="/{$altlanguage}/{$BASEURI}" hreflang="{$altlanguage}" title="{$altlanguagelocalname|escape}">{$altlanguage}</a>{if not $altlanguagelocalname@last} • {/if}{/foreach}
			</div>
{/if}

		</div>


		<nav>
		<div id="topmenu" itemscope itemtype="http://schema.org/WebSite">
			<meta itemprop="url" content="https://alexandria.dk/" />
			<form action="find" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
				{literal}
				<meta itemprop="target" content="https://alexandria.dk/find?find={find}"/>
				{/literal}
				<div class="topmenublock">
					<a href="personer" class="person">{$_persons|ucfirst}</a>
				</div>
				<div class="topmenublock">
					<a href="scenarier" class="game">{$_scenarios|ucfirst}</a>
				</div>
				<div class="topmenublock">
					<a href="boardgames" class="game">{$_boardgames|ucfirst}</a>
				</div>
				<div class="topmenublock">
					<a href="cons" class="con">{$_conventions|ucfirst}</a>
				</div>
				<div class="topmenublock">
					<a href="systemer" class="system">{$_rpgsystems|ucfirst}</a>
				</div>
				<div class="topmenublock">
					<a href="magazines" class="magazines">{$_top_magazines|ucfirst}</a>
				</div>
				<div class="topmenublockfind">
					<label for="ffind" accesskey="s">{$_search|ucfirst}: <input id="ffind" type="search" name="find" value="{if isset($find)}{$find|escape}{/if}" size="15" class="find" itemprop="query-input"></label>
				</div>
			</form>
{/if}
		</div>
		</nav>

<div id="resultbox">
</div>

<div class="clear"></div>
