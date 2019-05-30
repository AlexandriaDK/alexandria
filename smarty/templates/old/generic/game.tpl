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
			<img src="gfx/scenarie/s_{$id}.jpg" alt="{$_sce_frontpagefor} {$title|escape}" title="{$_sce_frontpagefor} {$title|escape}">
		</a>
	</div>
{/if}

{if $alias != ""}
	<p class="indata">
		({$_aka}: {$alias})
	</p>
{/if}

{if $sysstring != "" || $genre != "" || $participants != "" || $tags || $user_id}
	<form action="adm/user_tags.php" method="post">
	<ul class="taglist">
	{foreach $tags AS $tag_id => $tag}
	<li>
		{if $user_can_edit_tag[$tag_id]}
			<span id="tagdelete_{$tag_id}" class="delete"> 
			<a href="adm/user_tags.php?scenario={$id}&tag_id={$tag_id}&action=delete" title="{$_sce_removetag|escape}">[{$_remove|ucfirst|escape}]</a></span>
		{/if}
	<a href="data?tag={$tag|rawurlencode}">{$tag|escape}</a>
	</li>
	{/foreach}
	{* This part is really only for users logged in *}
	{if $user_id}
	<li><a href="#" onclick="$('#tag_input_li').toggle(100).focus();$('#tag_input').focus();" title="{$_sce_addtag}" accesskey="t">+</a></li>
	<li style="display: none;" id="tag_input_li"><input type="hidden" name="scenario" value="{$id}"><input type="hidden" name="action" value="add"><input type="text" name="tag" id="tag_input" class="newtag" placeholder="E.g. Grind Night"></li>
	{/if}
	</ul>	
	</form>
	<p class="indata">
	{if $sysstring != ""}
		{$_rpgsystem|ucfirst}: {$sysstring}
		<br>
	{/if}
	{if $genre != ""}
		{$_genre|ucfirst}: {$genre}
		<br>
	{/if}
	{if $participants != ""}
		{$_participants|ucfirst}: {$participants|escape}
		{if $user_can_edit_participants || $user_admin || $user_editor}
		- <a href="#" onclick="$('#form_participants').toggle(); return false;">{$_sce_editplayerno}</a>
		{/if}
	{/if}
	{if ($user_id) && $participants == ""}
		{$_participants|ucfirst}: {$_unknown|ucfirst}. <a href="#" onclick="document.getElementById('form_participants').style.display='block'; return false;">{$_sce_addplayerno}</a>
	{/if}
	</p>
	{if ($user_id) }
		<div id="form_participants" style="display: none">
		<form action="adm/user_participants.php" method="post">
		<table>
		<tr><td>{$_sce_nogms}:</td><td><input type="text" name="gms" size="2" value="{$gms}" /></td></tr>
		<tr><td>{$_sce_noplayers}:</td><td><input type="text" name="players" size="2" value="{$players}" /></td></tr>
		<tr><td></td><td><input type="hidden" name="scenarie" value="{$id}" /><input type="submit" value="{$_edit|ucfirst|escape}" /></td></tr>
		</table>
		<p><span class="participantshint">{$_sce_playerhint}</span></p>
		</form>
		</div>
	{/if}


{/if}


{if $aut_extra != ""}
	<h3 class="parttitle">
		{$_sce_organizedby}
	</h3>
	<p class="indata">
		{$aut_extra}
	</p>
{/if}

{if $forflist != ""}
	<h3 class="parttitle">
		{$_sce_by}
	</h3>
		{$forflist}
{/if}

{include file="filelist.tpl"}

{if $descriptions}
<h3 class="parttitle">
	{$_sce_description}
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
		{$_sce_playedat}
	</h3>
	<table>
	{$conlist}
	</table>
{/if}

{if $runlist != ""}
	<h3 class="parttitle">
		{if $conlist == ""}{$_sce_runs}{else}{$_sce_furtherruns}{/if}
	</h3>
	<p class="indata">
		{$runlist}
	</p>

{/if}

{if $award}
<h3 id="awards">{$_awards|ucfirst}</h3>
		{$award}
{/if}

{if $trivia}
<h3 class="parttitle">{$_trivia|ucfirst}</h3>
<ul class="indatalist">
{$trivia}
</ul>
{/if}

{if $link}
<h3 class="parttitle">{$_links|ucfirst}</h3>
<p class="indata">
{$link}
</p>
{/if}

{include file="updatelink.tpl"}


</div>
