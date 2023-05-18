{assign var="pagetitle" value="{$_usb_title}"}
{include file="head.tpl"}

<div id="content">
		<h2 class="pagetitle">
			{$_usb_title}
		</h2>

		<p>
			{$_usb_description|nl2br}
		</p>

</div>

{include file="end.tpl"}
