{assign var="pagetitle" value="Oversigt over personer"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Person-oversigt:
	</h2>

	<p style="font-size: 14px;"><a href="personer?b={$b}&amp;s=f">Sortér pr. fornavn</a> &nbsp; <a href="personer?b={$b}">Sortér pr. efternavn</a>
	<br /><br />
	{$chars}
	</p>

	<table cellspacing="2" cellpadding="2">
	<tr><td><h2>{$b|strtoupper}</h2></td></tr>

	<tr valign="top">
	<td><div class="person" style="margin-right: 10px;">{$part1}</div></td>
	<td><div class="person">{$part2}</div></td>
	</tr>
	</table>

</div>

{include file="end.tpl"}
