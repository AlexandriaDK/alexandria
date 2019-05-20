{assign var="pagetitle" value="Feeds"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Feeds:
	</h2>

		<p>
			Feeds from a bunch of Danish gaming blogs - updated each hour.<br>
			You can also fetch a <a href="feedrss.php">meta feed for all blogs</a> to your own RSS feed reader.
		</p>
	
		<table>
			<tr><th>Title</th><th>By</th><th>Comments</th><th>Posted</th></tr>
			{section name=i loop=$feeddata}
			<tr>
			<td title="{$feeddata[i].title|escape}">
				<a href="{$feeddata[i].link|escape}">{if $feeddata[i].title == ""}<i>(no title)</i>{else}{$feeddata[i].title|truncate:55|escape}{/if}</a>
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
			The following feeds are being fetched:
		</h3>
		
		<p>
			{section name=i loop=$feedlist}
			<a href="{$feedlist[i].pageurl|escape}">{$feedlist[i].owner|escape}: {$feedlist[i].name|escape}</a><br>
			{/section}
		</p>

		<h3>
			Do you own a gaming blog?
		</h3>
		
		<p>
			You can have your blog added to this list by <a href="kontakt">contacting Alexandria</a>
			and send a link and a description. Your page needs to offer a feed following the RSS or Atom specification.
			Most blogs have this feature enabled.
		</p>
		
		<p>
			Har du andet end rollespils-indhold på din blog, kan du benytte dig af tags,
			kategorier eller andre opdelinger, så Alexandria kan nøjes med at hente det rollespils-relevante.
		</p>


</div>

{include file="end.tpl"}
