{assign var="pagetitle" value="My settings"}
{include file="head.tpl"}

<div id="contentwide">

		<h2 class="pagetitle">
			My settings:
		</h2>

		<p>
			... work in progress. At the momeny only editors can see the menu option and this page.
		</p>

		<h3>
			User data:
		</h3>
		<p>
		Name: {$user_name|escape}
		<br>
		Connected to a person in Alexandria's library:
{if $useraut}
		Yes - <a href="data?person={$useraut['aut_id']}" class="person">{$useraut['firstname']|escape} {$useraut['surname']|escape}</a>
{else}
		No
{/if}
		<br>
{if $user_editor}
		You are an editor at Alexandria (and thereby an all-round cool person). Check out <a href="https://www.facebook.com/groups/1602088646679278/">our Facebook group</a>.
{/if}
		</p>
		
		<h3>
			Connected accounts:
		</h3>

		<p>
			Facebook: {if $userloginmap['facebook']}Yes{else}No{/if}<br>
			Twitter: {if $userloginmap['twitter']}Yes{else}No{/if}<br>
			Steam: {if $userloginmap['steam']}Yes{else}No{/if}<br>
			Twitch: {if $userloginmap['twitch']}Yes{else}No{/if}<br>
			Foobar: {if $userloginmap['foobar']}Yes{else}No{/if}<br>

		</p>

</div>

{include file="end.tpl"}
