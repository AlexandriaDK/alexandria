<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="gfx/conset/l_{$id}.jpg">
			<img src="gfx/conset/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		(AKA: {$alias})
	</p>
{/if}

{if $description != ""}
	<h3 class="parttitle">
		Om kongres-serien:
	</h3>
	
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

{include file="filelist.tpl"}

{if $conlist != ""}
	<h3 class="parttitle">
		Kongresser:
	</h3>
	{$conlist}
{/if}

{if $trivia}
	<h3 class="parttitle">Trivia:</h3>
	<ul class="indatalist">
		{$trivia}
	</ul>
{/if}

{if $link}
	<h3 class="parttitle">Links:</h3>
	<p class="indata">
		{$link}
	</p>
{/if}

{include file="updatelink.tpl"}

</div>
