{assign var="pagetitle" value="Jost-spillet"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Jost-spillet:
	</h2>

{if $intro == 1} 
	<p style="width: 350px;">
		Jost-spillet bygger på ideen om at mange rollespillere er forbundet med hinanden,
		ved at have skrevet scenarier med fælles bekendte. To personer er forbundet, hvis
		de begge har skrevet scenarie med den samme tredje person - og så fremdeles.
	</p>
	<p style="width: 350px;">
		Det er også muligt at <a href="applet/allpeople_2004jun.gif">hente et diagram over
		alle større relationer</a>.
	</p>
{/if}

	{$content}
</div>

{include file="end.tpl"}
