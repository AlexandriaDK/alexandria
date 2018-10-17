{if $filelist}
<h3 class="parttitle">Download:</h3>
	<table class="indata">
	{section name=i loop=$filelist}
	<tr>
	<td>
		{if $filelist[i].extension == "pdf"}<img src="gfx/ikon_pdf.gif" alt="PDF" title="PDF-dokument" width="20" height="20" />{/if}
		{if $filelist[i].extension == "doc"}<img src="gfx/ikon_doc.gif" alt="Word" title="Word-dokument" width="20" height="20" />{/if}
		{if $filelist[i].extension == "zip"}<img src="gfx/icon_archive.svg" alt="Zip" title="Zip-arkiv" width="21" height="18" />{/if}
		{if $filelist[i].extension == "txt"}<img src="gfx/ikon_txt.gif" alt="Text" title="Tekst-dokument" width="16" height="16" />{/if}
		{if $filelist[i].extension == "mp3"}<img src="gfx/icon_music.svg" alt="MP3" title="Lydfil" width="20" height="20" />{/if}
		{if $filelist[i].extension == "pps"}<img src="gfx/ikon_pps.gif" alt="PPS" title="PowerPoint-dokument" width="20" height="20" />{/if}
		{if $filelist[i].extension == "jpg"}<span title="Billede">🖼️</span>{/if}
		{if $filelist[i].extension == "png"}<span title="Billede">🖼️</span>{/if}
		{if $filelist[i].extension == "gif"}<span title="Billede">🖼️</span>{/if}
	</td>
	<td>
		<a href="download/{$filedir}/{$id}/{$filelist[i].filename|rawurlencode}">{$filelist[i].description}</a>
	</td>
	<td>
	{if $filelist[i].filesizetext} ({$filelist[i].filesizetext} MB){/if}
	</td>
	</tr>
	{/section}
	</table>
{/if}

