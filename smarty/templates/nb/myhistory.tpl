{assign var="pagetitle" value="Min side"}
{include file="head.tpl"}

<div id="contentwide">

		<h2 class="pagetitle">
			Min side:
		</h2>
		
		{if $content_addentry}
			<h3>Du kan ikke lige tilføje data i øjeblikket...</h3>
		{/if}
		
		<div id="kongres" style="float: left; margin-right: 30px;" >
		{$content_myconvents}
		{if not $content_myconvents}
		<h3 class="parttitle">Kongresser:</h3>
		<p>
			Du har ikke tilføjet nogen kongresser på din liste.
		</p>
		{/if}
		</div>
		
		<div id="scenarier" style="float: left; margin-right: 30px;" >
		{$content_myscenarios}
		{if not $content_myscenarios}
		<h3 class="parttitle">Scenarier:</h3>
		<p>
			Du har ikke tilføjet nogen scenarier på din liste.
		</p>
		{/if}
		</div>		

		<div id="achievements" style="float: left; margin-right: 30px; ">
		{$content_personal_achievements}
		</div>

</div>

{include file="end.tpl"}
