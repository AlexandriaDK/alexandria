<div id="content">

	<h2 class="datatitle">{$name|escape} ({$year})</h2>
	{$arrows}

{if $pic}
	<div style="float: right;">
		<a href="gfx/convent/l_{$id}.jpg">
			<img src="gfx/convent/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
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
		Sted: {$place}<br>
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

{include file="filelist.tpl"}

{if $confirmed == 0}
	<p class="indata">
		<i>
			Vi har ikke haft noget program for denne kongres, så oversigten over spil er blot baseret på eksterne
			referencer, hukommelse, tidlige rygter, etc., og er derfor måske ikke komplet.<br>
			Har du en ændring, eller ligger du inde med et program, så
			<a href="rettelser?cat=convent&amp;data_id={$id}">send os en rettelse</a>.
		</i>
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
			Scenarier:
		</h3>
		</td></tr>
	{section name=i loop=$scenlistdata}

		<tr>
		<td>{$scenlistdata[i].userdyn.read}</td>
		<td>{$scenlistdata[i].userdyn.gmed}</td>
		<td>{$scenlistdata[i].userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $scenlistdata[i].filescount}<a href="data?scenarie={$scenlistdata[i].id}" alt="Download" title="Dette scenarie kan downloades">💾</a>{/if}</td>
		<td>{$scenlistdata[i].runsymbol}</td>
		<td><a href="data?scenarie={$scenlistdata[i].id}" class="scenarie">{$scenlistdata[i].title|escape}</a></td>
		<td style="padding-left: 10px">{$scenlistdata[i].authtml}{if $scenlistdata[i].autextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$scenlistdata[i].autextracount} yderligere personer">[…]</span><span class="authorlistextra">{$scenlistdata[i].autextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$scenlistdata[i].systemhtml}</td>
		</tr>
	{/section}
	{/if}
	{if $boardlist != "" }
		<tr><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="boardgames">
			Brætspil:
		</h3>
		</td></tr>
	{section name=i loop=$boardlistdata}

		<tr>
		<td>{$boardlistdata[i].userdyn.read}</td>
		<td>{$boardlistdata[i].userdyn.gmed}</td>
		<td>{$boardlistdata[i].userdyn.played}</td>
		<td style="width: 10px;"></td>
		<td>{if $boardlistdata[i].filescount}<a href="data?scenarie={$boardlistdata[i].id}" alt="Download" title="Regler til dette brætspil kan downloades">💾</a>{/if}</td>
		<td>{$boardlistdata[i].runsymbol}</td>
		<td><a href="data?scenarie={$boardlistdata[i].id}" class="scenarie">{$boardlistdata[i].title|escape}</a></td>
		<td style="padding-left: 10px">{$boardlistdata[i].authtml}{if $boardlistdata[i].autextracount}<br><span onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors" title="{$boardlistdata[i].autextracount} yderligere personer">[…]</span><span class="authorlistextra">{$boardlistdata[i].autextrahtml}{/if}</td>
		<td style="padding-left: 10px">{$boardlistdata[i].systemhtml}</td>
		</tr>
	{/section}
	{/if}

	</table>
{/if}

{if $award}
<h3 id="awards">Priser:</h3>
		{$award}
{/if}


{if $organizerlist || $editorganizers}
<h3 class="parttitle" id="organizers">Arrangører:</h3>
{if $editorganizers && !$editmode}
<p class="addorganizersyourself">
	<a href="/fblogin">Log ind</a> for at oprette arrangører.
</p>
{/if}

	<table class="indata">
	{section name=i loop=$organizerlist}
	<tr>
	<td style="padding-right: 10px">
		{$organizerlist[i].role|escape}
	</td>
	<td>
		{if $organizerlist[i].aut_id}
		<a href="data?person={$organizerlist[i].aut_id}" class="person">{$organizerlist[i].name|escape}</a>
		{else}
		{$organizerlist[i].aut_extra|escape}
		{/if}
	</td>
	{if $editmode}
	<td style="text-align: center;">
		{foreach $user_can_edit_organizers AS $acrel_id => $true}
		{if $organizerlist[i].id == $acrel_id}
			<a href="adm/user_organizers.php?convent={$id}&amp;acrel_id={$acrel_id}&amp;action=delete">[Slet]</a>
			{break}
		{/if}
		{/foreach}
	</td>
	{/if}
	</tr>
	{/section}
	
	{if $editmode}
	<form action="adm/user_organizers.php" method="post">
	<input type="hidden" name="convent" value="{$id}">
	<input type="hidden" name="action" value="add">
	<tr style="vertical-align: top">
	<td style="padding-bottom: 250px">
		<input type="text" name="role" value="" placeholder="Arrangør-rolle" autofocus>
	</td>
	<td>
		<input type="text" name="aut_text" value="" placeholder="Navn" class="tags" style="width: 250px;" >
	</td>
	<td>
		<input type="submit" value="Tilføj">
	</td>
	</tr>
	</form>
	{/if}
	</table>
{if $user_id && !$editmode}
	<p class="addorganizersyourself">
		<a href="data?con={$id}&amp;edit=organizers#organizers">Tilføj arrangører for denne kongres</a>
		</p>
{/if}

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
