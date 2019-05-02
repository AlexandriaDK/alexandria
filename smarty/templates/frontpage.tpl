{assign var="pagetitle" value="Rollespil - Scenarier - Brætspil"}
{if $user_admin}
{include file="head.tpl"}
{else}
{include file="head.tpl"}
{/if}

<div id="contenttext">

	<div class="latestnews">
		<h3>
			Seneste nyt:
		</h3>

		<p class="topnote">
			Alexandria bliver løbende rettet og opdateret - kun større ændringer er nævnt her:
		</p>

		{section name=i loop=$newslist}
		<p>
			<a id="{$newslist[i].anchor}">{$newslist[i].date}</a>:<br>
			{$newslist[i].news}
		</p>
		{/section}
		<h3>
			Alexandria i tal:
		</h3>
		<table class="tableoverview">
			<tr><td>Scenarier:</td><td class="statnumber">&nbsp;{$stat_all_sce}</td></tr>
			<tr><td>Personer:</td><td class="statnumber">&nbsp;{$stat_all_aut}</td></tr>
			<tr><td>Systemer:</td><td class="statnumber">&nbsp;{$stat_all_sys}</td></tr>
			<tr><td>Brætspil:</td><td class="statnumber">&nbsp;{$stat_all_board}</td></tr>
			<tr><td>Kongresser:</td><td class="statnumber">&nbsp;{$stat_all_convent}</td></tr>
			<tr><td>Scenarier til download:</td><td class="statnumber">&nbsp;{$scenarios_downloadable}</td></tr>
			<tr><td colspan="2"><a href="statistik">Flere tal</a></td></tr>
		</table>				

	</div>
	
	<div class="frontpagedownloads">
		<h3>
			Seneste scenarier til download:
		</h3>
		<ul>
		{section name=i loop=$latest_downloads}
			<li><a href="data?scenarie={$latest_downloads[i].id}" class="scenarie">{$latest_downloads[i].title|escape}</a></li>
		{/section}
		</ul>
			
		<h3 style="margin-bottom: 0;">
			Kommende arrangementer:
		</h3>
		<p style="margin-top: 2px;">
			(tjek <a href="https://drive.google.com/open?id=1VTnF6jAhuhhMw43fJqzlaH1QFt8-7rzdLyP7muk1VVM&amp;fbclid=IwAR2z9SpFPQXfpmtvdgV52yq9fsSo1GlpTBwAiIr6wwZ-f4B0aFMMYQcgtdc">denne oversigt</a> for brætspil)
		</p>
			{$html_nextevents}
		
		
	</div>
	

</div>

{include file="end.tpl"}
