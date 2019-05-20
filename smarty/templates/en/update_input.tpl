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
			
			{$content}

				<tr><td></td><td><input type="submit" value="Send your correction" /></td></tr>
			</table>
		</form>

		<p>
			If you need to attach files or submit larger amounts of information you can email us with the
			relevant information at the address <a href="mailto:kontakt@alexandria.dk">kontakt@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
