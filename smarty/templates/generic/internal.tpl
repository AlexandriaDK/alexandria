{if $user_editor && $intern != ""}
	<h3 class="internal">{$_sce_intern}</h3>
	<p class="indata internal">
		{$intern|escape|textlinks|nl2br}
	</p>
{/if}

