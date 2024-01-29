<div id="content">
{include file="originalsearch.tpl"}

	<h2 class="datatitle{if $cancelled} cancelled{/if}">{$name|escape} ({$year|yearname})</h2>
	<div class="arrows">
{if $arrowset.prev.active}
	<a href="data?con={$arrowset.prev.conid}" title="{$arrowset.prev.name|escape}" rel="prev">←</a>
{else}
	<span class="inactive">←</span>
{/if}
{if $arrowset.next.active}
	<a href="data?con={$arrowset.next.conid}" title="{$arrowset.next.name|escape}" rel="next">→</a>
{else}
	<span class="inactive">→</span>
{/if}
	</div>
{if $pic}
	<div style="float: right;">
		<a href="/gfx/convent/l_{$id}.jpg">
			<img src="/gfx/convent/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
		</a>
	</div>
{/if}

{include file="alias.tpl"}

{if $place || $dateset || $countrycode}
	<p class="indata">
	{if $place || $countrycode}
		{$_location|ucfirst}: {if $place}{$place}{/if}{if $place && $countrycode}, {/if}{if $countrycode}{$countrycode|getCountryNameFallback}{/if}
		{if $haslocations}<a href="locations?convention_id={$id}">🗺️</a>{/if}
		<br>
	{/if}
	{if $dateset}
		{$_date|ucfirst}: {$dateset}
	{/if}
	</p>
{/if}

{if $partof != ""}
	<h3 class="parttitle">{$_con_partof}: {$partof}</h3>
{/if}

{if $cancelled}
	<h3 class="cancelnotice">
			{$_con_cancelled|nl2br}
	</h3>
{/if}

{if $description != ""}
	<h3 class="parttitle">
		{$_con_about}:
	</h3>
	
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

{include file="filelist.tpl"}

{if $confirmed == 0}
	<p class="indata needhelp">
		{$_con_noinfo|nl2br}
		<a href="rettelser?cat=convention&amp;data_id={$id}">{$_con_sendcorrection}</a>.
	</p>
{elseif $confirmed == 1}
	<p class="indata needhelp">
		{$_con_helpwithlist|nl2br|sprintf:'kontakt'}
	</p>
{elseif $confirmed == 3}
	<p class="indata needhelp">
		{$_con_helpwithcontent|nl2br|sprintf:'kontakt'}
	</p>
{/if}

	{* clear for picture *}
	<div style="clear: both;">
	</div>

