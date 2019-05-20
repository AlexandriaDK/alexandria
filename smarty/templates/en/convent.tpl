<div id="content">

	<h2 class="datatitle">{$name|escape} ({$year})</h2>
	{$arrows}

{if $pic}
	<div style="float: right;">
		<a href="/gfx/convent/l_{$id}.jpg">
			<img src="/gfx/convent/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}" style="border: 1px solid black; margin: 2px;" />
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
		Location: {$place}<br>
	{/if}
	{if $dateset}
		Date: {$dateset}	
	{/if}
	</p>
{/if}

{if $partof != ""}
	<h3 class="parttitle">Part of: {$partof}</h3>
{/if}

{if $description != ""}
	<h3 class="parttitle">
		About the convention:
	</h3>
	
	<p class="indata">
		{$description|textlinks|nl2br}
	</p>
{/if}

{include file="filelist.tpl"}

{if $confirmed == 0}
	<p class="indata">
		<i>
			We haven't had access to a program for this convention. The list is simply based on external
			references, memory, rumors and so on and the list might not be completed.<br>
			If you have a correction or are in access of a program then
			<a href="rettelser?cat=convent&amp;data_id={$id}">please send us an update</a>.
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
			Scenarios:
		</h3>
		</td></tr>
	{$scenlist}
	{/if}
	{if $boardlist != "" }
		<tr><td colspan="8">
		<h3 class="parttitle" style="margin: 0px; padding: 0px" id="boardgames">
			Board games:
		</h3>
		</td></tr>
	{$boardlist}
	{/if}

	</table>
{/if}

{if $award}
<h3 id="awards">Awards:</h3>
		{$award}
{/if}


{if $organizerlist || $editorganizers}
<h3 class="parttitle" id="organizers">Organizers:</h3>
{if $editorganizers && !$editmode}
<p class="addorganizersyourself">
	<a href="/fblogin">Login</a> to add organizers.
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
			<a href="adm/user_organizers.php?convent={$id}&amp;acrel_id={$acrel_id}&amp;action=delete">[Remove]</a>
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
		<input type="text" name="role" value="" placeholder="Organizer role" autofocus>
	</td>
	<td>
		<input type="text" name="aut_text" value="" placeholder="Name" class="tags" style="width: 250px;" >
	</td>
	<td>
		<input type="submit" value="Add">
	</td>
	</tr>
	</form>
	{/if}
	</table>
{if $user_id && !$editmode}
	<p class="addorganizersyourself">
		<a href="data?con={$id}&amp;edit=organizers#organizers">Add organizers for this convention</a>
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
