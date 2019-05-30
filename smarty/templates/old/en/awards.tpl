{if ! $csname}
{assign var="pagetitle" value="Awards - winners and nominated"}
{else}
{assign var="pagetitle" value="Awards - winners and nominated - $csname"}
{/if}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Awards and prizes:
	</h2>

{if ! $cid}
	<p>
		Choose a convention:
	</p>
{else}
	<p>
		Winners and nominees for <a href="data?conset={$cid}" class="con">{$csname|escape}</a>. Winners are marked with <span style="text-decoration: underline;">underline</span>.
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
