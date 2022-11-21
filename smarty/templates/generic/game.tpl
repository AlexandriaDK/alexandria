<script>
$( function() {
	$( "#tabs" ).tabs();
} );
</script>

<div id="content">
{include file="originalsearch.tpl"}

	<h2 class="datatitle">{$title|escape}</h2>

{if $pic}
	<div class="thumb">
		<a href="/gfx/scenarie/l_{$id}.jpg">
			<img src="/gfx/scenarie/s_{$id}.jpg" alt="{$_sce_frontpagefor} {$title|escape}" title="{$_sce_frontpagefor} {$title|escape}">
		</a>
	</div>
{/if}

{include file="alias.tpl"}

{if $sysstring != "" || $genre != "" || $participants != "" || $tags || isset($user_id)}
	{if isset($user_id)}
	<form action="adm/user_tags.php" method="post">
	<input type="hidden" name="token" value="{$token}">
	{/if}
	<ul class="taglist">
	{foreach $tags AS $tag_id => $tag}
	<li>
		{if isset($user_can_edit_tag[$tag_id]) && $user_can_edit_tag[$tag_id] }
			<span id="tagdelete_{$tag_id}" class="delete"> 
			<a href="adm/user_tags.php?scenario={$id}&tag_id={$tag_id}&action=delete&token={$token}" title="{$_sce_removetag|escape}">[{$_remove|ucfirst|escape}]</a></span>
		{/if}
	<a href="data?tag={$tag|rawurlencode}" rel="tag" class="tag">{$tag|escape}</a>
	</li>
	{/foreach}
	{* This part is really only for users logged in *}
	{if isset($user_id)}
	<li><a href="#" onclick="$('#tag_input_li').toggle(100).focus();$('#tag_input').focus();" title="{$_sce_addtag}" accesskey="t" class="tag">+</a></li>
	<li style="display: none;" id="tag_input_li"><input type="hidden" name="scenario" value="{$id}"><input type="hidden" name="action" value="add"><input type="text" name="tag" id="tag_input" class="newtag" placeholder="E.g. Grind Night"></li>
	{/if}
	</ul>	
	{if isset($user_id)}
	</form>
	{/if}
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
		{if $user_can_edit_participants || isset($user_admin) || isset($user_editor)}
		- <a href="#" onclick="$('#form_participants').toggle(); return false;">{$_sce_editplayerno}</a>
		{/if}
	{/if}
	{if isset($user_id) && $participants == ""}
		{$_participants|ucfirst}: {$_unknown|ucfirst}. <a href="#" onclick="document.getElementById('form_participants').style.display='block'; return false;">{$_sce_addplayerno}</a>
	{/if}
	</p>
	{if isset($user_id) }
		<div id="form_participants" style="display: none">
		<form action="adm/user_participants.php" method="post">
		<input type="hidden" name="token" value="{$token}">
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


{if $person_extra != ""}
	<h3 class="parttitle">
		{$_sce_organizedby}
	</h3>
	<p class="indata">
		{$person_extra}
	</p>
{/if}

{if $personlist != ""}
	<h3 class="parttitle">
		{$_sce_by}
	</h3>
		{$personlist}
{/if}

{include file="filelist.tpl"}

{if count($descriptions) gt 1}
<div class="clear">{* Make tab menu clear of picture *}
{/if}

{if $descriptions}
<h3 class="parttitle">
	{$_sce_description}
</h3>
{if count($descriptions) gt 1}
<div id="tabs" style="margin-top: 0">
<ul>
{foreach $descriptions AS $d_id => $d}
<li><a href="#description-{$d_id}" {if isset($d.langname)}title="{$d.langname|escape}"{/if}>{$d.language|escape}{if $d.note} ({$d.note}){/if}</a></li>
{/foreach}
</ul>

{foreach $descriptions AS $d_id => $d}
	<div id="description-{$d_id}">
	<p class="indata"{if isset($d.langcode)} lang="{$d.langcode|escape}"{/if}>
		{$d.description|escape|textlinks|nl2br}
	</p>
	</div>
{/foreach}
</div>
{else}
	<p class="indata"{if isset($descriptions[0].langcode)} lang="{$descriptions[0].langcode|escape}"{/if}>
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

{if $awards}
<h3 id="awards">{$_con_awards|ucfirst}</h3>
{foreach $awards AS $award}
<h4 class="awardconventhead"><a href="{$award.type_award_url}" class="con" title="{$_allawardsfor|sprintf:$award.type_name|escape}">{$award.type_name|escape}</a></h4>
<div>
{$award.awards}
</div>
{/foreach}
{/if}

{if $articlesfrom}
<h3 class="parttitle">{$_p_articles}</h3>
	<table id="gamearticles">
	{foreach $articlesfrom as $article}
	<tr>
	<td>{$article.title|escape}</td>
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
