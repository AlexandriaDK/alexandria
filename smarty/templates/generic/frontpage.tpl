{assign var="pagetitle" value="{$_fp_pagetitle}"}
{if $user_admin}
{include file="head.tpl"}
{else}
{include file="head.tpl"}
{/if}

<div id="contenttext">

	<div class="latestnews">
		<h3>
			{$_fp_latestnews}
		</h3>

		<p class="topnote">
			{$_fp_topnote}
		</p>

		{foreach from=$newslist item=$news}
		<p>
			<a id="{$news.anchor}">{$news.date}</a>:<br>
			{$news.news}
		</p>
		{/foreach}
		<h3>
			{$_fp_alexnumbers}
		</h3>
		<table class="tableoverview">
			<tr><td>{$_scenarios|@ucfirst}:</td><td class="statnumber">{$stat_all_sce|nicenumber}</td></tr>
			<tr><td>{$_boardgames|@ucfirst}:</td><td class="statnumber">{$stat_all_board|nicenumber}</td></tr>
			<tr><td>{$_persons|@ucfirst}:</td><td class="statnumber">{$stat_all_aut|nicenumber}</td></tr>
			<tr><td>{$_conventions|@ucfirst}:</td><td class="statnumber">{$stat_all_convent|nicenumber}</td></tr>
			<tr><td>{$_rpgsystems|@ucfirst}:</td><td class="statnumber">{$stat_all_sys|nicenumber}</td></tr>
			<tr><td>{$_fp_scefordownload}:</td><td class="statnumber">{$scenarios_downloadable|nicenumber}</td></tr>
			<tr><td colspan="2"><a href="statistik">{$_fp_morenumbers}</a></td></tr>
		</table>				

	</div>
	
	<div class="frontpagedownloads">
		<h3>
			{$_fp_recentdownload}
		</h3>
		<ul>
		{foreach from=$latest_downloads item=$scenario}
			<li><a href="data?scenarie={$scenario.id}" class="game" title="{$scenario.origtitle|escape}">{$scenario.title|escape}</a></li>
		{/foreach}
		</ul>
			
		<h3 style="margin-bottom: 0;">
			{$_fp_upcomingevents}
		</h3>
		<p style="margin-top: 2px;">
			{$_fp_bgevents|sprintf:'https://drive.google.com/open?id=1VTnF6jAhuhhMw43fJqzlaH1QFt8-7rzdLyP7muk1VVM&amp;fbclid=IwAR2z9SpFPQXfpmtvdgV52yq9fsSo1GlpTBwAiIr6wwZ-f4B0aFMMYQcgtdc'}
		</p>
			{$html_nextevents}
		
		
	</div>
	

</div>

{include file="end.tpl"}
