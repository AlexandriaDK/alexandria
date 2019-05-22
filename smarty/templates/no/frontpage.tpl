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

		{section name=i loop=$newslist}
		<p>
			<a id="{$newslist[i].anchor}">{$newslist[i].date}</a>:<br>
			{$newslist[i].news}
		</p>
		{/section}
		<h3>
			{$_fp_alexnumbers}
		</h3>
		<table class="tableoverview">
			<tr><td>{$_scenarios|@ucfirst}:</td><td class="statnumber">{$stat_all_sce}</td></tr>
			<tr><td>{$_boardgames|@ucfirst}:</td><td class="statnumber">{$stat_all_board}</td></tr>
			<tr><td>{$_persons|@ucfirst}:</td><td class="statnumber">{$stat_all_aut}</td></tr>
			<tr><td>{$_conventions|@ucfirst}:</td><td class="statnumber">{$stat_all_convent}</td></tr>
			<tr><td>{$_rpgsystems|@ucfirst}:</td><td class="statnumber">{$stat_all_sys}</td></tr>
			<tr><td>{$_fp_scefordownload}:</td><td class="statnumber">{$scenarios_downloadable}</td></tr>
			<tr><td colspan="2"><a href="statistik">{$_fp_morenumbers}</a></td></tr>
		</table>				

	</div>
	
	<div class="frontpagedownloads">
		<h3>
			{$_fp_recentdownload}
		</h3>
		<ul>
		{section name=i loop=$latest_downloads}
			<li><a href="data?scenarie={$latest_downloads[i].id}" class="scenarie">{$latest_downloads[i].title|escape}</a></li>
		{/section}
		</ul>
			
		<h3 style="margin-bottom: 0;">
			{$_fp_upcomingevents}
		</h3>
		<p style="margin-top: 2px;">
			{$_fp_bgevents}
		</p>
			{$html_nextevents}
		
		
	</div>
	

</div>

{include file="end.tpl"}
