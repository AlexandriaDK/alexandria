{assign var="pagetitle" value="Søg efter scenarie"}
{include file="head.tpl"}

<div id="contenttext">

	<h2 class="pagetitle">
		Søg efter scenarie:
	</h2>

		<p>
			Udfyld de felter, du vil medtage i din søgning. Søgningen tager højde for
			simple taste- og stavefejl i titlen.
		</p>
		
		<form action="find">
			<table>
				<tr>
					<td><label for="search_title">Titel</label></td>
					<td><input type="text" name="search_title" id="search_title" size="30" /></td>
				</tr>
				<tr>
					<td><label for="search_description">Del af foromtalen</label></td>
					<td><input type="text" name="search_description" id="search_description" size="30" /></td>
				</tr>
				<tr>
					<td>System</td>
					<td><select name="search_system">
					<option value="">Ikke vigtigt</option>
{section name=s loop=$systems}
						<option value="{$systems[s].id}">{$systems[s].name}</option>
{/section}
						</select>
					</td>
				</tr>

				<tr>
					<td>Kongres, scenariet har været spillet på</td>
					<td><select name="search_conset">
					<option value="">Ikke vigtigt</option>
{section name=set loop=$consets}
						<option value="{$consets[set].id}">{$consets[set].name}</option>
{/section}
						</select>
					</td>
				</tr>
				<tr>
					<td>Genre<br><span style="font-size: 0.8em;">(bemærk, at kun en del af scenarierne har genrer angivet)</span></td>
					<td style="column-count: 2;">
{section name=g loop=$genres}
						<input type="checkbox" name="search_genre[]" value="{$genres[g].id}" id="search_genre_{$genres[g].id}" /><label for="search_genre_{$genres[g].id}">{$genres[g].name}</label>{if ! $smarty.section.g.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>Øvrig kategori</span></td>
					<td style="column-count: 2;">
{section name=c loop=$categories}
						<input type="checkbox" name="search_genre[]" value="{$categories[c].id}" id="search_genre_{$categories[c].id}" /><label for="search_genre_{$categories[c].id}">{$categories[c].name}</label>{if ! $smarty.section.c.last}<br>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>Antal spillere</td>
					<td><input type="number" name="search_players" size="3" min="0" style="width: 50px;" max="10000" /></td>
				</tr>

				<tr>
					<td><label for="search_no_gm">Spillederløst scenarie</label></td>
					<td><input type="checkbox" name="search_no_gm" id="search_no_gm" /></td>
				</tr>

{*
				<tr>
					<td><label for="search_short">Kun novellescenarie</label></td>
					<td><input type="checkbox" name="search_genre[]" id="search_short" value="10" /></td>
				</tr>
*}

				<tr>
					<td><label for="search_boardgames">Kun brætspil</label></td>
					<td><input type="checkbox" name="search_boardgames" id="search_boardgames" /></td>
				</tr>

				<tr>
					<td><label for="search_download">Kun scenarier, som kan downloades</label></td>
					<td><input type="checkbox" name="search_download" id="search_download" /></td>
				</tr>

				<tr>
					<td></td>
					<td><input type="hidden" name="search_type" value="findspec"><input type="submit" value="Søg" /></td>
				</tr>
				
			</table>
		</form>

		<h3>
			Genvejstast til søgefeltet
		</h3>
		
		<p>
			Der er et søgefelt øverst til højre på samtlige sider. Ved at trykke på Genvejstast-S
			for "søg" (typisk Alt-Shift-S under Windows), kan man gå ind i feltet, i stedet for at benytte musen.
		</p>

		<h3>
			Søgeresultat-i-første-hug
		</h3>
		
		<p>
			Jævnlige brugere af Alexandria vil sætte pris på en yderligere søgemulighed. Man kan søge
			efter indhold i Alexandria, allerede mens man taster adressen ind. Man skal ganske enkelt
			blot indtaste sit søgeord umiddelbart efter <span class="urltext">{$servername}/</span> i adresselinjen, fx:
		</p>

		<p>
			<a href="//{$servername}/Paladins%20Lampe"><span class="urltext">{$servername}/Paladins Lampe</span></a>
		</p>

		<p>
			Alexandria vil i dette eksempel søge efter "Paladins Lampe", og efterfølgende føre dig
			til resultatsiden.
		</p>

</div>

{include file="end.tpl"}
