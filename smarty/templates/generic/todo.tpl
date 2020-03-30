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
<p>
	{$_todo_listguide|nl2br}
</p>
<div class="todolist">
{foreach from=$cons_list item=$con}
	<a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a><br>
{/foreach}
</div>

<h3>{$_todo_helpwithcontent}</h3>
<p>
	{$_todo_contentguide|nl2br}
</p>
<div class="todolist">
{foreach from=$cons_content item=$con}
	<a href="data?con={$con.id}" class="con">{$con.name} ({$con.year})</a><br>
{/foreach}
</div>

<h3>{$_todo_tools}</h3>
<p>
	{$_todo_toolsguide|nl2br|sprintf:'https://www.google.com/search?query=pdf+ocr'}
</p>

{include file="end.tpl"}
