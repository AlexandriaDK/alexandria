{assign var="pagetitle" value="{$_create_creategame|escape}"}
{include file="head.tpl"}

<div id="content">

	<h2 class="pagetitle">
		{$_create_creategame|escape}
	</h2>

	<p>
		{$_create_introtext|nl2br}
	</p>

	{if ! isset($user_id)}
	<p>
		<em>{$_create_notloggedin}</em>
	</p>
	{else}

	<form action="adm/user_creategame.php" method="post">
		<table id="createtable">
			<tr>
				<td>{$_create_input_title}</td>
				<td><input type="text" name="title" style="width: 200px" id="gametitle" required></td>
			</tr>
			<tr id="titledidyoumean" style="display: none;">
				<td>{$_create_input_didyoumean|nl2br}</td>
				<td id="existingtitles"></td>
			</tr>
			<tr>
				<td>{$_create_input_type}</td>
				<td>
					<select name="gametype" id="gametype" required>
						<option value="">{$_create_input_selecttype}</option>
						<option value="larp">{$_create_input_larp}</option>
						<option value="tabletop">{$_create_input_tabletop}</option>
						<option value="boardgame">{$_create_input_boardgame}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><span id="text_authors">{$_create_input_authors_organizers}</span><span id="text_designers" style="display: none;">{$_create_input_designers}</span></td>
				<td><input type="text" name="person[0][name]" class="personinput" placeholder="{$_name|ucfirst}"><br><input
						type="text" name="person[1][name]" class="personinput" placeholder="{$_name|ucfirst}"><br><input type="text"
						name="person[2][name]" class="personinput" placeholder="{$_name|ucfirst}"><br><input type="text"
						name="person[3][name]" class="personinput" placeholder="{$_name|ucfirst}"><br><input type="text"
						name="person[4][name]" class="personinput" placeholder="{$_name|ucfirst}"></td>
			</tr>
			<tr>
				<td>{$_create_input_begindate}</td>
				<td><input type="date" name="runbegin" id="runbegin"></td>
			</tr>
			<tr>
				<td>{$_create_input_enddate}</td>
				<td><input type="date" name="runend" id="runend"></td>
			</tr>
			<tr>
				<td>{$_create_input_runlocation}</td>
				<td><input type="text" name="runlocation" style="width: 200px" id="locationreference"></td>
			</tr>
			<tr>
				<td>{$_create_input_runlanguage}</td>
				<td><input type="text" name="rundescription" style="width: 200px" placeholder="{$_create_input_runlanguage_placeholder|escape}"></td>
			</tr>
			<tr>
				<td>{$_links_website}:</td>
				<td><input type="url" name="website" style="width: 200px">
			<tr>
				<td>{$_sce_description}:</td>
				<td><textarea name="description" placeholder="{$_create_input_description_placeholder|escape}"
						style="width: 500px; height: 100px"></textarea></td>
			</tr>
			<tr>
				<td>{$_create_input_otherinformation}</td>
				<td><textarea name="notes"
						placeholder="{$_create_input_other_placeholder|escape}"
						style="width: 500px; height: 100px"></textarea></td>
			</tr>
			<tr>
				<td>{$_create_input_email|nl2br}</td>
				<td><input type="email" name="useremail">
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="{$_create_creategame|escape}">
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

		$("select#gametype").on("change", function() {
			if (this.value == 'larp' || this.value == 'tabletop') {
				$("span#text_authors").show();
				$("span#text_designers").hide();
			}
			if (this.value == 'boardgame') {
				$("span#text_authors").hide();
				$("span#text_designers").show();
			}
		});

		$("input#runbegin").on("blur", function() {
			if ($("input#runend").val() == "") {
				$("input#runend").val($("input#runbegin").val())
			}
		});

		$("input#locationreference").autocomplete({
			source: 'xmlrequest.php?action=locationsearch',
			autoFocus: false,
			minLength: 3,
			delay: 100
		});

		$(".personinput").autocomplete({
			source: 'ajax.php?type=person',
			autoFocus: true,
			delay: 10,
			minLength: 3
		});

	});
</script>

{include file="end.tpl"}