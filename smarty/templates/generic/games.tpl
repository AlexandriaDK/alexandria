{if ! isset($boardgamesonly) }
{if $beginchar eq "1"}
{assign var="pagetitle" value="$_games_title_sce - $_games_beginwithno"}
{else}
{assign var="pagetitle" value="$_games_title_sce - $_games_beginwithchar $beginchar"}
{/if}
{else}
{assign var="pagetitle" value="$_games_title_bg"}
{/if}
{include file="head.tpl"}

<div id="contentwide">

{if ! isset($boardgamesonly) }
	<h2 class="pagetitle gamesselect">
		{$_games_title_sce}
	</h2>

	<p class="gameslinks">
		{$_games_firstletter}<br>
		{$keys}
		<br><br>
		{$_games_genre}<br>
		{$genre}
	</p>
{else}
	<h2 class="pagetitle gamesselect">
		{$_games_title_bg}
	</h2>
{/if}
	<table>
		<thead>
		<tr>
{if $user_id}
			<th colspan="4"></th>
{/if}
			<th></th>
			<th class="listhead">{$_title|ucfirst}:</th>
			<th class="listhead">{$_by|ucfirst}:</th>
			<th class="listhead">{$_games_writtenfor|ucfirst}:</th>
		</tr>
		</thead>
		<tbody>
		{$scenlist}
		</tbody>
	</table>

</div>

{include file="end.tpl"}
