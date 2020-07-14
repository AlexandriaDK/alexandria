{include file="head.tpl"}

	<div id="content" lang="en">
		<h1>LOCAL ALEXANDRIA INSTALLATION</h1>
		<p>
			This web site has just been installed. We need to create a database structure and fill in content.
		</p>
		{if $stage == 'dbsetup'}
		<h2>Part 1 of 3: Database structure</h2>
		<p>
			Import structure and data from Alexandria.dk? If you have any old Alexandria tables in the selected database <code class="label">{$dbname|escape}</code> they will be deleted.
		</p>
		<p class="installnote">
			Data will be fetched from <a href="https://alexandria.dk/export.php">https://alexandria.dk/export.php</a> in JSON format.
		</p>
		<form action="./" method="post">
		<div>
			<input type="hidden" name="token" value="{$token}">
			<input type="hidden" name="action" value="importstructure">
			<input type="submit" value="Import structure">
		</div>
		</form>
		{elseif $stage == 'populate'}
		<h2>Part 2 of 3: Content import</h2>
		<p>
			Database structure was created successfully.
		</p>
		<p>
			Import content from Alexandria.dk? If you have any content in the tables in the selected database <code class="label">{$dbname|escape}</code> it will be deleted.
		</p>
		<p class="installnote">
			Data will be fetched from <a href="https://alexandria.dk/export.php">https://alexandria.dk/export.php</a> in JSON format.
		</p>
		<form action="./" method="post">
		<div>
			<input type="hidden" name="token" value="{$token}">
			<input type="hidden" name="action" value="populate">
			<input type="submit" value="Import content">
		</div>
		</form>
		{elseif $stage == 'ready'}
		<h2>Part 3 of 3: Activate site</h2>
		<p>
			Data has been succesfully imported. Active the site? This is a local action and does not contact or notify any external services.
		</p>
		<form action="./" method="post">
		<div>
			<input type="hidden" name="token" value="{$token}">
			<input type="hidden" name="action" value="activate">
			<input type="submit" value="Activate site">
		</div>
		</form>
		{elseif $stage == 'tokenerror'}
		<h2>Token error</h2>
		<p>
			Can't validate browser token. Have you enabled cookies? Please <a href="./">try again</a>.
		</p>
		{else}
		<h2>Unknown territory</h2>
		<p>
			Out of help.
		</p>

		{/if}
	</div>

{include file="end.tpl"}
