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
	{foreach from=$slist item=$game}
		<tr>
			{if $game.read}<td>{$game.read}</td>{/if}
			{if $game.gmed}<td>{$game.gmed}</td>{/if}
			{if $game.played}<td>{$game.played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $game.files}<a href="{$game.link}" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
			<td><a href="{$game.link}" class="scenarie">{$game.title|escape}</a></td>
			<td class="lpad">{$game.forflist}</td>
			<td>{if $game.conlink}<a href="{$game.conlink}" class="con" title="{$game.coninfo}">{$game.conname|escape}</a>{/if}</td>

	{/foreach}
	</table>
{/if}

{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
