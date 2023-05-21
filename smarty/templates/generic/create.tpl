{assign var="pagetitle" value="Create game"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		Create game
	</h2>

	<p>
		Are we missing anything? You can add a game to the Alexandria database
		here. All information is optional.
	</p>

	<p>
		Don't worry about missing stuff, typos, and so on. We have editors who will
		check up on the content.
	</p>

	<p>
		If you have other questions, have created a game in error, or have other issues feel free to <a
			href="kontakt">contact us</a>.
	</p>

	{if ! isset($user_id)}
	<p>
		<em>You are not logged in.</em>
	</p>
	{else}

	<form action="adm/user_creategame.php" method="post">
		<table id="createtable">
			<tr>
				<td>Title of the game:</td>
				<td><input type="text" name="title" style="width: 200px" id="gametitle" required></td>
			</tr>
			<tr id="titledidyoumean" style="display: none;">
				<td>Did you mean:<br>(These games already exist)</td>
				<td id="existingtitles"></td>
			</tr>
			<tr>
				<td>LARP?</td>
				<td><input type="checkbox" name="larp" checked></td>
			</tr>
			<tr>
				<td>Authors and organizers:</td>
				<td><input type="text" name="person[0][name]" class="personinput" placeholder="Name"><br><input
						type="text" name="person[1][name]" class="personinput" placeholder="Name"><br><input type="text"
						name="person[2][name]" class="personinput" placeholder="Name"><br><input type="text"
						name="person[3][name]" class="personinput" placeholder="Name"><br><input type="text"
						name="person[4][name]" class="personinput" placeholder="Name"></td>
			</tr>
			<tr>
				<td>Begin date:</td>
				<td><input type="date" name="runbegin"></td>
			</tr>
			<tr>
				<td>End date:</td>
				<td><input type="date" name="runend"></td>
			</tr>
			<tr>
				<td>Run location:</td>
				<td><input type="text" name="runlocation" style="width: 200px" id="locationreference"></td>
			</tr>
			<tr>
				<td>Run language:</td>
				<td><input type="text" name="rundescription" style="width: 200px" placeholder="E.g. English run"></td>
			</tr>
			<tr>
				<td>Website:</td>
				<td><input type="url" name="website" style="width: 200px">
			<tr>
				<td>Description:</td>
				<td><textarea name="description" placeholder="Presentation text for scenario"
						style="width: 500px; height: 100px"></textarea></td>
			</tr>
			<tr>
				<td>Other information:</td>
				<td><textarea name="notes"
						placeholder="Notes for Alexandria editors, e.g. further runs and locations, amount of participants, anecdotes, etc."
						style="width: 500px; height: 100px"></textarea></td>
			</tr>
			<tr>
				<td>Your e-mail:<br>(optional)</td>
				<td><input type="email" name="useremail">
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Create game">
		</table>
		<input type="hidden" name="token" value="{$token}">
	</form>
	{/if}
</div>

<script>
	let requestCount = 0;
	let lastTitle = '';
	$(function() {
		$('#gametitle').on("keyup", function() {
			requestCount++;
			var title = $('#gametitle').val();
			if (title.length > 3 && title != lastTitle) {
				lastTitle = title;
				var thisCount = requestCount;
				$.getJSON("xmlrequest.php", { action: 'titlesearch', q: title }, function(data) {
					if (thisCount != requestCount) { // newer request; abort 
						return false;
					}
					var html = '';
					for (var i = 0; i < data.length && i < 5; i++) {
						html += '<a href="data?scenarie=' + data[i][0] +
							'" target="_blank" class="game">' + data[i][1] +
							'</a><br>'; // Title need escape
					}

					if (html) {
						$('#titledidyoumean').css('display', 'table-row');
						$('#existingtitles').html(html);

					} else {
						$('#titledidyoumean').css('display', 'none');
					}
				})

			}
		});

		$("input#locationreference").autocomplete({
			source: 'xmlrequest.php?action=locationsearch',
			autoFocus: false,
			minLength: 3,
			delay: 100
		})

		$(".personinput").autocomplete({
			source: 'ajax.php?type=person',
			autoFocus: true,
			delay: 10,
			minLength: 3
		});

	});
</script>

{include file="end.tpl"}