{assign var="pagetitle" value="Oversigt over scenarier"}
{include file="head.tpl"}

<div id="contentwide">

	<h2 class="pagetitle" style="margin-bottom: 1em;">
		Scenarie-oversigt:
	</h2>

	<p style="font-size: 14px;">
		Vælg begyndelsesbogstav:<br />
		{$keys}
		<br /><br />
		Eller genre:<br />
		{$genre}
	</p>

	<table cellspacing="1" cellpadding="2">
		<tr>
{if $user_id}
			<th colspan="4">
{/if}
			<th></th>
			<th class="listhead" >Titel:</th>
			<th class="listhead" >Af:</th>
			<th class="listhead" >Skrevet til:</th>
		</tr>
		{$scenlist}
	</table>

</div>

{include file="end.tpl"}
