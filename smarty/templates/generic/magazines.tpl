{assign var="pagetitle" value="{$_magazines_title}"}
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
	<table>
	<tbody>
	{foreach $articles as $row}	
	<tr>
	{if not isset($lastid) || $row.id != $lastid}
	<td style="padding-right: 10px; text-align: right;">{if $row.page}{$_file_page} {$row.page|escape}{/if}</td>
	<td style="padding-right: 10px;">{if $row.sce_id}<a href="data?scenarie={$row.sce_id}" class="scenarie">{$row.title|escape}</a>{else}{$row.title|escape}{/if}</td>
	{else}
	<td colspan="2"></td>
	{/if}
	<td style="padding-right: 10px;">
		{if $row.aut_id}
		<a href="data?person={$row.aut_id}" class="person">{$row.name|escape}</a>
		{else}
		{$row.aut_extra|escape}
		{/if}
	</td>
	<td>{$row.role|escape}</td>
	</tr>
	{assign "lastid" $row.id}
	{/foreach}
	</tbody>
	</table>
	{/if}

{elseif $magazineid}
	<h2 class="pagetitle">
		{$magazinename}
	</h2>
	{if $magazinedescription}
	<p>
	{$magazinedescription|escape|textlinks|nl2br}
	</p>
	{/if}
	<ul>
	{foreach $issues as $issue}
	<li><a href="magazines?issue={$issue.id}">{$issue.title|escape}{if $issue.releasetext} - {$issue.releasetext|escape}{/if}</a></li>
	{/foreach}
	</ul>
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
		<h3><a href="magazines?id={$magazine.id}">{$magazine.name|escape}</a></h3>
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
