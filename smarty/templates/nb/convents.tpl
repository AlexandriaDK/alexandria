{assign var="pagetitle" value="{$_cons_title}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle" style="margin-bottom: 1em;">
		{$_cons_list}
	</h2>


	<div class="con concolumns">{$list}</div>


</div>

{include file="end.tpl"}
