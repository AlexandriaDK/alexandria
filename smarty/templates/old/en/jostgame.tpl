{assign var="pagetitle" value="The Jost Game"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		The Jost Game
	</h2>

{if $intro == 1} 
	<p style="width: 350px;">
		The Jost Game is our version of "Six Degrees". A lot of authors are interconnected
		by having written scenarios with the same co-authors. Two people are connected if
		they have written scenarios with the same third part - and so on.
	</p>
{/if}

	{$content}
</div>

{include file="end.tpl"}
