{assign var="pagetitle" value="Rollespil - Scenarier - Brætspil"}
{include file="head.tpl"}

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
			<a id="{$newslist[i].anchor}">{$newslist[i].date}</a>:<br>
			{$newslist[i].news}
		</p>
		{/section}

	</div>
	
	<div style="float: left; width: 200px; padding-left: 10px;">
		<h3>
			Kommende arrangementer:
		</h3>
		<div id="selectlist">
			<input type="checkbox" class="switchbox" id="listconvents" checked> <label class="checkgreen" for="listconvents">Kongresser</label>
			<input type="checkbox" class="switchbox" id="listscenarios" checked > <label class="checkorange" for="listscenarios">Scenarier</label>
		</div>
			{$html_nextevents}
		
<script>
[].forEach.call(document.querySelectorAll('input[type="checkbox"]'), function(checkbox) {
	checkbox.addEventListener('change', function(e) {
		if (this.id == 'listconvents') {
			other = document.getElementById('listscenarios');
		} else if (this.id == 'listscenarios') {
			other = document.getElementById('listconvents');
		}
		if (!this.checked && !other.checked) {
			other.checked = true;
		}

		if (this.checked && other.checked) {
			$('#eventsall').show();
			$('#eventsconvent').hide();
			$('#eventsscenario').hide();
		} else if (other.checked && other.id == 'listconvents') {
			$('#eventsall').hide();
			$('#eventsconvent').show();
			$('#eventsscenario').hide();
		} else if (other.checked && other.id == 'listscenarios') {
			$('#eventsall').hide();
			$('#eventsconvent').hide();
			$('#eventsscenario').show();
		}
		
	});
});
</script>

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
		
		<h3>
			Seneste scenarier til download:
		</h3>
		<ul>
		{section name=i loop=$latest_downloads}
			<li style="padding-bottom: 2px;"><a href="data?scenarie={$latest_downloads[i].id}" class="scenarie">{$latest_downloads[i].title|escape}</a></li>
		{/section}
		</ul>
			
	</div>
	

</div>

{include file="end.tpl"}
