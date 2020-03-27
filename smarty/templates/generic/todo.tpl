{assign var="pagetitle" value="{$_todo_title}"}
{include file="head.tpl"}

<div id="contenttext">
		<h2 class="pagetitle">
			{$_todo_title}
		</h2>

<p>
	{$_todo_intro|nl2br|sprintf:'kontakt'}
</p>

<h3>{$_todo_helpwithlist}</h3>
<div class="todolist">
{foreach from=$cons_list item=$con}
	<a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a><br>
{/foreach}
</div>

<h3>{$_todo_helpwithcontent}</h3>
<div class="todolist">
{foreach from=$cons_content item=$con}
	<a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a><br>
{/foreach}
</div>

{include file="end.tpl"}
