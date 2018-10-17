<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="gfx/person/l_{$id}.jpg">
			<img src="gfx/person/s_{$id}.jpg" alt="Billede af {$name|escape}" title="Billede af {$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
<p class="indata">
	(AKA: {$alias})
</p>
{/if}

{if $birth != ""}
<p class="indata">
	Født: {$birth}
</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $slist}
	<h3 class="parttitle">Scenarier:</h3>

	<table border="0" cellspacing="1" cellpadding="1" class="indata">
	{section name=s loop=$slist}
		<tr valign="top">
			{if $slist[s].seen}<td>{$slist[s].seen}</td>{/if}
			{if $slist[s].gmed}<td>{$slist[s].gmed}</td>{/if}
			{if $slist[s].played}<td>{$slist[s].played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $slist[s].files}<img src="/gfx/ikon_download.gif" alt="Download" title="Dette scenarie kan downloades" width="15" height="15" />{/if}</td>
			<td align="center"><img src="gfx/{$slist[s].iconfile}" alt="{$slist[s].icontitle}" title="{$slist[s].icontitle}" width="{$slist[s].iconwidth}" height="{$slist[s].iconheight}" /></td>
			<td><a href="{$slist[s].link}" class="scenarie">{$slist[s].title|escape}</a></td>
			<td style="padding-left: 10px;">{$slist[s].conlist}</td>
		</tr>
	{/section}
	</table>
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
