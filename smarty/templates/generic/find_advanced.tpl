{assign var="pagetitle" value="{$_find_searchforgame}"}
{include file="head.tpl"}

<div id="contenttext">

	<h2 class="pagetitle">
		{$_find_searchforgame}
	</h2>

		<form action="find">
			<table>
				<tr>
					<td><label for="search_title">{$_title|ucfirst}</label></td>
					<td><input type="text" name="search_title" id="search_title" size="30" /></td>
				</tr>
				<tr>
					<td><label for="search_description">{$_find_description}</label></td>
					<td><input type="text" name="search_description" id="search_description" size="30" /></td>
				</tr>
				<tr>
					<td>{$_rpgsystem|ucfirst}</td>
					<td><select name="search_system">
					<option value="">{$_find_notimportant}</option>
{foreach from=$systems item=$system}
						<option value="{$system.id}">{$system.name}</option>
{/foreach}
						</select>
					</td>
				</tr>

				<tr>
					<td>{$_find_conplay}</td>
					<td><select name="search_conset">
					<option value="">{$_find_notimportant}</option>
{foreach from=$consets item=$conset}
						<option value="{$conset.id}">{$conset.name}</option>
{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{$_genre|ucfirst}<br><span class="findhint">{$_find_genre_part}</span></td>
					<td style="column-count: 2;">
{section name=g loop=$genres}
						<input type="checkbox" name="search_genre[]" value="{$genres[g].id}" id="search_genre_{$genres[g].id}" /><label for="search_genre_{$genres[g].id}">{$genres[g].name}</label>{if ! $smarty.section.g.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>{$_find_othercategory}</span></td>
					<td style="column-count: 2;">
{section name=c loop=$categories}
						<input type="checkbox" name="search_genre[]" value="{$categories[c].id}" id="search_genre_{$categories[c].id}" /><label for="search_genre_{$categories[c].id}">{$categories[c].name}</label>{if ! $smarty.section.c.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>{$_find_playerno}</td>
					<td><input type="number" name="search_players" size="3" min="0" style="width: 50px;" max="10000" /></td>
				</tr>

				<tr>
					<td><label for="search_no_gm">{$_find_nogm}</label></td>
					<td><input type="checkbox" name="search_no_gm" id="search_no_gm" /></td>
				</tr>

				<tr>
					<td><label for="search_boardgames">{$_find_boardgamesonly}</label></td>
					<td><input type="checkbox" name="search_boardgames" id="search_boardgames" /></td>
				</tr>

				<tr>
					<td><label for="search_download">{$_find_downloadableonly}</label></td>
					<td><input type="checkbox" name="search_download" id="search_download" /></td>
				</tr>

				<tr>
					<td></td>
					<td><input type="hidden" name="search_type" value="findspec"><input type="submit" value="{$_search|escape}" /></td>
				</tr>
				
			</table>
		</form>

		{$_find_advancedhints}

</div>

{include file="end.tpl"}
