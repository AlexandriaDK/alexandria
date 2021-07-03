<div id="content">

	<h2 class="datatitle">{$name|escape}</h2>

{if $pic}
	<div style="float: right;">
		<a href="/gfx/person/l_{$id}.jpg">
			<img src="/gfx/person/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
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
	<h3 class="parttitle">{$_games|ucfirst}</h3>

	<table class="scenariolist indata">
	{foreach from=$slist item=$game}
		<tr>
			{if $game.read}<td>{$game.read}</td>{else}<td></td>{/if}
			{if isset($game.gmed) && $game.gmed}<td>{$game.gmed}</td>{else}<td></td>{/if}
			{if $game.played}<td>{$game.played}</td><td style="width: 5px;">&nbsp;</td>{/if}
			<td>{if $game.files}<a href="{$game.link}" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
			<td style="text-align: center;">
			{if $game.textsymbol}
			<span title="{$game.icontitle|escape}">{$game.textsymbol}</span>
			{elseif $game.iconfile}
			<img src="/gfx/{$game.iconfile}" alt="{$game.icontitle|escape}" title="{$game.icontitle|escape}" width="{$game.iconwidth}" height="{$game.iconheight}" />
			{else}

			{/if}
			</td>
			<td><a href="{$game.link}" class="scenarie" title="{$game.origtitle|escape}">{$game.title|escape}</a></td>
			<td style="padding-left: 10px;">{$game.conlist}</td>
		</tr>
	{/foreach}
	</table>
{/if}

{if $award}
<h3 id="awards">{$_p_awards}</h3>
		{$award}
{/if}

{if $organizerlist}
<h3 class="parttitle" id="organizer">{$_p_organizerroles}</h3>
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

{if $articlesfrom}
<h3 class="parttitle">{$_p_articles}</h3>
	<table id="personarticles">
	{foreach $articlesfrom as $article}
	<tr>
	<td>{if $article.sce_id}<a href="data?scenarie={$article.sce_id}">{$article.title|escape}</a>{else}{$article.title|escape}{/if}</td>
	<td>{$article.role|escape}</td>
	<td>{if $article.page}{$_file_page} {$article.page|escape}{/if}</td>
	<td><a href="magazines?issue={$article.issue_id}">{$article.issuetitle|escape}</a>{if $article.releasetext} ({$article.releasetext|escape}){/if}</td>
	<td class="magazine"><a href="magazines?id={$article.magazine_id}">{$article.magazinename|escape}</a></td>
	</tr>
	{/foreach}
	</table>
{/if}

{include file="articlereference.tpl"}
{include file="trivialink.tpl"}
{include file="internal.tpl"}
{include file="updatelink.tpl"}

</div>
