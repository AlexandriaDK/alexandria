{assign var="pagetitle" value="Statistik"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Alexandria i tal:
	</h2>

	<table border="0" cellspacing="0" cellpadding="1">

	<tr valign="top">
	<td class="statleft">Mest aktive forfattere:<br />
		{$stat_aut_active}
	</td>

	<td>Mest eksponerede forfatter:<br />(antal gange, et af forfatterens<br />scenarier har været kørt)<br />
		{$stat_aut_exp}
	</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	
	<tr valign="top">
	<td class="statleft">Forfattere, der har skrevet<br />scenarier med flest øvrige forfattere:<br />
		{$stat_aut_workwith}
	</td>
	
	<td>Mest brugte systemer:<br />
		{$stat_sys_used}
	</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	
	<tr valign="top">
	<td class="statleft">Scenarier spillet på flest kongresser:<br />
		{$stat_sce_replay}
	</td>

	<td>Scenarier med flest forfattere:<br />
		{$stat_sce_auts}
	</td>
	</tr>

	<tr><td colspan="3">&nbsp;</td></tr>
	
	<tr valign="top">
	<td class="statleft">Kongresser med flest scenarier:<br />(inkl. reruns, ekskl. aflysninger)<br />
		{$stat_con_sce}
	</td>
	
	<td>Antal kongresser + nye scenarier,<br />sorteret efter år:<br />
		{$stat_con_year}
	</td>
	</tr>
	
	</table>
	
</div>

{include file="end.tpl"}
