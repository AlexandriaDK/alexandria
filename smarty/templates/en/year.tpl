<div id="content">

	<h2 class="datatitle">Events in {$year}</h2>

	{$yearlist}
	
	{if $num_cons == 0}
	<p>No events were found this year.</p>
	{else}
	<p>{$num_cons} {if $num_cons == 1}event{else}events{/if}:</p>

	<div class="calendar">	
	{$output}
	</div>
	{/if}

</div>
