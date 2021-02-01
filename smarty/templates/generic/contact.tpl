{assign var="pagetitle" value="{$_contact_title}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_contact_us}
	</h2>

		<p>
			{$_contact_corrections|sprintf:'rettelser':'mailto:peter@ter.dk':'peter@ter.dk'|textlinks|nl2br}
		</p>
	
</div>

{include file="end.tpl"}
