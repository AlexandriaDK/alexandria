<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="gfx/person/l_{$id}.jpg">
			<img src="gfx/person/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{if $alias != ""}
<p class="indata">
	(AKA: {$alias})
</p>
{/if}

{if $birth != "" || $death != ""}
<p class="indata">
{if $birth != ""}
	Født: {$birth}
{/if}
{if $birth != "" && $death != ""}
	<br>
{/if}
{if $death != ""}
	Død: {$death}
{/if}
</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $slist}
	<h3 class="parttitle">Scenarier:</h3>

	<table class="scenariolist indata">
	{section name=s loop=$slist}
		<tr>
			{if $slist[s].read}<td>{$slist[s].read}</td>{else}<td></td>{/if}
			{if $slist[s].gmed}<td>{$slist[s].gmed}</td>{else}<td></td>{/if}
			{if $slist[s].played}<td>{$slist[s].played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $slist[s].files}<a href="{$slist[s].link}" title="Dette scenarie kan downloades">💾</a>{/if}</td>
			<td style="text-align: center;">
			{if $slist[s].textsymbol}
			<span title="{$slist[s].icontitle|escape}">{$slist[s].textsymbol}</span>
			{elseif $slist[s].iconfile}
			<img src="gfx/{$slist[s].iconfile}" alt="{$slist[s].icontitle|escape}" title="{$slist[s].icontitle|escape}" width="{$slist[s].iconwidth}" height="{$slist[s].iconheight}" />
			{else}

			{/if}
			</td>
			<td><a href="{$slist[s].link}" class="scenarie">{$slist[s].title|escape}</a></td>
			<td style="padding-left: 10px;">{$slist[s].conlist}</td>
		</tr>
	{/section}
	</table>
{/if}

{if $award}
<h3 id="awards">Anerkendelser:</h3>
		{$award}
{/if}

{if $organizerlist}
<h3 class="parttitle" id="organizer">Arrangør-poster:</h3>
	<table class="organizerlist indata">
	{section name=i loop=$organizerlist}
	<tr>
	<td style="text-align: right;">
		<a href="data?con={$organizerlist[i].convent_id}" class="con">{$organizerlist[i].name|escape}</a>
	</td>
	<td style="padding-right: 10px">
		<a href="data?con={$organizerlist[i].convent_id}" class="con">({$organizerlist[i].year})</a>
	</td>
	<td>
		{$organizerlist[i].role|escape}
	</td>
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
