{assign var="pagetitle" value="Oversigt over personer"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Person-oversigt:
	</h2>

	<p style="font-size: 16px;"><a href="personer?b={$b}&amp;s=f">Sortér pr. fornavn</a> &nbsp; <a href="personer?b={$b}">Sortér pr. efternavn</a>
	<br><br>
	{$chars}
	</p>

	<h2>{$b|mb_strtoupper}</h2>

	<div class="person" style="column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;">{$list}</div>

</div>

{include file="end.tpl"}
