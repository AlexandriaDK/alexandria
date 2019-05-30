{assign var="pagetitle" value="{$_feeds|ucfirst}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_feeds|ucfirst}
	</h2>

		<p>
			{$_feeds_presentation|nl2br}
		</p>
	
		<table>
			<tr><th>{$_feeds_postdate}</th><th>{$_feeds_by}</th><th>{$_feeds_commentsno}</th><th>{$_feeds_postdate}</th></tr>
			{section name=i loop=$feeddata}
			<tr>
			<td title="{$feeddata[i].title|escape}">
				<a href="{$feeddata[i].link|escape}">{if $feeddata[i].title == ""}<i>({$_feeds_notitle})</i>{else}{$feeddata[i].title|truncate:55|escape}{/if}</a>
			</td>
			<td>
				{if $feeddata[i].aut_id}
					<a href="data?person={$feeddata[i].aut_id}" class="person">{$feeddata[i].owner|escape}</a>
				{else}
					{$feeddata[i].owner|escape}
				{/if}
			</td>
			<td style="text-align: right; padding-right: 35px;">
				{$feeddata[i].comments}
			</td>
			<td style="text-align: right;">
				{$feeddata[i].printdate}
			</td>
			</tr>
			{/section}
		</table>

		<h3>
			{$_feeds_sources}
		</h3>
		
		<p>
			{section name=i loop=$feedlist}
			<a href="{$feedlist[i].pageurl|escape}">{$feedlist[i].owner|escape}: {$feedlist[i].name|escape}</a><br>
			{/section}
		</p>

		<h3>
			{$_feeds_yourblog}
		</h3>
		
		<p>
			{$_feeds_yourblogdetails}
		</p>
</div>

{include file="end.tpl"}
