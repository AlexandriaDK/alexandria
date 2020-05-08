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
	({$_aka}: {$alias})
</p>
{/if}

{if $birth != "" || $death != ""}
<p class="indata">
{if $birth != ""}
	{$_p_born}: {$birth}
	{if $age != "" && $death == ""}({$_person_age|sprintf:$age|escape}){/if}
{/if}
{if $birth != "" && $death != ""}
	<br>
{/if}
{if $death != ""}
	{$_p_died}: {$death}
	{if $age != "" && $birth != ""}({$_person_age|sprintf:$age|escape}){/if}
{/if}
</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

{if $slist}
	<h3 class="parttitle">{$_scenarios|ucfirst}</h3>

	<table class="scenariolist indata">
	{foreach from=$slist item=$scenario}
		<tr>
			{if $scenario.read}<td>{$scenario.read}</td>{else}<td></td>{/if}
			{if isset($scenario.gmed) && $scenario.gmed}<td>{$scenario.gmed}</td>{else}<td></td>{/if}
			{if $scenario.played}<td>{$scenario.played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $scenario.files}<a href="{$scenario.link}" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
			<td style="text-align: center;">
			{if $scenario.textsymbol}
			<span title="{$scenario.icontitle|escape}">{$scenario.textsymbol}</span>
			{elseif $scenario.iconfile}
			<img src="gfx/{$scenario.iconfile}" alt="{$scenario.icontitle|escape}" title="{$scenario.icontitle|escape}" width="{$scenario.iconwidth}" height="{$scenario.iconheight}" />
			{else}

			{/if}
			</td>
			<td><a href="{$scenario.link}" class="scenarie">{$scenario.title|escape}</a></td>
			<td style="padding-left: 10px;">{$scenario.conlist}</td>
		</tr>
	{/foreach}
	</table>
{/if}

{if $award}
<h3 id="awards">{$_p_awards}</h3>
		{$award}
{/if}

{if $organizerlist}
<h3 class="parttitle" id="organizer">{$_p_organizerroles}:</h3>
	<table class="organizerlist indata">
	{foreach from=$organizerlist item=$con}
	<tr>
	<td style="text-align: right;" {if $con.cancelled}class="cancelled"{/if}>
		{con id=$con.convent_id name=$con.name begin=$con.begin end=$con.end }
	</td>
	<td style="padding-right: 10px" {if $con.cancelled}class="cancelled"{/if}>
		{con id=$con.convent_id year=$con.year }
	</td>
	<td>
		{$con.role|escape}
	</td>
	</tr>
	{/foreach}
	</table>
{/if}

{include file="trivialink.tpl"}
{include file="internal.tpl"}
{include file="updatelink.tpl"}

</div>
