{* This file is not used anymore! Use game.tpl instead *}
<div id="content">

	<h2 class="datatitle">{$title|escape}</h2>

{if $pic}
	<div class="thumb">
		<a href="gfx/scenarie/l_{$id}.jpg">
			<img src="gfx/scenarie/s_{$id}.jpg" alt="Forside til {$title|escape}" title="Forside til {$title|escape}">
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
			<a href="adm/user_tags.php?scenario={$id}&tag_id={$tag_id}&action=delete" title="Slet tag">[Slet]</a></span>
		{/if}
	<a href="data?tag={$tag|rawurlencode}">{$tag|escape}</a>
	</li>
	{/foreach}
	{* This part is really only for users logged in *}
	{if $user_id}
	<li><a href="#" onclick="$('#tag_input_li').toggle(100).focus();$('#tag_input').focus();" title="Tilføj tag" accesskey="t">+</a></li>
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
		Deltagere: {$participants|escape}
		{if $user_can_edit_participants || $user_admin || $user_editor}
		- <a href="#" onclick="$('#form_participants').toggle(); return false;">ret antal spillere</a>
		{/if}
	{/if}
	{if ($user_id) && $participants == ""}
		Deltagere: Ukendt. <a href="#" onclick="document.getElementById('form_participants').style.display='block'; return false;">Tilføj antal spillere</a>
	{/if}
	</p>
	{if ($user_id) }
		<div id="form_participants" style="display: none">
		<form action="adm/user_participants.php" method="post">
		<table>
		<tr><td>Antal GM's:</td><td><input type="text" name="gms" size="2" value="{$gms}" /></td></tr>
		<tr><td>Antal spillere:</td><td><input type="text" name="players" size="2" value="{$players}" /></td></tr>
		<tr><td></td><td><input type="hidden" name="scenarie" value="{$id}" /><input type="submit" value="Ret" /></td></tr>
		</table>
		<p><span style="font-size: 0.8em;">Du kan indtaste et variabelt antal spillere: Angiv fx <i>4-6</i> for 4 til 6 spillere</span></p>
		</form>
		</div>
	{/if}


{/if}


{if $aut_extra != ""}
	<h3 class="parttitle">
		Arrangeret af:
	</h3>
	<p class="indata">
		{$aut_extra}
	</p>
{/if}

{if $forflist != ""}
	<h3 class="parttitle">
		Af:
	</h3>
		{$forflist}
{/if}

{include file="filelist.tpl"}

{if $description != ""}
	<h3 class="parttitle">
		Foromtale:
	</h3>
	
	<p class="indata">
		{$description|escape|textlinks|nl2br}
	</p>
{/if}

{if $conlist != ""}
	<h3 class="parttitle">
		Spillet på:
	</h3>
	<table>
	{$conlist}
	</table>
{/if}

{if $runlist != ""}
	<h3 class="parttitle">
		{if $conlist == ""}Afviklinger:{else}Afviklinger derudover:{/if}
	</h3>
	<p class="indata">
		{$runlist}
	</p>

{/if}

{if $award}
<h3 id="awards">Priser:</h3>
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
