<div id="content">

	<h2 class="datatitle">{$tag|escape}</h2>

{if $alias != ""}
	<p class="indata">
		({$_aka}: {$alias})
	</p>
{/if}

{if $description != ""}
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $slist}
	<h3 class="parttitle">
		{$_scenarios|ucfirst}
	</h3>

	<table class="indata">
	{section name=s loop=$slist}
		<tr>
			{if $slist[s].read}<td>{$slist[s].read}</td>{/if}
			{if $slist[s].gmed}<td>{$slist[s].gmed}</td>{/if}
			{if $slist[s].played}<td>{$slist[s].played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $slist[s].files}<a href="{$slist[s].link}"  title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
			<td><a href="{$slist[s].link}" class="scenarie">{$slist[s].title|escape}</a></td>
			<td class="lpad">{$slist[s].forflist}</td>
			<td>{if $slist[s].conlink}<a href="{$slist[s].conlink}" class="con" title="{$slist[s].coninfo}">{$slist[s].conname|escape}</a>{/if}</td>

	{/section}
	</table>
{/if}

{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
