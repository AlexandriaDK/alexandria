{assign var="pagetitle" value="Rollespil - Scenarier"}
{include file="head.afd.tpl"}

<div id="contenttext">

	<div style="width: 190px; padding-right: 10px; border-right: 1px solid black; float: left;">
		<h3>
			Seneste nyt:
		</h3>

		<p style="font-size: 0.8em;">
			<i>Alexandria bliver løbende rettet og opdateret - kun større ændringer er nævnt her:</i>
		</p>

		{section name=i loop=$newslist}
		<p>
			<a id="{$newslist[i].anchor}">{$newslist[i].date}</a>:<br />
			{$newslist[i].news}
		</p>
		{/section}

	</div>
	
	<div style="float: left; width: 200px; padding-left: 10px;">
		<h3>
			Kommende kongresser:
		</h3>
		<table cellspacing="1">
			{$html_nextcons}
		</table>
		
		<h3>
			Alexandria i tal:
		</h3>
		<table cellspacing="1" cellpadding="0">
			<tr><td>Antal scenarier:</td><td align="right">&nbsp;{$stat_all_sce}</td></tr>
			<tr><td>Antal personer:</td><td align="right">&nbsp;{$stat_all_aut}</td></tr>
			<tr><td>Antal systemer:</td><td align="right">&nbsp;{$stat_all_sys}</td></tr>
			<tr><td>Antal kongresser:</td><td align="right">&nbsp;{$stat_all_convent}</td></tr>
			<tr><td>Scenarier til download:</td><td align="right">&nbsp;{$scenarios_downloadable}</td></tr>
			<tr><td colspan="2"><a href="statistik">Flere tal</a></td></tr>
		</table>				
		
		<h3>
			Seneste scenarier til download:
		</h3>
		<ul>
		{section name=i loop=$latest_downloads}
			<li><a href="data?scenarie={$latest_downloads[i].id}" class="scenarie">{$latest_downloads[i].title|escape}</a></li>
		{/section}
		</ul>
			
	</div>
	

</div>

{include file="end.tpl"}
