{assign var="pagetitle" value="Feeds"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Feeds:
	</h2>

		<p>
			Feeds fra diverse danske rollespilsblogs - opdateres en gang i timen.<br />
			Det er også muligt at hente et <a href="feedrss.php">meta-feed for alle blogs</a> (RSS) til sin egen feed-læser.
		</p>
	
		<table>
			<tr><th>Titel</th><th>Af</th><th>Postet</th></tr>
			{section name=i loop=$feeddata}
			<tr>
			<td title="{$feeddata[i].title|escape}">
				<a href="{$feeddata[i].link|escape}">{$feeddata[i].title|truncate:45|escape}</a>
			</td>
			<td>
				{if $feeddata[i].aut_id}
					<a href="data?person={$feeddata[i].aut_id}" class="person">{$feeddata[i].owner|escape}</a>
				{else}
					{$feeddata[i].owner|escape}
				{/if}
			</td>
			<td style="text-align: right;">
				{$feeddata[i].printdate}
			</td>
			</tr>
			{/section}
		</table>

		<h3>
			Der hentes nyheder fra følgende sider:
		</h3>
		
		<p>
			{section name=i loop=$feedlist}
			<a href="{$feedlist[i].pageurl|escape}">{$feedlist[i].owner|escape}: {$feedlist[i].name|escape}</a><br />
			{/section}
		</p>

		<h3>
			Har du en rollespils-blog?
		</h3>
		
		<p>
			Du kan få tilføjet nye indlæg fra din blog ved at <a href="kontakt">kontakte Alexandria</a>
			og sende et link og en beskrivelse. Din side skal tilbyde et feed i RSS- eller Atom-formatet, ellers kan Alexandria ikke
			hente indholdet.
		</p>
		
		<p>
			Ydermere skal indholdet være begrænset til rollespils-indlæg; har du andet
			indhold på din blog, bør du benytte dig af tags, kategorier eller andre opdelinger,
			så det er muligt kun at hente de rollespils-relaterede indlæg.
		</p>


</div>

{include file="end.tpl"}
