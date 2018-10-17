{assign var="pagetitle" value="S�g efter scenarie"}
{include file="head.tpl"}

<div id="contenttext">

	<h2 class="pagetitle">
		S�g efter scenarie:
	</h2>

{*
		<form action="find">
			<table cellspacing="0" cellpadding="4" style="margin-left: 20px;">
				<tr valign="middle">
					<td>
						Indtast din s�gning:
					</td>
					<td ><input type="text" name="find" size="25" class="find" /></td>
				</tr>
				<tr>
					<td>
					S�g indenfor:
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
					<td><input type="submit" value="S�g" /></td>
				</tr>
				<tr>
					<td colspan="2">S�gningen tager h�jde for simple taste- og stavefejl</td>
				</tr>

			</table>
		</form>

		<h3>
			S�g efter et scenarie:
		</h3>

*}		
		<p>
			Udfyld de felter, du vil medtage i din s�gning. S�gningen tager h�jde for
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
					<td>V�lg scenariets system:</td>
					<td colspan="2"><select name="search_system">
					<option value="">Ikke vigtigt</option>
{section name=s loop=$systems}
						<option value="{$systems[s].id}">{$systems[s].name}</option>
{/section}
						</select>
					</td>
				</tr>

				<tr valign="top">
					<td>V�lg kongres, scenariet har v�ret spillet p�:</td>
					<td colspan="2"><select name="search_conset">
					<option value="">Ikke vigtigt</option>
{section name=set loop=$consets}
						<option value="{$consets[set].id}">{$consets[set].name}</option>
{/section}
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td>V�lg scenariets genre:<br>(bem�rk, at kun en del af scenarierne har genrer angivet)</td>
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
					<td><input type="hidden" name="search_type" value="findspec"><input type="submit" value="S�g" /></td>
				</tr>
				
			</table>
		</form>

		<h3>
			Genvejstast til s�gefeltet
		</h3>
		
		<p>
			Der er et s�gefelt �verst til h�jre p� samtlige sider. Ved at trykke p� Genvejstast-S
			for "s�g" (typisk Alt-S under Windows), kan man g� ind i feltet, i stedet for at benytte musen.
		</p>

		<h3>
			S�geresultat-i-f�rste-hug
		</h3>
		
		<p>
			J�vnlige brugere af Alexandria vil s�tte pris p� en yderligere s�gemulighed. Man kan s�ge
			efter indhold i Alexandria, allerede mens man taster adressen ind. Man skal ganske enkelt
			blot indtaste sit s�geord umiddelbart efter <tt><?php print $_SERVER['SERVER_NAME']; ?>/</tt> i adresselinjen, fx:
		</p>

		<p>
			<a href="http://{$servername}/Paladins%20Lampe"><tt>{$servername}/Paladins Lampe</tt></a>
		</p>

		<p>
			Alexandria vil i dette eksempel s�ge efter "Paladins Lampe", og efterf�lgende f�re dig
			til resultatsiden.
		</p>

		<h3>
			Firefox-brugere
		</h3>

		<p>
			Brugere af Firefox kan <a href="javascript:window.sidebar.addSearchEngine('http://alexandria.dk/plugin/alexandriadk.src','http://alexandria.dk/plugin/alexandriadk.png', 'Alexandria', 'Games');">tilf�je Alexandria som Search Engine</a>
			og dermed s�ge direkte i browseren.
		</p>
		
		<p>
			Eksempel:
		</p>
		
		<p>
			<a href="javascript:window.sidebar.addSearchEngine('http://alexandria.dk/plugin/alexandriadk.src','http://alexandria.dk/plugin/alexandriadk.png', 'Alexandria', 'Games');"><img src="gfx/firefox_search.png" alt="Firefox search" width="250" height="124" style="border: 1px solid black;" /></a>
		</p>
	</td>

{include file="end.tpl"}
