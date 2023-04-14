<div id="content">

	<h2 class="datatitle">{$name|escape}{if $haslocations} <a href="locations?conset_id={$id}">🗺️</a>{/if}</h2>

{if $pic}
	<div style="float: right;">
		<a href="/gfx/conset/l_{$id}.jpg">
			<img src="/gfx/conset/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{include file="alias.tpl"}

{if $description != ""}
	<h3 class="parttitle">
		{$_conset_about}
	</h3>
	
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

{include file="filelist.tpl"}

{if $condata}
	<h3 class="parttitle">
		{$_conventions|ucfirst}
	</h3>
	<table class="conlist">
	{foreach from=$condata item=$con}
	<tr>
		<td>{$con.userdyn}</td>
		<td>{con dataset=$con}</td>
		<td style="padding-left: 10px">{if $con.haslocations}<a href="locations?convention_id={$con.id}" title="{$con.haslocations} {if $con.haslocations == 1}{$_location|escape}{else}{$_locations|escape}{/if}">🗺️</a>{/if}
		<td {if $con.cancelled}class="cancelled" title="{$_sce_cancelled|ucfirst}"{/if}>{$con.place}{if $con.place && $con.country}, {/if}{if $con.country}{$con.country|getCountryNameFallback}{/if}</td>
	</tr>
	{/foreach}
	</table>
{/if}

{include file="articlereference.tpl"}
{include file="trivialink.tpl"}
{include file="internal.tpl"}
{include file="updatelink.tpl"}

</div>
