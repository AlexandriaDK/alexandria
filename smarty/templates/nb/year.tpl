<div id="content">

	<h2 class="datatitle">{$_year_eventsin} {$year}</h2>

	{$yearlist}
	
	{if $num_cons == 0}
	<p>{$_year_nomatch}</p>
	{else}
	<p>{$num_cons} {if $num_cons == 1}{$_event}{else}{$_events}{/if}:</p>

	<div class="calendar">	
	{$output}
	</div>
	{/if}

</div>
