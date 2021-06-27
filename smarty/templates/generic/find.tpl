{assign var="pagetitle" value="$_find_title"}
{include file="head.tpl"}

<div id="contentwide">

	<h2 class="pagetitle" style="margin-bottom: 5px;">
		{$_find_title}
	</h2>

{if $find_aut}
	<h3 class="findhead">
		{$_persons|ucfirst}
	</h3>
	{$find_aut}
{/if}

{if $find_sce}
	<h3 class="findhead">
		{if $search_boardgames}
		{$_boardgames|ucfirst}
		{else}
		{$_scenarios|ucfirst}
		{/if}
	</h3>
	{$find_sce}
{/if}

{if $find_convent}
	<h3 class="findhead">
		{$_conventions|ucfirst}
	</h3>
	{$find_convent}
{/if}

{if $find_sys}
	<h3 class="findhead">
		{$_rpgsystems|ucfirst}
	</h3>
	{$find_sys}
{/if}

{if $find_tags}
	<h3 class="findhead">
		{$_tags|ucfirst}
	</h3>
	{$find_tags}
{/if}

{if $find_magazines}
	<h3 class="findhead">
		{$_top_magazines|ucfirst}
	</h3>
	{$find_magazines}
{/if}


{if $find_files}
	<h3 class="findhead">
		{$_files|ucfirst}
	</h3>
	{$find_files}
{/if}

{if $find_articles}
	<h3 class="findhead">
		{$_p_articles|ucfirst}
	</h3>
	{$find_articles}
{/if}

{if $find_blogposts}
	<h3 class="findhead">
		{$_find_blogposts}
	</h3>
	{$find_blogposts}
{/if}

{if ! $find_aut && ! $find_sce && ! $find_convent && ! $find_sys && ! $find_files && ! $find_blogposts && ! $find_tags && ! $find_articles}
	<p class="nomatch">
		{$_find_nomatch}
	</p>
	<p>
		{$_find_contactus|sprintf:'rettelser'}
	</p>
{/if}

</div>

{include file="end.tpl"}
