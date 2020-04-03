<div id="content">

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

{if $alias != ""}
	<p class="indata">
		({$_aka}: {$alias})
	</p>
{/if}

{if $place || $dateset}
	<p class="indata">
	{if $place}
		{$_location|ucfirst}: {$place}<br>
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
		<a href="rettelser?cat=convent&amp;data_id={$id}">{$_con_sendcorrection}</a>.
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

{if $scenlist != "" || $boardlist != ""}
	<table class="indata">
	{if $scenlist != "" }
		<tr><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="roleplay">
			{$_scenarios|ucfirst}:
		</h3>
		</td></tr>
	{foreach from=$scenlistdata item=$scenarios}

		<tr>
		<td>{$scenarios.userdyn.read}</td>
		<td>{$scenarios.userdyn.gmed}</td>
		<td>{$scenarios.userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $scenarios.filescount}<a href="data?scenarie={$scenarios.id}" alt="Download" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
		<td>{$scenarios.runsymbol}</td>
		<td><a href="data?scenarie={$scenarios.id}" class="scenarie">{$scenarios.title|escape}</a></td>
		<td style="padding-left: 10px">{$scenarios.authtml}{if $scenarios.autextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$scenarios.autextracount} {$_con_morepersons}">[…]</span><span class="authorlistextra">{$scenarios.autextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$scenarios.systemhtml}</td>
		</tr>
	{/foreach}
	{/if}
	{if $boardlist != "" }
		<tr><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="boardgames">
			{$_boardgames|ucfirst}:
		</h3>
		</td></tr>
	{foreach from=$boardlistdata item=$boardgames}

		<tr>
		<td>{$boardgames.userdyn.read}</td>
		<td>{$boardgames.userdyn.gmed}</td>
		<td>{$boardgames.userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $boardgames.filescount}<a href="data?scenarie={$boardgames.id}" alt="Download" title="{$_sce_bgdownloadable|escape}">💾</a>{/if}</td>
		<td>{$boardgames.runsymbol}</td>
		<td><a href="data?scenarie={$boardgames.id}" class="scenarie">{$boardgames.title|escape}</a></td>
		<td style="padding-left: 10px">{$boardgames.authtml}{if $boardgames.autextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$boardgames.autextracount} {$_con_morepersons}">[…]</span><span class="authorlistextra">{$boardgames.autextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$boardgames.systemhtml}</td>
		</tr>
	{/foreach}
	{/if}

	</table>
{/if}

{if $award}
<h3 id="awards">{$_con_awards}:</h3>
		{$award}
{/if}

{if $organizerlist || $editorganizers}
<h3 class="parttitle" id="organizers">{$_organizers|ucfirst}</h3>
{if $editorganizers && !$editmode}
<p class="addorganizersyourself">
	<a href="/fblogin">{$_con_login}</a> {$_con_addorganizer}
</p>
{/if}

	<table class="indata">
	{foreach from=$organizerlist item=$ol}
	<tr>
	<td style="padding-right: 10px">
		{$ol.role|escape}
	</td>
	<td>
		{if $ol.aut_id}
		<a href="data?person={$ol.aut_id}" class="person">{$ol.name|escape}</a>
		{else}
		{$ol.aut_extra|escape}
		{/if}
	</td>
	{if $editmode}
	<td style="text-align: center;">
		{foreach $user_can_edit_organizers AS $acrel_id => $true}
		{if $ol.id == $acrel_id}
			<a href="adm/user_organizers.php?convent={$id}&amp;acrel_id={$acrel_id}&amp;action=delete">[{$_remove}]</a>
			{break}
		{/if}
		{/foreach}
	</td>
	{/if}
	</tr>
	{/foreach}
	
	{if $editmode}
	<form action="adm/user_organizers.php" method="post">
	<input type="hidden" name="convent" value="{$id}">
	<input type="hidden" name="action" value="add">
	<tr style="vertical-align: top">
	<td style="padding-bottom: 250px">
		<input type="text" name="role" value="" placeholder="{$_con_organizerrole|escape}" autofocus>
	</td>
	<td>
		<input type="text" name="aut_text" value="" placeholder="{$_name|escape}" class="tags" style="width: 250px;" >
	</td>
	<td>
		<input type="submit" value="{$_add|escape}">
	</td>
	</tr>
	</form>
	{/if}
	</table>
{if $user_id && !$editmode}
	<p class="addorganizersyourself">
		<a href="data?con={$id}&amp;edit=organizers#organizers">{$_con_addorganizers}</a>
		</p>
{/if}

{/if}

{include file="trivialink.tpl"}
{include file="updatelink.tpl"}

</div>
