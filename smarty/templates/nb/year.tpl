<div id="content">

	<h2 class="datatitle">Arrangementer i {$year}</h2>

	{$yearlist}
	
	{if $num_cons == 0}
	<p>Beklager - der blev ikke findet nogen arrangementer det år.</p>
	{else}
	<p>{$num_cons} {if $num_cons == 1}arrangement{else}arrangementer{/if}:</p>

	<div class="calendar">	
	{$output}
	</div>
	{/if}

</div>
