{assign var="pagetitle" value="{$_about_title}"}
{include file="head.tpl"}

<div id="content">
		<h2 class="pagetitle">
			{$_about_welcome}
		</h2>

		<p>
			{$_about_content|nl2br}
		</p>

		<h3>
			{$_about_history_title}
		</h3>

		<p>
			{$_about_history|nl2br}
		</p>
</div>

{include file="end.tpl"}
