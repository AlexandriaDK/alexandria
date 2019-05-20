{assign var="pagetitle" value="Alexandria in numbers"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Alexandria in numbers:
	</h2>

	<table class="tablestatlist">

	<tr>
	<td class="statleft">Most prolific authors:<br>
		{$stat_aut_active}
	</td>

	<td>Most exposed authors:<br>(how many times any of the author's<br>scenarios have been scheduled)<br>
		{$stat_aut_exp}
	</td>
	</tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	
	<tr>
	<td class="statleft">Authors who have co-written<br>with most other authors:<br>
		{$stat_aut_workwith}
	</td>
	
	<td>Most used RPG systems:<br>
		{$stat_sys_used}
	</td>
	</tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	
	<tr>
	<td class="statleft">Scenarios played at most conventions:<br>
		{$stat_sce_replay}
	</td>

	<td>Scenarios with most authors:<br>
		{$stat_sce_auts}
	</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>
	
	<tr>
	<td class="statleft">Conventions with most scenarios:<br>(incl. re-runs, excl. cancellations)<br>
		{$stat_con_sce}
	</td>
	
	<td>Conventions + new scenarios,<br>ordered by year:<br>
		{$stat_con_year}
	</td>
	</tr>
	
	</table>
	
</div>

{include file="end.tpl"}
