{assign var="pagetitle" value="{$_todo_title}"}
{include file="head.tpl"}

<div id="content">
<h2 class="pagetitle">{$_todo_title}</h2>
<p>
	{$_todo_intro|nl2br|sprintf:'kontakt'}
</p>

<h3>{$_todo_international}</h3>
<p>
	{$_todo_guideinternational|nl2br|sprintf:'kontakt'}
</p>

{if $cons_list}
<h3>{$_todo_helpwithlist}</h3>
<p>
	{$_todo_guidelist|nl2br}
</p>

<div id="tabslist" class="tabsmin">
<ul>
{foreach $cons_list as $cc => $country}
<li><a href="#tabslist-{$cc}" title="{$country.countryname|escape}">{$country.countryname|escape} ({$country.cons|count})</a></li>
{/foreach}
</ul>
{foreach $cons_list as $cc => $countries}
<div id="tabslist-{$cc}" class="todolist">
	{foreach $countries.cons as $con}
		<div>
		{con dataset=$con}
		</div>
	{/foreach}
</div>
{/foreach}
</div>
{/if}

{if $cons_content}
<h3>{$_todo_helpwithcontent}</h3>
<p>
	{$_todo_guidecontent|nl2br}
</p>

<div id="tabsguide" class="tabsmin">
<ul>
{foreach $cons_content as $cc => $country}
<li><a href="#tabsguide-{$cc}" title="{$country.countryname|escape}">{$country.countryname|escape} ({$country.cons|count})</a></li>
{/foreach}
</ul>
{foreach $cons_content as $cc => $countries}
<div id="tabsguide-{$cc}" class="todolist">
	{foreach $countries.cons as $con}
		<div>
		{con dataset=$con}
		</div>
	{/foreach}
</div>
{/foreach}
</div>
{/if}

{if $cons_missing}
<h3>{$_todo_helpwithmissing}</h3>
<p>
	{$_todo_guidemissing|nl2br}
</p>

<div id="tabsmissing" class="tabsmin">
<ul>
{foreach $cons_missing as $cc => $country}
<li><a href="#tabsmissing-{$cc}" title="{$country.countryname|escape}">{$country.countryname|escape} ({$country.cons|count})</a></li>
{/foreach}
</ul>
{foreach $cons_missing as $cc => $countries}
<div id="tabsmissing-{$cc}" class="todolist">
	{foreach $countries.cons as $con}
		<div>
		{con dataset=$con}
		</div>
	{/foreach}
</div>
{/foreach}
</div>
{/if}

<h3>{$_todo_tools}</h3>
<p>
	{$_todo_guidetools|nl2br|sprintf:'https://www.google.com/search?query=pdf+ocr'}
</p>


</div>
{include file="end.tpl"}
