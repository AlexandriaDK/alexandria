{if ! $csname}
{assign var="pagetitle" value="$_award_title"}
{else}
{assign var="pagetitle" value="$_award_title - $csname"}
{/if}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_award_awards}
	</h2>

{if ! $cid}
	<p>
		{$_award_selectconset}:
	</p>
{else}
	<p>
		{$_award_overview1} <a href="data?conset={$cid}" class="con">{$csname|escape}</a>. {$_award_overview2}
	</p>
{/if}

<div class="allawards">
{$html_content}
</div>
		
{include file="updatelink.tpl"}

</div>

{include file="end.tpl"}
