<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="gfx/system/l_{$id}.jpg">
			<img src="gfx/system/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		(AKA: {$alias})
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

{*
{if $scenlist != ""}
	<h3 class="parttitle">
		Scenarios:
	</h3>
		{$scenlist}
{/if}
*}

{if $slist}
	<h3 class="parttitle">
		Scenarios:
	</h3>

	<table class="indata">
	{section name=s loop=$slist}
		<tr>
			{if $slist[s].read}<td>{$slist[s].read}</td>{/if}
			{if $slist[s].gmed}<td>{$slist[s].gmed}</td>{/if}
			{if $slist[s].played}<td>{$slist[s].played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $slist[s].files}<a href="{$slist[s].link}" title="This scenario can be downloaded">💾</a>{/if}</td>
			<td><a href="{$slist[s].link}" class="scenarie">{$slist[s].title|escape}</a></td>
			<td class="lpad">{$slist[s].forflist}</td>
			<td>{if $slist[s].conlink}<a href="{$slist[s].conlink}" class="con" title="{$slist[s].coninfo}">{$slist[s].conname|escape}</a>{/if}</td>

	{/section}
	</table>
{/if}


{if $trivia}
<h3 class="parttitle">Trivia:</h3>
<ul class="indatalist">
{$trivia}
</ul>
{/if}

{if $link}
<h3 class="parttitle">Links:</h3>
<p class="indata">
{$link}
</p>
{/if}

{include file="updatelink.tpl"}

</div>
