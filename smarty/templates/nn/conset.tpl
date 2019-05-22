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

{if $conlist != ""}
	<h3 class="parttitle">
		{$_conventions|ucfirst}
	</h3>
	<table class="conlist">
	{section name=i loop=$condata}
	<tr>
		<td>{$condata[i].userdyn}</td>
		<td><a href="data?con={$condata[i].id}" class="con" title="{$condata[i].dateset|escape}">{$condata[i].name} ({$condata[i].year})</a></td>
                <td style="padding-left: 10px;">{$condata[i].place}</td>
	</tr>
	{/section}
	</table>
{/if}

{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
