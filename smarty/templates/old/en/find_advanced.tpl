{assign var="pagetitle" value="Game search"}
{include file="head.tpl"}

<div id="contenttext">

	<h2 class="pagetitle">
		Game search:
	</h2>

		<form action="find">
			<table>
				<tr>
					<td><label for="search_title">Title</label></td>
					<td><input type="text" name="search_title" id="search_title" size="30" /></td>
				</tr>
				<tr>
					<td><label for="search_description">Part of description</label></td>
					<td><input type="text" name="search_description" id="search_description" size="30" /></td>
				</tr>
				<tr>
					<td>RPG system</td>
					<td><select name="search_system">
					<option value="">Not important</option>
{section name=s loop=$systems}
						<option value="{$systems[s].id}">{$systems[s].name}</option>
{/section}
						</select>
					</td>
				</tr>

				<tr>
					<td>Convention the game has been presented at</td>
					<td><select name="search_conset">
					<option value="">Not important</option>
{section name=set loop=$consets}
						<option value="{$consets[set].id}">{$consets[set].name}</option>
{/section}
						</select>
					</td>
				</tr>
				<tr>
					<td>Genre<br><span style="font-size: 0.8em;">(notice that not every game has genres added)</span></td>
					<td style="column-count: 2;">
{section name=g loop=$genres}
						<input type="checkbox" name="search_genre[]" value="{$genres[g].id}" id="search_genre_{$genres[g].id}" /><label for="search_genre_{$genres[g].id}">{$genres[g].name}</label>{if ! $smarty.section.g.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>Other categories</span></td>
					<td style="column-count: 2;">
{section name=c loop=$categories}
						<input type="checkbox" name="search_genre[]" value="{$categories[c].id}" id="search_genre_{$categories[c].id}" /><label for="search_genre_{$categories[c].id}">{$categories[c].name}</label>{if ! $smarty.section.c.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>Number of players</td>
					<td><input type="number" name="search_players" size="3" min="0" style="width: 50px;" max="10000" /></td>
				</tr>

				<tr>
					<td><label for="search_no_gm">Scenario without GM</label></td>
					<td><input type="checkbox" name="search_no_gm" id="search_no_gm" /></td>
				</tr>

{*
				<tr>
					<td><label for="search_short">Kun novellescenarie</label></td>
					<td><input type="checkbox" name="search_genre[]" id="search_short" value="10" /></td>
				</tr>
*}

				<tr>
					<td><label for="search_boardgames">Board games only</label></td>
					<td><input type="checkbox" name="search_boardgames" id="search_boardgames" /></td>
				</tr>

				<tr>
					<td><label for="search_download">Only downloadable scenarios</label></td>
					<td><input type="checkbox" name="search_download" id="search_download" /></td>
				</tr>

				<tr>
					<td></td>
					<td><input type="hidden" name="search_type" value="findspec"><input type="submit" value="Search" /></td>
				</tr>
				
			</table>
		</form>

		<h3>
			Hotkey for search field
		</h3>
		
		<p>
			There is a search field present at all pages. By pressing Hotkey+S for "search" (usually
			Alt+Shift+S when using Windows) you can enter this field instead of using the mouse.
		</p>

		<h3>
			I'm feeling lucky
		</h3>
		
		<p>
			You can search for a game directly when typing in the URL. Simply add
			your search phrase after <span class="urltext">{$servername}/</span> in the address field, e.g.
		</p>

		<p>
			<a href="//{$servername}/We%20were%20WASP"><span class="urltext">{$servername}/We were WASP</span></a>
		</p>

		<p>
			In this case Alexandria will search for "We were WASP" and redirect you to the matching page.
		</p>

</div>

{include file="end.tpl"}
