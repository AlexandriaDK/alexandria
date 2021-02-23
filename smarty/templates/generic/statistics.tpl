{assign var="pagetitle" value="$_stat_title"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_stat_title}
	</h2>

	<table class="tablestatlist">

	<tr>
	<td><span class="stathead">{$_stat_consbycountry|nl2br}</span><br>
		<table class="tablestat tablestartpad">
		{foreach from=$stat_con_country item=$scc}
		<tr><td class="statnumber">{$scc.placeout}</td><td>{$scc.localecountry}</td><td class="statnumber">{$scc.count} </td></tr>
		{/foreach}
		</table>
	</td>

	<td><span class="stathead">{$_stat_descriptionsbylanguage|nl2br}</span><br>
		<table class="tablestat tablestartpad">
		{foreach from=$stat_description_language item=$sdl}
		<tr><td class="statnumber">{$sdl.placeout}</td><td>{$sdl.localecountry|ucfirst}</td><td class="statnumber">{$sdl.count} </td></tr>
		{/foreach}
		</table>
	</td>

	</tr>
	
	<tr>	
	<td class="statleft"><span class="stathead">{$_stat_largestcons|nl2br}</span><br>
		{$stat_con_sce}
	</td>
	<td><span class="stathead">{$_stat_conscelist|nl2br}</span><br>
		<table class="tablestat tablestartpad">
		{foreach from=$stat_con_year item=$scy}
		<tr><td><a href="data?year={$scy.year}" class="con">{$scy.year|yearname}</a></td><td class="statnumber">{$scy.cons} </td><td>{if $scy.cons == 1}{$_convention}{else}{$_conventions}{/if}&nbsp;</td><td class="statnumber">{$scy.games}</td><td>{if $scy.games == 1}{$_game}{else}{$_games}{/if}</td></tr>
		{/foreach}
		</table>
	</td>
	</tr>


	<tr>
	<td class="statleft"><span class="stathead">{$_stat_mostprolific|nl2br}</span><br>
		{$stat_aut_active}
	</td>

	<td><span class="stathead">{$_stat_mostexposed|nl2br}</span>
		<br>
		{$stat_aut_exp}
	</td>
	</tr>
	
	<tr>
	<td class="statleft"><span class="stathead">{$_stat_cowritten|nl2br}</span><br>
		{$stat_aut_workwith}
	</td>
	
	<td><span class="stathead">{$_stat_mostsystem|nl2br}</span><br>
		{$stat_sys_used}
	</td>
	</tr>
	
	<tr>
	<td class="statleft"><span class="stathead">{$_stat_mostcons|nl2br}</span><br>
		{$stat_sce_replay}
	</td>

	<td><span class="stathead">{$_stat_mostauthors|nl2br}</span><br>
		{$stat_sce_auts}
	</td>
	</tr>
	
	</table>
	
</div>

{include file="end.tpl"}
