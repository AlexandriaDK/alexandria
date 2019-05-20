{if ! $csname}
{assign var="pagetitle" value="Priser - vindere og nominerede"}
{else}
{assign var="pagetitle" value="Priser - vindere og nominerede - $csname"}
{/if}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Priser og præmier:
	</h2>

{if ! $cid}
	<p>
		Vælg en kongres-serie:
	</p>
{else}
	<p>
		Oversigt over vindere og nominerede for <a href="data?conset={$cid}" class="con">{$csname|escape}</a>. Vindere er angivet med <span style="text-decoration: underline;">understregning</span>.
	</p>
{/if}

<div class="allawards">
{$html_content}
</div>

{*
		<p>
			Test: <a href="#" onclick="var nom=document.getElementsByClassName('nominee'); for (var i=0; i < nom.length; i++) { nom[i].style.display = 'none'; } return false;">Vis kun vindere</a>
		</p>

		<p>
			jquery: <a href="#" onclick="$('div.nominee').hide(); return false;">Vis kun vindere</a> - 
			<a href="#" onclick="$('div.nominee').show(); return false;">Vis også nominerede</a> 
		</p>

		<p>
			Vis kun år 1998: <a href="#" onclick="var years=document.querySelectorAll(':not([data-year=\'1998\']).awardyear'); for (var i=0; i < years.length; i++) { years[i].style.display='none';} return false;">1998</a>
		</p>
*}
		
{include file="updatelink.tpl"}

</div>

{include file="end.tpl"}
