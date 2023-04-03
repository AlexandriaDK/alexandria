{assign var="pagetitle" value="{$_jost_title}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_jost_title}
	</h2>

{if $intro == 1} 
	<p class="jostdescription">
		{$_jost_description}
	</p>
{/if}

<form action="jostspil" method="get"><table><tr>
<td>{$_jost_first}</td>
<td><input type="text" name="from" class="peopletags" value="{$from|escape}">{if $from_error} <span class="finderror">?</span>{/if}</td>
</tr>
<tr>
<td>{$_jost_second}</td>
<td><input type="text" name="to" class="peopletags" value="{$to|escape}">{if $to_error} <span class="finderror">?</span>{/if}</td>
</tr>
<tr><td><input type="submit" value="{$_jost_connect|escape}"></td></tr>
</table>
</form>
	{$content}
	{$svg}
</div>

{include file="end.tpl"}
