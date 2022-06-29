{assign var="pagetitle" value="$_stat_title"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_stat_title}
	</h2>

	<table class="tablestatlist">

	<tr>
	<td>
		<table class="tablestat tablestartpad">
		<thead>
		<tr><th colspan="3">{$_stat_consbycountry|nl2br}</th></tr>
		</thead>
		<tbody>
		{foreach from=$stat_con_country item=$scc}
		<tr><td class="statnumber">{$scc.placeout}</td><td>{$scc.localecountry}</td><td class="statnumber">{$scc.count|nicenumber} </td></tr>
		{/foreach}
		</tbody>
		</table>
	</td>

	<td>
		<table class="tablestat tablestartpad">
		<tr><th colspan="3">{$_stat_runsbycountry|nl2br}</th></tr>
		{foreach from=$stat_run_country item=$src}
		<tr><td class="statnumber">{$src.placeout}</td><td>{$src.localecountry}</td><td class="statnumber">{$src.count|nicenumber} </td></tr>
		{/foreach}
		</table>
	</td>

	<td>
		<table class="tablestat tablestartpad">
		<tr><th colspan="3">{$_stat_descriptionsbylanguage|nl2br}</th></tr>
		{foreach from=$stat_description_language item=$sdl}
		<tr><td class="statnumber">{$sdl.placeout}</td><td>{$sdl.localecountry|ucfirst}</td><td class="statnumber">{$sdl.count|nicenumber} </td></tr>
		{/foreach}
		</table>
	</td>
	</tr>
	
	<tr>	
	<td class="statleft"><span class="stathead">{$_stat_largestcons|nl2br}</span><br>
		{$stat_con_game}
	</td>

	<td>
		<table class="tablestat tablestartpad">
		<tr><th colspan="3">{$_stat_conscelist|nl2br}</th></tr>
		{foreach from=$stat_con_year item=$scy}
		<tr><td><a href="data?year={$scy.year}" class="con">{$scy.year|yearname}</a></td><td class="statnumber">{$scy.cons|nicenumber} </td><td>{if $scy.cons == 1}{$_convention}{else}{$_conventions}{/if}&nbsp;</td><td class="statnumber">{$scy.games|nicenumber}</td><td>{if $scy.games == 1}{$_game}{else}{$_games}{/if}</td></tr>
		{/foreach}
		</table>
	</td>
	</tr>


	<tr>
	<td class="statleft"><span class="stathead">{$_stat_mostprolific|nl2br}</span><br>
		{$stat_person_active}
	</td>

	<td><span class="stathead">{$_stat_mostexposed|nl2br}</span>
		<br>
		{$stat_person_exp}
	</td>

	<td class="statleft"><span class="stathead">{$_stat_cowritten|nl2br}</span><br>
		{$stat_person_workwith}
	</td>
	</tr>
	
	<tr>
	<td><span class="stathead">{$_stat_mostsystem|nl2br}</span><br>
		{$stat_gamesystem_used}
	</td>

	<td class="statleft"><span class="stathead">{$_stat_mostcons|nl2br}</span><br>
		{$stat_game_replay}
	</td>

	<td><span class="stathead">{$_stat_mostauthors|nl2br}</span><br>
		{$stat_game_auts}
	</td>
	</tr>
	
	</table>
	
</div>

{include file="end.tpl"}
