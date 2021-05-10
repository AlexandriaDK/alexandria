{assign var="pagetitle" value="{$_magazines_title}"}
{include file="head.tpl"}

<div id="content">

	<div>
{if $issueid}
	<h2 class="pagetitle">
		<a href="magazines?id={$issue.magazineid}">{$issue.magazinename|escape}</a>
	</h2>
	<h3>{$issue.title|escape}{if $issue.releasetext} - {$issue.releasetext|escape}{/if}</h3>

	<h4>{$_magazines_colophone}</h4>
	<table>
	<tbody>
	{foreach $colophone as $row}
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

	<h4>{$_magazines_content}</h4>
	<table>
	<tbody>
	{foreach $articles as $row}
	<tr>
	<td style="padding-right: 10px; text-align: right;">{$_file_page} {$row.page|escape}</td>
	<td style="padding-right: 10px;">{$row.title|escape}</td>
	<td style="padding-right: 10px;">
		{if $row.aut_id}
		<a href="data?person={$row.aut_id}" class="person">{$row.name|escape}</a>
		{else}
		{$row.aut_extra|escape}
		{/if}
	</td>
	<td>{$row.role|escape}</td>
	</tr>
	{/foreach}
	</tbody>
	</table>

{elseif $magazineid}
	<h2 class="pagetitle">
		{$magazinename}
	</h2>
	<ul>
	{foreach $issues as $issue}
	<li><a href="magazines?issue={$issue.id}">{$issue.title|escape}{if $issue.releasetext} - {$issue.releasetext|escape}{/if}</a></li>
	{/foreach}
	</ul>
{else}
	<h2 class="pagetitle">
		{$_magazines_list}
	</h2>
	{foreach $magazines as $magazine}
		<h3><a href="magazines?id={$magazine.id}">{$magazine.name|escape}</a></h3>
	{/foreach}

{/if}
	</div>

</div>

{include file="end.tpl"}
