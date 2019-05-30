{assign var="pagetitle" value="Min side"}
{include file="head.tpl"}

<div id="contentwide">

		<h2 class="pagetitle">
			Min side:
		</h2>
		
		{if $content_addentry}
			<h3>Du kan ikke lige tilføje data i øjeblikket...</h3>
		{/if}
		
		<div id="kongres" style="float: left; margin-right: 30px;" >
		{if not $content_myconvents}
		<h3 class="parttitle">Kongresser:</h3>
		<p>
			Du har ikke tilføjet nogen kongresser på din liste.
		</p>
		{else}
		<h3 class="parttitle">{$_conventions|ucfirst}: ({$con_count})</h3>
		<table><tr><td></td><td><a href="myhistory?o=5">{$_conname|ucfirst}</a></td><td><a href="myhistory?o=7">{$_series|ucfirst}</a></td><td><a href="myhistory?o=6">{$_year|ucfirst}</a></td></tr>
		{$content_myconvents}
		</table>
		{/if}
		</div>
		
		<div id="scenarier" style="float: left; margin-right: 30px;" >
		{if not $content_myscenarios}
		<h3 class="parttitle">Scenarier:</h3>
		<p>
			Du har ikke tilføjet nogen scenarier på din liste.
		</p>
		{else}
		<div style="float: left;" >
		<h3 class="parttitle">{$_scenarios|ucfirst}: ({$game_count}: <span title="{$_top_read_pt}">{$game_read}</span>/<span title="{$_top_gmed_pt}">{$game_gmed}</span>/<span title="{$_top_played_pt}">{$game_played}</span>)</h3>
		<table><tr><td><a href="myhistory?o=1">{$_title|ucfirst}</a></td><td><a href="myhistory?o=2">{$_top_read_pt}</a></td><td><a href="myhistory?o=3">{$_top_gmed_pt}</a></td><td><a href="myhistory?o=4">{$_top_played_pt}</a></td></tr>
		{$content_myscenarios}
		</table>
		</div>
		{/if}
		</div>		

		<div id="achievements" style="float: left; margin-right: 30px; ">
		<h3>{$_achievements|ucfirst}: ({$achievement_count})</h3>
		{$content_personal_achievements}
		</div>

</div>

{include file="end.tpl"}
