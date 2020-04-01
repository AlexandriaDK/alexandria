{assign var="pagetitle" value="{$_todo_title}"}
{include file="head.tpl"}

<div id="content">
<h2 class="pagetitle">{$_todo_title}</h2>
<p>
	{$_todo_intro|nl2br|sprintf:'kontakt'}
</p>

<h3>{$_todo_tools}</h3>
<p>
	{$_todo_toolsguide|nl2br|sprintf:'https://www.google.com/search?query=pdf+ocr'}
</p>

{if $cons_list}
<h3>{$_todo_helpwithlist}</h3>
<p>
	{$_todo_listguide|nl2br}
</p>
<div class="countrylist">
{foreach from=$cons_list_c key=$cc item=$country}
	<span class="countrybutton" onclick="$('#helplist [data-country={$cc}]').toggle( 100 );" title="{$country|escape}">[{$cc|upper}]</span>
{/foreach}
</div>
<div class="todolist todocons" id="helplist">
{foreach from=$cons_list item=$con}
	{con dataset=$con}
{/foreach}
</div>
{/if}

{if $cons_content}
<h3>{$_todo_helpwithcontent}</h3>
<p>
	{$_todo_contentguide|nl2br}
</p>
<div class="countrylist">
{foreach from=$cons_content_c key=$cc item=$country}
	<span class="countrybutton" onclick="$('#helpguide [data-country={$cc}]').toggle( 100 );" title="{$country|escape}">[{$cc|upper}]</span>
{/foreach}
</div>
<div class="todolist todocons" id="helpguide">
{foreach from=$cons_content item=$con}
	{con dataset=$con}
{/foreach}
</div>
{/if}

{if $cons_missing}
<h3>{$_todo_helpwithmissing}</h3>
<p>
	{$_todo_contentmissing|nl2br}
</p>
<div class="countrylist">
{foreach from=$cons_missing_c key=$cc item=$country}
	<span class="countrybutton" onclick="$('#helpmissing [data-country={$cc}]').toggle( 100 );" title="{$country|escape}">[{$cc|upper}]</span>
{/foreach}
</div>
<div class="todolist todocons" id="helpmissing">
{foreach from=$cons_missing item=$con}
	{con dataset=$con}
{/foreach}
</div>
{/if}


{include file="end.tpl"}
