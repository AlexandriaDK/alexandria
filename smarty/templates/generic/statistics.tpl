{assign var="pagetitle" value="$_stat_title"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_stat_title}
	</h2>

	<table class="tablestatlist">

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
	
	<tr>
	<td class="statleft"><span class="stathead">{$_stat_largestcons|nl2br}</span><br>
		{$stat_con_sce}
	</td>
	
	<td><span class="stathead">{$_stat_conscelist|nl2br}</span><br>
		<table class="tablestat">
		{section name=i loop=$stat_con_year}
		<tr><td><a href="data?year={$stat_con_year[i].year}" class="con">{$stat_con_year[i].year}</a>&nbsp;&nbsp;&nbsp;</td><td class="statnumber">{$stat_con_year[i].cons} </td><td>{if $stat_con_year[i].cons == 1}{$_convention}{else}{$_conventions}{/if}&nbsp;</td><td class="statnumber">{$stat_con_year[i].games}</td><td>{if $stat_con_year[i].games == 1}{$_game}{else}{$_games}{/if}</td></tr>
		{/section}
		</table>
	</td>
	</tr>
	
	</table>
	
</div>

{include file="end.tpl"}
