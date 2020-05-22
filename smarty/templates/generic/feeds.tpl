{assign var="pagetitle" value="{$_feeds|ucfirst}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_feeds|ucfirst}
	</h2>

		<p>
			{$_feeds_presentation|sprintf:'feedrss'|nl2br}
		</p>
	
		<table>
			<tr><th>{$_feeds_title}</th><th>{$_feeds_by}</th><th>{$_feeds_commentsno}</th><th>{$_feeds_postdate}</th></tr>
			{foreach from=$feeddata item=$feedrow}
			<tr>
			<td title="{$feedrow.title|escape}">
				<a href="{$feedrow.link|escape}">{if $feedrow.title == ""}<i>({$_feeds_notitle})</i>{else}{$feedrow.title|truncate:55|escape}{/if}{if $feedrow.podcast} 🔊{/if}</a>
			</td>
			<td>
				{if $feedrow.aut_id}
					<a href="data?person={$feedrow.aut_id}" class="person">{$feedrow.owner|escape}</a>
				{else}
					{$feedrow.owner|escape}
				{/if}
			</td>
			<td style="text-align: right; padding-right: 35px;">
				{$feedrow.comments}
			</td>
			<td style="text-align: right;">
				{$feedrow.printdate}
			</td>
			</tr>
			{/foreach}
		</table>

		<h3>
			{$_feeds_sources}
		</h3>
		
		<p class="close">
			{foreach from=$feedlist item=$feed}
			<a href="{$feed.pageurl|escape}">{$feed.owner|escape}: {$feed.name|escape}</a><br>
			{/foreach}
		</p>

		<h3>
			{$_feeds_yourblog}
		</h3>
		
		<p>
			{$_feeds_yourblogdetails|sprintf:'kontakt'}
		</p>
</div>

{include file="end.tpl"}
