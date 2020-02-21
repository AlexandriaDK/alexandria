{assign var="pagetitle" value="{$_user_profile_head}"}
{include file="head.tpl"}

<div id="content">

		<h2 class="pagetitle">
			{$_user_profile_head}
		</h2>

		<p>
			{$_user_overview}
		</p>

		<h3>
			{$_user_information}
		</h3>

		<p>
		{$_name|ucfirst}: {$user_name|escape}
		</p>

		<p>
		{$_user_in_alexandria}: 
{if $useraut}
		{$_yes|ucfirst} - <a href="/data?person={$useraut['aut_id']}" class="person">{$useraut['firstname']|escape} {$useraut['surname']|escape}</a>
{else}
		{$_no|ucfirst}
{/if}
		</p>

		<p>
{if $user_editor}
		{$_user_is_editor|sprintf:'https://www.facebook.com/groups/1602088646679278/'}
{/if}
		</p>
		
		<h3>
			{$_user_connected_accounts}
		</h3>

		<p>
			{$_user_connected_accounts_information|sprintf:'mailto:peter@alexandria.dk':'peter@aleandria.dk'|nl2br}
		</p>

		<div class="accounts">
			<div class="{if $userloginmap['facebook']}linked{else}unlinked{/if}">Facebook</div>
			<div class="{if $userloginmap['google']}linked{else}unlinked{/if}">Google</div>
			<div class="{if $userloginmap['twitter']}linked{else}unlinked{/if}">Twitter</div>
			<div class="{if $userloginmap['steam']}linked{else}unlinked{/if}">Steam</div>
			<div class="{if $userloginmap['twitch']}linked{else}unlinked{/if}">Twitch</div>
			<div class="{if $userloginmap['discord']}linked{else}unlinked{/if}">Discord</div>
			<div class="{if $userloginmap['foobar']}linked{else}unlinked{/if}">Foobar</div>

		</div>

</div>

{include file="end.tpl"}
