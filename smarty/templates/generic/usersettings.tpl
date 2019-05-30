{assign var="pagetitle" value="Mine indstillinger"}
{include file="head.tpl"}

<div id="contentwide">

		<h2 class="pagetitle">
			Mine indstillinger:
		</h2>

		<p>
			... under programmering. I øjeblikket kan kun redaktører se menupunktet og denne side.
		</p>

		<h3>
			Brugeroplysninger:
		</h3>
		<p>
		Navn: {$user_name|escape}
		<br>
		I Alexandrias fortegnelser:
{if $useraut}
		Ja - <a href="/data?person={$useraut['aut_id']}" class="person">{$useraut['firstname']|escape} {$useraut['surname']|escape}</a>
{else}
		Nej
{/if}
		<br>
{if $user_editor}
		Du er redaktør på Alexandria (og dermed en allround cool person). Tjek <a href="https://www.facebook.com/groups/1602088646679278/">vores Facebook-gruppe</a> ud.
{/if}
		</p>
		
		<h3>
			Forbundne konti:
		</h3>

		<p>
			Facebook: {if $userloginmap['facebook']}Ja{else}Nej{/if}<br>
			Twitter: {if $userloginmap['twitter']}Ja{else}Nej{/if}<br>
			Steam: {if $userloginmap['steam']}Ja{else}Nej{/if}<br>
			Twitch: {if $userloginmap['twitch']}Ja{else}Nej{/if}<br>
			Foobar: {if $userloginmap['foobar']}Ja{else}Nej{/if}<br>

		</p>

</div>

{include file="end.tpl"}
