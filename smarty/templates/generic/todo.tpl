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
<ul>
{foreach from=$cons_list item=$con}
	<li><a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a></li>
{/foreach}
</ul>
</div>

<h3>{$_todo_helpwithcontent}</h3>
<div class="todolist">
<ul>
{foreach from=$cons_content item=$con}
	<li><a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a></li>
{/foreach}
</ul>
</div>

{include file="end.tpl"}
