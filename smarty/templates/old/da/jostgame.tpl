{assign var="pagetitle" value="Jost-spillet"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Jost-spillet
	</h2>

{if $intro == 1} 
	<p style="width: 350px;">
		Jost-spillet bygger på ideen om at mange rollespillere er forbundet med hinanden,
		ved at have skrevet scenarier med fælles bekendte. To personer er forbundet, hvis
		de begge har skrevet scenarie med den samme tredje person - og så fremdeles.
	</p>
{/if}

<form action="jostspil" method="get"><table><tr>
<td>{$_jost_first}</td>
<td><input type="text" name="from" class="tags" value="{$from|escape}" /></td></tr>
<tr>
<td>{$_jost_second}</td>
<td><input type="text" name="to" class="tags" value="{$to|escape}" /></td></tr>
<tr><td><input type="submit" value="{$_jost_connect|escape}" /></td></tr>
</table>
</form>

	{$content}
</div>

{include file="end.tpl"}
