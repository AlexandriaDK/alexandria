{assign var="pagetitle" value="Oversigt over personer"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		People:
	</h2>

	<p style="font-size: 16px;"><a href="personer?b={$b}&amp;s=f">Sort by first name</a> &nbsp; <a href="personer?b={$b}">Sort by surname</a>
	<br><br>
	{$chars}
	</p>

	<h2>{$b|mb_strtoupper}</h2>

	<div class="person" style="column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;">{$list}</div>

</div>

{include file="end.tpl"}