<!--
<script>
$(document).ready(function(){
  $("#filterSearch").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".conlist tr:not(.listhead)").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

<p style="text-align: right; margin: 0px; padding: 0px;"><input id="filterSearch" type="text" placeholder="🝖 Filter list"></p>
-->
{if $gamelistdata['scenarios'] || $gamelistdata['boardgames'] || $gamelistdata['fastavaljunior'] }
	<table class="indata conlist">
	{if $gamelistdata['scenarios']}
	
		<tr class="listhead"><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="roleplay">
			{$_scenarios|ucfirst}
		</h3>
		</td></tr>
	{foreach from=$gamelistdata['scenarios'] item=$scenarios}
		<tr>
		<td>{$scenarios.userdyn.read}</td>
		<td>{$scenarios.userdyn.gmed}</td>
		<td>{$scenarios.userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $scenarios.filescount}<a href="data?scenarie={$scenarios.id}" alt="Download" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
		<td>{$scenarios.runsymbol}</td>
		<td><a href="data?scenarie={$scenarios.id}" class="game">{$scenarios.title|escape}</a></td>
		<td style="padding-left: 10px">{$scenarios.personhtml}{if $scenarios.personextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$scenarios.personextracount} {$_con_morepersons}">[…]</span><span class="authorlistextra">{$scenarios.personextrahtml}{/if}</td>
		<td style="padding-left: 10px">{if $scenarios.system_id}<a href="data?system={$scenarios.system_id}" class="system">{$scenarios.system_translation}</a>{if $scenarios.system_extra} {$scenarios.system_extra|escape}{/if}{elseif $scenarios.system_extra}{$scenarios.system_extra|escape}{/if}</td>
	{/foreach}
	{/if}
	{if $gamelistdata['boardgames']}
		<tr class="listhead"><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="boardgames">
			{$_boardgames|ucfirst}
		</h3>
		</td></tr>
	{foreach from=$gamelistdata['boardgames'] item=$boardgames}
		<tr>
		<td>{$boardgames.userdyn.read}</td>
		<td></td>
		<td>{$boardgames.userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $boardgames.filescount}<a href="data?scenarie={$boardgames.id}" alt="Download" title="{$_sce_bgdownloadable|escape}">💾</a>{/if}</td>
		<td>{$boardgames.runsymbol}</td>
		<td><a href="data?scenarie={$boardgames.id}" class="game">{$boardgames.title|escape}</a></td>
		<td style="padding-left: 10px">{$boardgames.personhtml}{if $boardgames.personextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$boardgames.personextracount} {$_con_morepersons}">[…]</span><span class="authorlistextra">{$boardgames.personextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$boardgames.systemhtml}</td>
		</tr>
	{/foreach}
	{/if}
	{if $gamelistdata['fastavaljunior']}
		<tr class="listhead"><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="fastavaljuniorgames">
			{$_fastavaljuniorgames|ucfirst}
		</h3>
		</td></tr>
	{foreach from=$gamelistdata['fastavaljunior'] item=$fastavaljuniorgames}
		<tr>
		<td>{$fastavaljuniorgames.userdyn.read}</td>
		<td>{if ! $fastavaljuniorgames.boardgame}{$fastavaljuniorgames.userdyn.gmed}{/if}</td>
		<td>{$fastavaljuniorgames.userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $fastavaljuniorgames.filescount}<a href="data?scenarie={$fastavaljuniorgames.id}" alt="Download" title="{$_sce_bgdownloadable|escape}">💾</a>{/if}</td>
		<td>{$fastavaljuniorgames.runsymbol}</td>
		<td><a href="data?scenarie={$fastavaljuniorgames.id}" class="game">{$fastavaljuniorgames.title|escape}</a></td>
		<td style="padding-left: 10px">{$fastavaljuniorgames.personhtml}{if $fastavaljuniorgames.personextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$fastavaljuniorgames.personextracount} {$_con_morepersons}">[…]</span><span class="authorlistextra">{$fastavaljuniorgames.personextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$fastavaljuniorgames.systemhtml}</td>
		</tr>
	{/foreach}
	{/if}
	</table>
{/if}

{if $award}
<h3 id="awards">{$_con_awards}</h3>
		{$award}
{/if}

<h3 class="parttitle{if ! $organizerlist && ! $editorganizers} organizerhidden{/if}" id="organizers">{$_organizers|ucfirst}</h3>
	<table class="indata">
	{foreach from=$organizerlist item=$ol}
		<tr>
		<td style="padding-right: 10px">
			{$ol.role|escape}
		</td>
		<td>
			{if $ol.person_id}
			<a href="data?person={$ol.person_id}" class="person">{$ol.name|escape}</a>
			{else}
			{$ol.person_extra|escape}
			{/if}
		</td>
		<td style="text-align: center;">
			{foreach $user_can_edit_organizers AS $pcrel_id => $true}
			{if $ol.id == $pcrel_id}
				<a href="adm/user_organizers.php?convention={$id}&amp;pcrel_id={$pcrel_id}&amp;action=delete&amp;token={$token}">[{$_remove}]</a>
				{break}
			{/if}
			{/foreach}
		</td>
		</tr>
	{/foreach}

{if $user_id}
	<form action="adm/user_organizers.php" method="post">
	<input type="hidden" name="convention" value="{$id}">
	<input type="hidden" name="token" value="{$token}">
	<input type="hidden" name="action" value="add">
	<tr style="vertical-align: top" {if ! $editorganizers}class="organizerhidden"{/if}>
	<td style="padding-bottom: 250px">
		<input type="text" name="role" id="neworganizer" placeholder="{$_con_organizerrole|escape}" autofocus>
	</td>
	<td>
		<input type="text" name="person_text" value="" placeholder="{$_name|escape}" class="peopletags" style="width: 250px;" >
	</td>
	<td>
		<input type="submit" value="{$_add|escape}">
	</td>
	</tr>
	</form>
{/if}
</table>

{if $organizerlist && isset($user_id)}
	<p class="addorganizersyourself">
		<a href="#neworganizer">{$_con_addorganizers}</a>
	</p>
{/if}

{include file="articlereference.tpl"}
{include file="trivialink.tpl"}
{include file="internal.tpl"}
{include file="updatelink.tpl"}

</div>
