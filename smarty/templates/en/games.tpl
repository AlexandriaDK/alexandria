{if ! $boardgamesonly}
{assign var="pagetitle" value="Scenario overview - $titlepart"}
{else}
{assign var="pagetitle" value="Board game overview"}
{/if}
{include file="head.tpl"}

<div id="contentwide">

{if ! $boardgamesonly}
	<h2 class="pagetitle" style="margin-bottom: 1em;">
		Scenarios:
	</h2>

	<p style="font-size: 14px;">
		Choose first letter:<br>
		{$keys}
		<br><br>
		Or genre:<br>
		{$genre}
	</p>
{else}
	<h2 class="pagetitle" style="margin-bottom: 1em;">
		Board games:
	</h2>
{/if}
	<table>
		<tr>
{if $user_id}
			<th colspan="4">
{/if}
			<th></th>
			<th class="listhead" >Title:</th>
			<th class="listhead" >By:</th>
			<th class="listhead" >Presented at:</th>
		</tr>
		{$scenlist}
	</table>

</div>

{include file="end.tpl"}
