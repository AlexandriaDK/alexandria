{assign var="pagetitle" value="Søg efter scenarie"}
{include file="head.tpl"}

<div id="contenttext">

	<h2 class="pagetitle">
		Søg efter scenarie:
	</h2>

{*
		<form action="find">
			<table cellspacing="0" cellpadding="4" style="margin-left: 20px;">
				<tr valign="middle">
					<td>
						Indtast din søgning:
					</td>
					<td ><input type="text" name="find" size="25" class="find" /></td>
				</tr>
				<tr>
					<td>
					Søg indenfor:
					</td>
					<td>
						<select name="cat">
							<option value="">alle emner</option>
							<option value="aut" style="color: red;">personer</option>
							<option value="sce" style="color: #c60;">scenarier</option>
							<option value="con" style="color: green;">kongresser</option>
							<option value="sys" style="color: blue;">systemer</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="submit" value="Søg" /></td>
				</tr>
				<tr>
					<td colspan="2">Søgningen tager højde for simple taste- og stavefejl</td>
				</tr>

			</table>
		</form>

		<h3>
			Søg efter et scenarie:
		</h3>

*}		
		<p>
			Udfyld de felter, du vil medtage i din søgning. Søgningen tager højde for
			simple taste- og stavefejl i titlen.
		</p>
		
		<form action="find">
			<table>
				<tr>
					<td>Indtast scenariets titel:</td>
					<td colspan="2"><input type="text" name="search_title" size="30" /></td>
				</tr>
				<tr>
					<td>Indtast en del af foromtalen:</td>
					<td colspan="2"><input type="text" name="search_description" size="30" /></td>
				</tr>
				<tr valign="top">
					<td>Vælg scenariets system:</td>
					<td colspan="2"><select name="search_system">
					<option value="">Ikke vigtigt</option>
{section name=s loop=$systems}
						<option value="{$systems[s].id}">{$systems[s].name}</option>
{/section}
						</select>
					</td>
				</tr>

				<tr valign="top">
					<td>Vælg kongres, scenariet har været spillet på:</td>
					<td colspan="2"><select name="search_conset">
					<option value="">Ikke vigtigt</option>
{section name=set loop=$consets}
						<option value="{$consets[set].id}">{$consets[set].name}</option>
{/section}
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td>Vælg scenariets genre:<br>(bemærk, at kun en del af scenarierne har genrer angivet)</td>
					<td>
{section name=g loop=$genres}
						<input type="checkbox" name="search_genre[]" value="{$genres[g].id}" id="search_genre_{$genres[g].id}" /><label for="search_genre_{$genres[g].id}">{$genres[g].name}</label>{if ! $smarty.section.g.last}<br />{/if}
						{if ($smarty.section.g.iteration) == ($smarty.section.g.total/2|ceil) }</td><td>{/if}

{/section}
					</td>
				</tr>

				<tr>
					<td>Kun scenarier, som kan downloades:</td>
					<td colspan="2"><input type="checkbox" name="search_download" /></td>
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
			for "søg" (typisk Alt-S under Windows), kan man gå ind i feltet, i stedet for at benytte musen.
		</p>

		<h3>
			Søgeresultat-i-første-hug
		</h3>
		
		<p>
			Jævnlige brugere af Alexandria vil sætte pris på en yderligere søgemulighed. Man kan søge
			efter indhold i Alexandria, allerede mens man taster adressen ind. Man skal ganske enkelt
			blot indtaste sit søgeord umiddelbart efter <tt><?php print $_SERVER['SERVER_NAME']; ?>/</tt> i adresselinjen, fx:
		</p>

		<p>
			<a href="http://{$servername}/Paladins%20Lampe"><tt>{$servername}/Paladins Lampe</tt></a>
		</p>

		<p>
			Alexandria vil i dette eksempel søge efter "Paladins Lampe", og efterfølgende føre dig
			til resultatsiden.
		</p>

		<h3>
			Firefox-brugere
		</h3>

		<p>
			Brugere af Firefox kan <a href="javascript:window.sidebar.addSearchEngine('http://alexandria.dk/plugin/alexandriadk.src','http://alexandria.dk/plugin/alexandriadk.png', 'Alexandria', 'Games');">tilføje Alexandria som Search Engine</a>
			og dermed søge direkte i browseren.
		</p>
		
		<p>
			Eksempel:
		</p>
		
		<p>
			<a href="javascript:window.sidebar.addSearchEngine('http://alexandria.dk/plugin/alexandriadk.src','http://alexandria.dk/plugin/alexandriadk.png', 'Alexandria', 'Games');"><img src="gfx/firefox_search.png" alt="Firefox search" width="250" height="124" style="border: 1px solid black;" /></a>
		</p>
	</td>

{include file="end.tpl"}
