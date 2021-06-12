{assign var="pagetitle" value="{if $issueid}{$issue.magazinename} - {$issue.title} - {if $issue.releasetext}{$issue.releasetext}{/if}{elseif $magazinename}{$magazinename}{else}{$_magazines_title}{/if}"}
{include file="head.tpl"}

<div id="content">
{if $pic}
	<div style="float: right;">
		<a href="/gfx/{$picpath}/l_{$picid}.jpg">
			<img src="/gfx/{$picpath}/s_{$picid}.jpg" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

	<div>
{if $issueid}
	<h2 class="pagetitle">
		<a href="magazines?id={$issue.magazineid}">{$issue.magazinename|escape}</a>
	</h2>
	<h3>{$issue.title|escape}{if $issue.releasetext} - {$issue.releasetext|escape}{/if}</h3>
	<div class="arrows">
{if $arrowset.prev.active}
	<a href="magazines?issue={$arrowset.prev.id}" title="{$arrowset.prev.title|escape}{if $arrowset.prev.releasetext} - {$arrowset.prev.releasetext|escape}{/if}" rel="prev">←</a>
{else}
	<span class="inactive">←</span>
{/if}
{if $arrowset.next.active}
	<a href="magazines?issue={$arrowset.next.id}" title="{$arrowset.next.title|escape}{if $arrowset.next.releasetext} - {$arrowset.next.releasetext|escape}{/if}"" rel="next">→</a>
{else}
	<span class="inactive">→</span>
{/if}
	</div>

{include file="filelist.tpl"}

	{if $colophon}
	<h4>{$_magazines_colophon}</h4>
	<table>
	<tbody>
	{foreach $colophon as $row}
	<tr>
	<td style="padding-right: 10px; text-align: right;">
		{$row.role|escape}
	</td>
	<td>
		{if $row.aut_id}
		<a href="data?person={$row.aut_id}" class="person">{$row.name|escape}</a>
		{else}
		{$row.aut_extra|escape|nl2br}
		{/if}
	</td>
	</tr>
	{/foreach}
	</tbody>
	</table>
	{/if}

	{if $articles}
	<h4>{$_magazines_content}</h4>
	<table class="magazinecontent">
	<tbody>
	{foreach $articles as $row}	
	<tr>
	{if not isset($lastid) || $row.id != $lastid}
	<td class="page">{if $row.page}{$_file_page} {$row.page|escape}{/if}</td>
	<td {if $row.contributorcount > 1} rowspan="{$row.contributorcount}"{/if}>
	{if $row.sce_id}<a href="data?scenarie={$row.sce_id}" class="scenarie">{$row.title|escape}</a>{else}{$row.title|escape}{/if}
	{if $row.description}<br><span class="description">{$row.description|escape|textlinks|nl2br}</span>{/if}
	{if $row.references}<br><div class="references">
	{foreach $row.references AS $reference}{$reference} {/foreach}</div></td>
	{/if}
	{else}
	<td></td>
	{/if}
	<td class="contributor">
		{if $row.aut_id}
		<a href="data?person={$row.aut_id}" class="person">{$row.name|escape}</a>
		{else}
		{$row.aut_extra|escape}
		{/if}
	</td>
	<td class="role">{$row.role|escape}</td>
	</tr>
	{assign "lastid" $row.id}
	{/foreach}
	</tbody>
	</table>
	{/if}

{elseif $magazineid}
	<h2 class="pagetitle">
		{$magazinename|escape}
	</h2>
	{if $magazinedescription}
	<p>
	{$magazinedescription|escape|textlinks|nl2br}
	</p>
	{/if}
	<div class="issuegrid">
	{foreach $issues as $issue}
	<div>
	<div>
	<a href="magazines?issue={$issue.id}">
	{if $issue.thumbnail}
		<img src="/gfx/issue/s_{$issue.id}.jpg" alt="{$magazinename|escape}, {$issue.title}">
	{else}
		{if isset($magazinename)}<h3>{$magazinename|escape}</h3>{/if}
		{if isset($issue.title)}<h4>{$issue.title|escape}</h4>{/if}
		{if $issue.releasetext}<h4>{$issue.releasetext|escape}</h4>{/if}
	{/if}
	</a>
	</div>
	<a href="magazines?issue={$issue.id}">{$issue.title|escape}{if $issue.releasetext}<br>{$issue.releasetext|escape}{/if}</a>
	</div>
	{/foreach}
	</div>
	{include file="articlereference.tpl"}

{else}
	<h2 class="pagetitle">
		{$_magazines_list}
	</h2>
	<p>
		{$_magazines_description}
	</p>
	<div id="magazinelist">
	{foreach $magazines as $magazine}
		<div>
		<h3><a href="magazines?id={$magazine.id}">{$magazine.name|escape}</a> ({$magazine.issuecount})</h3>
		<blockquote>
			{$magazine.description|escape|textlinks|nl2br}
		</blockquote>
		</div>
	{/foreach}
	</div>

{/if}
	</div>

{include file="internal.tpl"}

{if $issueid}
{assign "id" $issueid}
{assign "type" "issue"}
{elseif $magazineid}
{assign "id" $magazineid}
{assign "type" "magazine"}
{else}
{assign "type" "magazine"}
{/if}
{include file="updatelink.tpl"}
</div>
{include file="end.tpl"}
