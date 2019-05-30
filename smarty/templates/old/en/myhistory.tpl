{assign var="pagetitle" value="My page"}
{include file="head.tpl"}

<div id="contentwide">

		<h2 class="pagetitle">
			My page:
		</h2>
		
		{if $content_addentry}
			<h3>You can not add data at the moment...</h3>
		{/if}
		
		<div id="kongres" style="float: left; margin-right: 30px;" >
		{$content_myconvents}
		{if not $content_myconvents}
		<h3 class="parttitle">Conventions:</h3>
		<p>
			You haven't added any conventions to your list.
		</p>
		{/if}
		</div>
		
		<div id="scenarier" style="float: left; margin-right: 30px;" >
		{$content_myscenarios}
		{if not $content_myscenarios}
		<h3 class="parttitle">Scenarios:</h3>
		<p>
			You haven't added any scenarios to your list.
		</p>
		{/if}
		</div>		

		<div id="achievements" style="float: left; margin-right: 30px; ">
		{$content_personal_achievements}
		</div>

</div>

{include file="end.tpl"}
