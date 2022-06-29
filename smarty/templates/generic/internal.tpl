{if $user_editor && $internal != ""}
	<h3 class="internal">{$_sce_intern}</h3>
	<p class="indata internal">
		{$internal|escape|textlinks|nl2br}
	</p>
{/if}

