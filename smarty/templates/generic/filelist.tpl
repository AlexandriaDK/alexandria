{if $filelist}
<h3 class="parttitle">{$_download|ucfirst}</h3>
	<table class="indata">
	{section name=i loop=$filelist}
	<tr>
	<td>
		{if $filelist[i].extension == "pdf"}<img src="/gfx/icon_pdf.svg" alt="PDF" title="{$_file_pdf}" width="20" height="20" />{/if}
		{if $filelist[i].extension == "doc"}<img src="/gfx/ikon_doc.gif" alt="Word" title="{$_file_doc}" width="20" height="20" />{/if}
		{if $filelist[i].extension == "zip"}<img src="/gfx/icon_archive.svg" alt="Zip" title="{$_file_zip}" width="21" height="18" />{/if}
		{if $filelist[i].extension == "txt"}<img src="/gfx/ikon_txt.gif" alt="Text" title="{$_file_text}" width="16" height="16" />{/if}
		{if $filelist[i].extension == "mp3"}<img src="/gfx/icon_music.svg" alt="MP3" title="{$_file_sound}" width="20" height="20" />{/if}
		{if $filelist[i].extension == "pps"}<img src="/gfx/ikon_pps.gif" alt="PPS" title="{$_file_powerpoint}" width="20" height="20" />{/if}
		{if $filelist[i].extension == "jpg"}<span title="{$_picture|ucfirst}">🖼️</span>{/if}
		{if $filelist[i].extension == "png"}<span title="{$_picture|ucfirst}">🖼️</span>{/if}
		{if $filelist[i].extension == "gif"}<span title="{$_picture|ucfirst}">🖼️</span>{/if}
	</td>
	<td>
		<a href="/download/{$filedir}/{$id}/{$filelist[i].filename|rawurlencode}">{$filelist[i].description}</a>
	</td>
	<td>
	{if isset($filelist[i].filesizetext) }
		({$filelist[i].filesizetext} MB)
	{/if}
	</td>
	</tr>
	{/section}
	</table>
{/if}

