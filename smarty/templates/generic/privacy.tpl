{assign var="pagetitle" value="{$_privacy_head}"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			{$_privacy_head}
		</h2>

		<p>
			{$_privacy_content|sprintf:'mailto:peter@ter.dk':'peter@ter.dk'|nl2br}
		</p>


</div>

{include file="end.tpl"}
