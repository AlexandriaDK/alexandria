{assign var="pagetitle" value="$_find_title"}
{include file="head.tpl"}

<div id="contentwide">

	<h2 class="pagetitle" style="margin-bottom: 5px;">
		{$_find_title}
	</h2>

{if $find_person}
	<h3 class="findhead">
		{$_persons|ucfirst}
	</h3>
	{$find_person}
{/if}

{if $find_game}
	<h3 class="findhead">
		{if $search_boardgames}
		{$_boardgames|ucfirst}
		{else}
		{$_scenarios|ucfirst}
		{/if}
	</h3>
	{$find_game}
{/if}

{if $find_convention}
	<h3 class="findhead">
		{$_conventions|ucfirst}
	</h3>
	{$find_convention}
{/if}

{if $find_gamesystem}
	<h3 class="findhead">
		{$_rpgsystems|ucfirst}
	</h3>
	{$find_gamesystem}
{/if}

{if $find_locations}
	<h3 class="findhead">
		{$_locations|ucfirst}
	</h3>
	{$find_locations}
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

{if $find_articles}
	<h3 class="findhead">
		{$_p_articles|ucfirst}
	</h3>
	{$find_articles}
{/if}

{if $find_files}
	<h3 class="findhead">
		{$_files|ucfirst}
	</h3>
	{$find_files}
{/if}

{if $find_blogposts}
	<h3 class="findhead">
		{$_find_blogposts}
	</h3>
	{$find_blogposts}
{/if}

{if ! $find_person && ! $find_game && ! $find_convention && ! $find_gamesystem && ! $find_files && ! $find_blogposts && ! $find_tags && ! $find_articles && ! $find_locations}
	<p class="nomatch">
		{$_find_nomatch}
	</p>
	<p>
		{$_find_contactus|sprintf:'rettelser'}
	</p>
{/if}

</div>

{include file="end.tpl"}
