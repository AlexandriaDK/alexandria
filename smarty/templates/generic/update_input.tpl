{assign var="pagetitle" value="{$_update_title}"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			{$_update_title}
		</h2>

		<p>
			{$_update_intro|nl2br}
		</p>

{if $category == 'convent' && $label != ""}
		<p class="addorganizersyourself">
			{$_update_add|sprintf:"data?con=$data_id&amp;edit=organizer#organizers"}
		</p>
{/if}

		<form action="rettelser_indsend" method="post">
			<table>

{if $label}
			<tr><td>
			<input type="hidden" name="cat" value="{$category|escape}" >
			<input type="hidden" name="data_id" value="{$data_id|escape}" >
			{$_update_for}</td>
			<td class="correctionlabel">{$label}</td></tr>
{else}
			<tr><td>{$_update_for2}</td><td><input type="text" name="data_label" size="30" maxlength="250"><br><span class="noteindtast">{$_update_for2help}</span></td></tr>
{/if}
			<tr><td>{$_update_correction}</td><td><textarea name="data_description" cols="30" rows="8"></textarea></td></tr>
			<tr><td>{$_update_name}</td><td><input type="text" name="user_name" size="30" value="{$user_name|escape}"></td></tr>
			<tr><td>{$_update_email}</td><td><input type="email" name="user_email" size="30"><br><span class="noteindtast">{$_update_emailhelp}</span></td></tr>
			<tr><td>{$_update_source}</td><td><textarea name="user_source" cols="30" rows="3"></textarea><br><span class="noteindtast">{$_update_sourcehelp}</span></td></tr>
			<tr><td>{$_update_spamcheck|nl2br}</td><td><input type="text" name="human" value="" size="3"></td></tr>

			<tr><td></td><td><input type="submit" value="{$_update_submitbutton}"></td></tr>
			</table>
		</form>

		<p>
			{$_update_larger}
		</p>	


</div>

{include file="end.tpl"}
