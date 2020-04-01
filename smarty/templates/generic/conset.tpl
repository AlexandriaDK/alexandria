<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="/gfx/conset/l_{$id}.jpg">
			<img src="/gfx/conset/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		({$_aka}: {$alias})
	</p>
{/if}

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
                <td style="padding-left: 10px;"{if $con.cancelled} class="cancelled" title="{$_sce_cancelled|ucfirst}"{/if}>{$con.place}</td>
	</tr>
	{/foreach}
	</table>
{/if}

{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
