{assign var="pagetitle" value="Søgning"}
{include file="head.tpl"}

<div id="contentwide">

	<h2 class="pagetitle" style="margin-bottom: 5px;">
		Søgeresultat:
	</h2>

{if $find_aut}
	<h3 class="findhead">
		Personer fundet:
	</h3>
	{$find_aut}
{/if}

{if $find_sce}
	<h3 class="findhead">
		Scenarier fundet:
	</h3>
	{$find_sce}
{/if}

{if $find_convent}
	<h3 class="findhead">
		Cons fundet:
	</h3>
	{$find_convent}
{/if}

{if $find_sys}
	<h3 class="findhead">
		Systemer fundet:
	</h3>
	{$find_sys}
{/if}

{if $find_files}
	<h3 class="findhead">
		Filer fundet:
	</h3>
	{$find_files}
{/if}

{if ! $find_aut && ! $find_sce && ! $find_convent && ! $find_sys && ! $find_files}
	<p style="font-weight: bold;">
		Intet fundet!
	</p>
{/if}

</div>

{include file="end.tpl"}
