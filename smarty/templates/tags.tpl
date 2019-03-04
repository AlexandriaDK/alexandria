{assign var="pagetitle" value="Oversigt over tags"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle" style="margin-bottom: 1em;">
		Tags:
	</h2>

	<div style="column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;">
	{foreach from=$taglist item=tag}
	<a href="{$tag.url}"{if $tag.has_article} class="highlight"{/if}>{$tag.tagname|escape}</a>
	{if $user_admin || $user_editor} ({$tag.count}){/if}	
	<br>
	{/foreach}

	</div>

</div>

{include file="end.tpl"}
