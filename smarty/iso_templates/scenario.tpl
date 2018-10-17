<div id="content">

	<h2 class="datatitle">{$title|escape}</h2>

{* Disabling pictures *}
{if $pic}
	<div style="float: right;">
		<a href="gfx/scenarie/l_{$id}.jpg">
			<img src="gfx/scenarie/s_{$id}.jpg" alt="Billede af {$title|escape}" title="Billede af {$title|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		(AKA: {$alias})
	</p>
{/if}

{if $sysstring != "" || $genre != ""}
	<p class="indata">
	{if $sysstring != ""}
		System: {$sysstring}
	{/if}
	{if $sysstring != "" && $genre != ""}
		<br />
	{/if}
	{if $genre != ""}
		Genre: {$genre}
	{/if}
	</p>
{/if}

{if $aut_extra != ""}
	<h3 class="parttitle">
		Arrangeret af:
	</h3>
	<p class="indata">
		{$aut_extra}
	</p>
{/if}

{if $forflist != ""}
	<h3 class="parttitle">
		Af:
	</h3>
		{$forflist}
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
		<a href="download/scenario/{$id}/{$filelist[i].filename|rawurlencode}">{$filelist[i].description}</a>
	</td>
	<td align="right">
	{if $filelist[i].filesizetext} ({$filelist[i].filesizetext}&nbsp;MB){/if}
	</td>
	</tr>
	{/section}
	</table>
{/if}


{if $description != ""}
	<h3 class="parttitle">
		Foromtale:
	</h3>
	
	<p class="indata">
		{$description|escape|nl2br}
	</p>
{/if}

{if $conlist != ""}
	<h3 class="parttitle">
		Spillet på:
	</h3>
	<p class="indata">
		{$conlist}
	</p>
{/if}

{if $runlist != ""}
	<h3 class="parttitle">
		{if $conlist == ""}Afviklet:{else}Derudover afviklet:{/if}
	</h3>
	<p class="indata">
		{$runlist}
	</p>

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
