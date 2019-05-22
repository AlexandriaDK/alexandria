{assign var="pagetitle" value="{$_tags_title}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle" style="margin-bottom: 1em;">
		{$_tags|ucfirst}
	</h2>

	<p>
		{$_tags_order}: <a href="tags">{$_tags_alpha}</a> - <a href="tags?popular">{$_tags_popular}</a>
	</p>

	<div style="column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;">
	{foreach from=$taglist item=tag}
	<a href="{$tag.url}"{if $tag.has_article} class="highlight"{/if}>{$tag.tagname|escape}</a>
	{if $user_admin || $user_editor} ({$tag.count}){/if}	
	<br>
	{/foreach}

	</div>

</div>

{include file="end.tpl"}
