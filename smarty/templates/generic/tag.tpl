<div id="content">

<article>
	<h2 class="datatitle">{$tag|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="/gfx/tag/l_{$id}.jpg">
			<img src="/gfx/tag/s_{$id}.jpg" alt="{$tag|escape}" title="{$tag|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $description != ""}
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}
</article>

{include file="filelist.tpl"}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $slist}
	<h3 class="parttitle">
		{$_games|ucfirst}
	</h3>

	<table class="indata">
	{foreach from=$slist item=$game}
		<tr>
			{if $game.read}<td>{$game.read}</td>{/if}
			{if $game.gmed}<td>{$game.gmed}</td>{/if}
			{if $game.played}<td>{$game.played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $game.files}<a href="{$game.link}"  title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
			<td><a href="{$game.link}" class="scenarie" title="{$game.origtitle|escape}">{$game.title|escape}</a></td>
			<td class="lpad">{$game.personlist}</td>
			<td {if $game.cancelled}class="cancelled"{/if}>{if isset($game.conlink)}<a href="{$game.conlink}" class="con" title="{$game.coninfo}">{$game.conname|escape}</a>{/if}</td>
		</tr>
	{/foreach}
	</table>
{/if}

{include file="articlereference.tpl"}
{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
