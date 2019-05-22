{assign var="pagetitle" value="Submit corrections"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			Submit corrections:
		</h2>

		<p>
			We are pleased to accept additions and corrections
			from you. A bunch of our information is based on
			old programs meaning it is not certain that these
			reflect the final conditions.
		</p>
		<p>
			If you <a href="fblogin">log in</a> you will
			be able to add organizers for conventions at the
			bottom of the convention pages.
		</p>

{if $category == 'convent' && $label != ""}
		<p class="addorganizersyourself">
			Please notice: You can <a href="data?con={$data_id}&amp;edit=organizers#organizers">add organizers yourself</a>
			for a convention instead of submitting an update for review. This is easier and faster for everybody.
		</p>
{/if}

		<form action="rettelser_indsend" method="post">
			<table>
{if $label}
			<tr><td>
			<input type="hidden" name="cat" value="{$category|escape}" >
			<input type="hidden" name="data_id" value="{$data_id|escape}" >
			Submit correction for:</td>
			<td class="correctionlabel">{$label}</td></tr>
{else}
			<tr><td>Submit correction for:</td><td><input type="text" name="data_label" size="30" maxlength="250"><br><span class="noteindtast">E.g. "We were WASP", "Fastaval 2002"...</span></td></tr>

{/if}
			<tr><td>Type in your correction:</td><td><textarea name="data_description" cols="30" rows="8"></textarea></td></tr>
			<tr><td>Your name:</td><td><input type="text" name="user_name" size="30" value="{$user_name|escape}"></td></tr>
			<tr><td>Your email:</td><td><input type="email" name="user_email" size="30"><br><span class="noteindtast">We will only contact you if we have further questions</span></td></tr>
			<tr><td>What is your source?</td><td><textarea name="user_source" cols="30" rows="3"></textarea><br><span class="noteindtast">E.g. a URL, a convention flyer, "it's me", "my memory"...</span></td></tr>
			<tr><td>Enter the letter <b>A</b>:<br>(to prevent spam)</td><td><input type="text" name="human" value="" size="3"></td></tr>

			<tr><td></td><td><input type="submit" value="Send your correction"></td></tr>
			</table>
		</form>

		<p>
			If you need to attach files or submit larger amounts of information you can email us with the
			relevant information at the address <a href="mailto:kontakt@alexandria.dk">kontakt@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
