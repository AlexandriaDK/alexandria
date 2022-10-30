{assign var="pagetitle" value="{$_about_title}"}
{include file="head.tpl"}

<div id="content">
		<h2 class="pagetitle">
			{$_about_welcome}
		</h2>

		<p>
			{$_about_content|sprintf:'rettelser':'https://www.retsinformation.dk/Forms/R0710.aspx?id=164796#P71':'data?person=1':'https://loot.alexandria.dk/AlexandriaOffline.zip':'export':'https://gitlab.com/brodersen/alexandria':'kontakt':'privacy'|nl2br}
		</p>

		<h3>
			{$_about_history_title}
		</h3>

		<p>
			{$_about_history|sprintf:'https://loot.alexandria.dk/varefakta.php':'data?scenarie=525':'data?person=1':'data?con=198#awards':'data?con=497#awards':'https://www.facebook.com/groups/FriendsOfAlexandria':'https://gitlab.com/brodersen/alexandria':'export':'https://loot.alexandria.dk/AlexandriaOffline.zipxxx'|nl2br}
		</p>
</div>

{include file="end.tpl"}
