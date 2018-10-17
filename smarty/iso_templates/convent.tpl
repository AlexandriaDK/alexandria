<div id="content">

	<h2 class="datatitle">{$name|escape} ({$year})</h2>
	{$arrows}

{if $pic}
	<div style="float: right;">
		<a href="gfx/convent/l_{$id}.jpg">
			<img src="gfx/convent/s_{$id}.jpg" alt="Billede af {$name|escape}" title="Billede af {$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		(AKA: {$alias})
	</p>
{/if}

{if $place || $dateset}
	<p class="indata">
	{if $place}
		Sted: {$place}<br />
	{/if}
	{if $dateset}
		Dato: {$dateset}	
	{/if}
	</p>
{/if}

{if $partof != ""}
	<h3 class="parttitle">Del af: {$partof}</h3>
{/if}

{if $description != ""}
	<h3 class="parttitle">
		Om kongressen:
	</h3>
	
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

{if $filelist}
<h3 class="parttitle">Download:</h3>
	<table cellspacing="1" cellpadding="1" class="indata">
	{section name=i loop=$filelist}
	<tr>
	<td>
		{if $filelist[i].extension == "pdf"}<img src="gfx/ikon_pdf.gif" alt="PDF" title="PDF-dokument" width="20" height="20" />{/if}
		{if $filelist[i].extension == "doc"}<img src="gfx/ikon_doc.gif" alt="Word" title="Word-dokument" width="20" height="20" />{/if}
		{if $filelist[i].extension == "zip"}<img src="gfx/ikon_zip.gif" alt="Zip" title="Zip-arkiv" width="20" height="18" />{/if}
		{if $filelist[i].extension == "txt"}<img src="gfx/ikon_txt.gif" alt="Text" title="Tekst-dokument" width="16" height="16" />{/if}
	</td>
	<td>
		<a href="download/convent/{$id}/{$filelist[i].filename|rawurlencode}">{$filelist[i].description}</a>
	</td>
	<td>
	{if $filelist[i].filesizetext} ({$filelist[i].filesizetext} MB){/if}
	</td>
	</tr>
	{/section}
	</table>
{/if}

{if $confirmed == 0}
	<p class="indata">
		<i>
			Vi har ikke haft noget program for denne kongres, så scenarielisten er blot baseret på eksterne
			referencer, hukommelse, tidlige rygter, etc., og er derfor måske ikke komplet.<br />
			Har du en ændring, eller ligger du inde med et program, så
			<a href="rettelser?cat=convent&amp;data_id={$id}">send os en rettelse</a>.
		</i>
	</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $scenlist != ""}
	<h3 class="parttitle">
		Scenarier:
	</h3>

	{$scenlist}
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
