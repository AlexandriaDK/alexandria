<script>
$( function() {
	$( "#tabs" ).tabs();
} );
</script>

<div id="content">

	<h2 class="datatitle">{$title|escape}</h2>

{if $pic}
	<div class="thumb">
		<a href="gfx/scenarie/l_{$id}.jpg">
			<img src="gfx/scenarie/s_{$id}.jpg" alt="Front page for {$title|escape}" title="Front page for {$title|escape}">
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		(AKA: {$alias})
	</p>
{/if}

{if $sysstring != "" || $genre != "" || $participants != "" || $tags || $user_id}
	<form action="adm/user_tags.php" method="post">
	<ul class="taglist">
	{foreach $tags AS $tag_id => $tag}
	<li>
		{if $user_can_edit_tag[$tag_id]}
			<span id="tagdelete_{$tag_id}" class="delete"> 
			<a href="adm/user_tags.php?scenario={$id}&tag_id={$tag_id}&action=delete" title="Remove tag">[Remove]</a></span>
		{/if}
	<a href="data?tag={$tag|rawurlencode}">{$tag|escape}</a>
	</li>
	{/foreach}
	{* This part is really only for users logged in *}
	{if $user_id}
	<li><a href="#" onclick="$('#tag_input_li').toggle(100).focus();$('#tag_input').focus();" title="Add tag" accesskey="t">+</a></li>
	<li style="display: none;" id="tag_input_li"><input type="hidden" name="scenario" value="{$id}"><input type="hidden" name="action" value="add"><input type="text" name="tag" id="tag_input" class="newtag" placeholder="E.g. Grind Night"></li>
	{/if}
	</ul>	
	</form>
	<p class="indata">
	{if $sysstring != ""}
		System: {$sysstring}
		<br>
	{/if}
	{if $genre != ""}
		Genre: {$genre}
		<br>
	{/if}
	{if $participants != ""}
		Participants: {$participants|escape}
		{if $user_can_edit_participants || $user_admin || $user_editor}
		- <a href="#" onclick="$('#form_participants').toggle(); return false;">edit participants</a>
		{/if}
	{/if}
	{if ($user_id) && $participants == ""}
		Participants: Unknown. <a href="#" onclick="document.getElementById('form_participants').style.display='block'; return false;">Add participants</a>
	{/if}
	</p>
	{if ($user_id) }
		<div id="form_participants" style="display: none">
		<form action="adm/user_participants.php" method="post">
		<table>
		<tr><td>GMs:</td><td><input type="text" name="gms" size="2" value="{$gms}" /></td></tr>
		<tr><td>Players:</td><td><input type="text" name="players" size="2" value="{$players}" /></td></tr>
		<tr><td></td><td><input type="hidden" name="scenarie" value="{$id}" /><input type="submit" value="Update" /></td></tr>
		</table>
		<p><span style="font-size: 0.8em;">You can enter a range of players, e.g. <i>4-6</i> for 4 to 6 players</span></p>
		</form>
		</div>
	{/if}


{/if}


{if $aut_extra != ""}
	<h3 class="parttitle">
		Organized by:
	</h3>
	<p class="indata">
		{$aut_extra}
	</p>
{/if}

{if $forflist != ""}
	<h3 class="parttitle">
		By:
	</h3>
		{$forflist}
{/if}

{include file="filelist.tpl"}

{if $descriptions}
<h3 class="parttitle">
	Description:
</h3>
{if $descriptionscount gt 1}
<div id="tabs">
<ul>
{foreach $descriptions AS $d_id => $d}
<li><a href="#description-{$d_id}">{$d.language|escape}{if $d.note} ({$d.note}){/if}</a>
{/foreach}
</ul>

{foreach $descriptions AS $d_id => $d}
	<div id="description-{$d_id}">
	<p class="indata">
		{$d.description|escape|textlinks|nl2br}
	</p>
	</div>
{/foreach}
</div>
{else}
	<p class="indata">
		{$descriptions[0].description|escape|textlinks|nl2br}
	</p>
{/if}
{/if}

{if $conlist != ""}
	<h3 class="parttitle">
		Played at:
	</h3>
	<table>
	{$conlist}
	</table>
{/if}

{if $runlist != ""}
	<h3 class="parttitle">
		{if $conlist == ""}Runs:{else}Other runs:{/if}
	</h3>
	<p class="indata">
		{$runlist}
	</p>

{/if}

{if $award}
<h3 id="awards">Awards:</h3>
		{$award}
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
