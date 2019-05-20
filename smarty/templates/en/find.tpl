{assign var="pagetitle" value="Søgning"}
{include file="head.tpl"}

<div id="contentwide">

	<h2 class="pagetitle" style="margin-bottom: 5px;">
		Søgeresultat:
	</h2>

{if $find_aut}
	<h3 class="findhead">
		Personer:
	</h3>
	{$find_aut}
{/if}

{if $find_sce}
	<h3 class="findhead">
		{if $search_boardgames}
		Brætspil:
		{else}
		Scenarier:	
		{/if}
	</h3>
	{$find_sce}
{/if}

{if $find_convent}
	<h3 class="findhead">
		Cons:
	</h3>
	{$find_convent}
{/if}

{if $find_sys}
	<h3 class="findhead">
		Systemer:
	</h3>
	{$find_sys}
{/if}

{if $find_tags}
	<h3 class="findhead">
		Tags:
	</h3>
	{$find_tags}
{/if}

{if $find_files}
	<h3 class="findhead">
		Filer:
	</h3>
	{$find_files}
{/if}

{if $find_blogposts}
	<h3 class="findhead">
		Blogindlæg:
	</h3>
	{$find_blogposts}
{/if}

{if ! $find_aut && ! $find_sce && ! $find_convent && ! $find_sys && ! $find_files && ! $find_blogposts}
	<p style="font-weight: bold;">
		Intet fundet!
	</p>
	<p>
		Mangler vi noget? <a href="/rettelser">Send os gerne en rettelse!</a>
	</p>
{/if}

</div>

{include file="end.tpl"}
