<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Main page</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="/uistyle.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(document).ready(function() {  
	$.get( "frontstat.php?days=7", function( data ) {
		$( "tbody#stats" ).append( data );
	});
	$.get( "frontstat.php?days=365", function( data ) {
		$( "tbody#stats" ).append( data );
	})
});

</script>
</head>

<body>

<?php
include("links.inc.php");
printinfo();
?>

<div style="font-family: Verdana, Tahoma; font-size: 12px; width: 600px;">

<p style="font-style: italic;">Translator? <a href="language.php">Over here!</a></p>

<p>
	You are logged in as: <b><?php print htmlspecialchars($authuser); ?></b>.
</p>

<p>
	Welcome to the editor section of Alexandria. Feel free to join <a href="https://www.facebook.com/groups/1602088646679278/">our Facebook group</a>.
</p>

<h3>
	Active users
</h3>
<table>
<thead>
<tr style="font-size: 0.8em;"><th>Name</th><th>New edits</th><th>Edits</th><th>Most recent edit</th></tr>
</thead>
<tbody id="stats">

</tbody>
</table>

<h3>
	Overview
</h3>

<p style="width: 600px;">
	Read about the navigation and the data model:
	<ul>
		<li><a href="#navigation">Navigation and editing</a></li>
		<li>Menu options:
			<ul>
				<li><a href="#person">Person</a></li>
				<li><a href="#game">Game</a></li>
				<li><a href="#con">Con</a></li>
				<li><a href="#conset">Con series</a></li>
				<li><a href="#rpgsystem">RPG System</a></li>
				<li><a href="#tag">Tag</a></li>
				<li><a href="#news">News</a></li>
				<li><a href="#translations">Translations</a></li>
				<li><a href="#log">Log</a></li>
				<li><a href="#technical">Technical</a></li>
				<li><a href="#tickets">Tickets</a></li>
			</ul>
		</li>
		<li><a href="#datamodel">The data model</a></li>
		<li><a href="#tips">Tips &amp; tricks</a></li>
	</ul>
</p>

<h3 id="navigation">
	Navigation and editing
</h3>

<p>
	For simple usage of the project most of all connections are located under <b>Game</b>. For every
	game you can connect persons and cons here.
</p>

<p>
	The easiest way to navigate and find persons and games is to use the "Quick find" search field at the top at every page. It
	is located at the top of every page. You can easily access it using the hotkey K (usually <b>Shift+Alt K</b>) in
	your browser.
</p>

<h3 id="person">
	Person
</h3>

<p>
	A person is one independent individual. The following are <em>not</em> regarded as a person:
	<ul>
		<li>Pseudonym/alias (e.g. "El Prez", "Anonymous")</li>
		<li>Group of persons (e.g. "Albertslund Ungdomsskole", "The Dogme Collective", "Natural Born Holmers")</li>
	</ul>
	You can add an alias to a person, and a group of persons can be added as a note under a single <a href="#game">game</a> or written in more details as a <a href="#tag">tag</a>.
</p>

<p>
	
	<b>Fields:</b>
	<ul>
		<li><b>Name</b> (split into First name and Surname)<br>
			The person's well-known name. For convenience the full name can be written in a single name field. In that case Alexandria will break the name up into First name and Surname by the last space.
		</li>
		<li><b>Internal note</b><br>
			Optional internal note for administrative use - e.g. documentation
			of spelling and other relevant information for other editors. These
			notes will not appear at the public part of Alexandria or in the
			exports of data. You can add your initials if you add notes that could
			inspire more questions.
		</li>
		<li><b>Date of birth</b><br>
			Date of birth if known and the person accepts having it publicly available. Leave it blank otherwise.
		</li>
		<li><b>Date of death</b><br>
			Date of death if relevant and known. Leave it blank otherwise.
		</li>
	</ul>
</p>

<h3 id="scenarier">
	Game
</h3>

<p>
	A game is a single public known time limited game. This includes regular con scenarios, LARPs,
	designer board game.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>Title</b><br>
			Title of the game. For very long titles it is useful to truncate it or use a well-known
			abbreviation and add the full title as an alias to the game. If the game has no title and is
			only known in the programme by the RPG system it is advised to create a title based on the system
			and add a corresponding event, e.g. "AD&amp;D (Fastaval 1990)". This suffix can also be useful if
			multiple different scenarios happen to have the same title.
		</li>
		<li><b>Description</b><br>
			If possible the original description of the game. Several descriptions can be
			added by pressing the [+] link. Add the language code (e.g. "sv") or the language code
			and a location pointer if several different descriptions have been publicised (e.g. "sv (GothCon 89)").
			Every language serves as its own tab. Double click on the tab to change the language code.
			If the task of transcribing the description is too cumbersome simply type a couple of lines (for ease
			of recognition) and add ".." to the end to mark that the description is not final.
		</li>
		<li><b>Internal note</b><br>
			E.g. details about file distribution rights for a scenario by the author. Also other stuff
			that could be useful in the future in an expansion of Alexandria's model (e.g. payment, expected
			game length). Add initials if you have questions or answers.
		</li>
		<li><b>Participants</b><br>
			An integer or a range of GMs and players. E.g. type <i>4-6</i> for 4 to 6 players. You can
			add more details as well
		</li>
		<li><b>Board game?</b><br>
			Is this a board game? If in doubt ask the author of their view of their own work.
		</li>
		
		<li><b>RPG System</b><br>
			The RPG system the scenario was originally designed for. This list
			is derived from the <a href="#rpgsystem">RPG System</a> section.
			You can add a note here e.g. "2nd edition", "Forgotten Realms" and so on.
			If the RPG system is unknown you can add the custom system name as a note.
		</li>
		<li><b>By</b><br>
			People who have created the game. Type in the name of the person (with the help of autocomplete), select the
			role (e.g. author, illustrator) and add a possible note (e.g. "Character design").
			Add another row by clicking the ➕sign). A single person should be noted several times
			if they have more than one role connected to the game. Remove a person from the list by pressing
			the 🗑️ icon.
			If the background of a name of a person is not green the person is not found in the current
			list of persons in Alexandria. Press the 👤 icon to dynamically add the person.
			You can add an optional organizer such as a group name (e.g. "TRC - Taastrup Roleplaying Club") or other
			moniker the authors and organizers are known as.
		</li>
		<li><b>Con</b><br>
			The conventions the game has been played at. The current connected conventions are
			listed at the left side. At the right side all conventions in Alexandrias are listed.
			Connect a convention by selecting it at the right side and press the appropriate button
			to add it to the left side. Similarly select a convention at the left side and click "Remove"
			to remove it.
		</li>
	</ul>
</p>

<h3 id="con">
	Con
</h3>

<p>
	A convention (con) is a planned open event with a focus on gaming. This includes most events spanning several days.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>Name</b><br>
			Name of the con without the year. More cons can have the same name, e.g. "Fastaval".
		</li>
		<li><b>Year</b><br>
			Four digit year for the time of the event.
		</li>
		<li><b>Start date, End date</b><br>
			Exact date for the start and end of the con if known.
		</li>
		<li><b>Location</b><br>
			Name of location of the place including name of town, e.g. "Katrinebjergskolen, Århus".
		</li>
		<li><b>Country code</b><br>
			Two letter ISO country code for the location of the con, e.g. <i>se</i> for Sweden.
		</li>
		<li><b>Description</b><br>
			Various objective information about the event, e.g. guests of honor, pricing, major events at the con.
		</li>
		<li><b>Internal note</b><br>
			Stuff that could be useful in the future in an expansion of Alexandria's model (e.g. payment).
		</li>
		<li><b>Con series</b><br>
			The series this con is connected to. Default is <i>Other</i>, a catch-all for cons not part of any series.
		</li>
		<li><b>Data validity</b><br>
			Status for collection and entering data for the convention. Some selections will have the con show up on the <a href="../todo">public to-do</a> page.
		</li>
		<li><b>Cancelled?</b><br>
			Whether the convention was cancelled. The details about the cancellation should be written under the description.
		</li>

	</ul>
</p>

<h3 id="conset">
	Con series
</h3>

<p>
	A con series is a list of an amount of cons that are connected to each other by the same organizing group
	or are clearly sequels to each other.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>Name</b><br>
			The generic name for all cons in this series, usually based on a running name
			(e.g. ARCON) or the organizing group (e.g. "Con II Crew").
		</li>
		<li><b>Description</b><br>
			Any objective information of the con series as a whole.
		</li>
		<li><b>Internal note</b><br>
			Any objective information of the con series as a whole.
		</li>
		<li><b>Country code</b><br>
			Two letter ISO country code for the location of the con, e.g. <i>dk</i> for Denmark. Any
			convention under this con series without a country present will default to this country. This
			makes it easier to have all the conventions for a single con series located in the same country.
		</li>

	</ul>

</p>

<h3 id="rpgsystem">
	RPG System
</h3>

<p>
	An RPG system is an indepent role-playing system. A single RPG system usually contains variations such as editions within itself. These specifications will usually be added for the simple game. If a large amount of scenarios are written in specific variations (e.g. AD&amp;D vs. Dungeons &amp; Dragons 5th Editions) it makes sense to have separate entries for the two editions.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>Name</b><br>
			The generic name for the system.
		</li>
		<li><b>Description</b><br>
			Any objective information of the system as a whole such as authors, distributors, releases, dates, expansions, and so on.
		</li>
	</ul>

</p>

<h3 id="tag">
	 Tag
</h3>

<p>
	A tag is a keyword (one or several words) that can be attached to one or several games. This is useful for scenarios with sequels, organizer groups, themes, time periods and so on. Tags can exist without any games connected to it as a form of article of a topic.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>Tag</b><br>
			The tag keyword, e.g. 
		</li>
		<li><b>Description</b><br>
			Any useful information about the tag. This can be an article by itself.
		</li>
	</ul>

</p>
<h3 id="tag">
	 News
</h3>

<p>
	Short news post for the front page of Alexandria.
</p>

<p>
	<b>Fields:</b>
	<ul>
		<li><b>News</b><br>
			The text for the news post. Keep it short.
		</li>
		<li><b>Date and time</b><br>
			Timestamp for the news post. Format in YYYY-MM-DD HH:MM:SS - leave blank for current date and time.
		</li>
		<li><b>Online</b><br>
			Toggle to add or hide the news post from the front page.
		</li>
	</ul>
</p>

<h3 id="translations">
	Translations
</h3>
<p>
	Texts and translations for the public Alexandria pages. Click on a label to edit the associated text. Click on a language code (e.g. <i>de</i> for German/Deutsch) to focius on this language. This makes it easier to find missing translations for a single language.
</p>

<h3 id="log">
	Log
</h3>
<p>
	Internal log of edits by editors and users.
</p>

<h3 id="technical">
	Technical
</h3>

<p>
	The technical page is mostly an informative page for check-ups of irregularities in the
	database as well as some statistics. This includes a lust of people in Alexandria without
	any connections at all, cons without any known start date and so on. This could highlight
	errors the be followed up upon. Also check out the <a href="../todo">public to-do</a> page.
</p>

<h3 id="tickets">
	Tickets
</h3>

<p>
	The ticket section is the organized system for handling corrections submitted by users.
	It is possible to see open tickets, post internal notes (if some clarity is needed), and close tickets.
	This system only runs internally. If the submitter needs to be contacted an editor has to email the person
	directly. This can not be done though the ticket system.
</p>

<h3 id="datamodel">
	The data model
</h3>

<p>
	The dull parts: How everything is and should be connected as a database.
</p>

<p>
	The Alexandria database is a relational database consisting of multiple relations. Basically this
	means that we avoid redundant data and every piece of data is simply linked together. A person can
	be the author of several scenarios, and a scenario can also be written by several persons. A
	con can consist of many scenarios, and a scenario can be played at several cons. You get the idea.
</p>

<p>
	Part of the model can be displayed the following way:
</p>

<!--
Gott in Himmel, we should have some better SVG illustrations here
-->

<table cellspacing="1" cellpadding="1" style="font-size: 14px; border: 1px solid black; padding: 2px 2px 2px 2px;">

	<tr>
		<th title="E.g. &quot;Peter Brodersen&quot;, &quot;Palle Schmidt&quot;">Person</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<td style="font-size: 10px" colspan=3>(Many-to-many relationship)</td>
		<th>⇔</th>
		<th align="left" title="E.g. &quot;Author&quot;, &quot;Illustrator&quot;, &quot;Layouter&quot;">Role</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<th title="E.g. &quot;Dødens Skygge&quot;, &quot;Dogme#1 - Pesten&quot;, &quot;Match Madness&quot;">Game</th>
		<th>⇔</th>
		<th align="left" title="E.g. &quot;AD&amp;D&quot;, &quot;Paranoia&quot;, &quot;GURPS&quot;">RPG System</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<td style="font-size: 10px" colspan=3>(Many-to-many relationship)</td>
		<th>⇔</th>
		<th align="left" title="E.g. &quot;Premiere&quot;, &quot;Re-run&quot;, &quot;Cancelled&quot;">Presentation</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<th title="E.g. &quot;Fastaval 1996&quot;, &quot;Viking Con 20&quot;">Con</th>
		<th>⇔</th>
		<th align="left" title="E.g. &quot;Fastaval&quot;, &quot;Viking Con&quot;">Con series</th>
	</tr>

</table>		

<p>
	A <b>person</b> can have been involved in several games, even as an illustrator for one game and author for another game (mentioned as <b>Role</b>).
</p>

<p>
	A <b>game</b> can have several persons connected. A game can however only have one <b>system</b> connected.
	A scenario can't be both <i>AD&amp;D</i> and Warhammer Fantasy Roleplay. If this is still the case just
	add a note/trivia to the game.
</p>

<p>
	A <b>con</b> can contain several games - also games connected to other cons. In this connection
	the <b>presentation</b> of the game is also noted, e.g. premiere or test run. This way a scenario
	can be marked as cancelled for one con and as premiere for another con.
</p>

<p>
	A <b>Con series</b> is a list of cons under the same moniker or organizer team. A con can only
	be associated to one con series.
</p>

<p>
	Example of usage of the data model for database users: We want to find out which persons
	who have ever been an author for a specific con series, e.g. Fastaval. In this case we need to look at the
	graph and connect "person" to "con series". From here we can see we need to move from Person across Game and Con
	to end up at the Con series. Or in other words, we need to find the persons who have written scenarios that has
	been played at cons who are part of Fastaval.
</p>


<h3 id="tips">
	Tips &amp; tricks
</h3>

<p>
	<ul>
		<li>Use the "Quick search" field! This saves a lot of time and might
			be the only way to access a data entry. Note the hotkey
			(Hotkey + K) for the search field.
		</li>
		<li>
			If you need to enter a program for a con you can go to the related con page. From
			here you can click the "Use con as default con" link. With this selected the con will
			be placed at the top at the scenario page for an easy way to select this.
		</li>
		<li>
			For most input fields you can link to other entries by adding sets of three brackets around
			the topic, e.g. [[[Peter Brodersen]]]. On the public page this will display as a link, e.g:
			<a href="../find?find=Peter%20Brodersen">Peter Brodersen</a>
		</li>
		<li>
			Are you missing a feature that could make your life as an editor easier? 
			Drop a note in the <a href="https://www.facebook.com/groups/1602088646679278/">Facebook editor group</a> or
			mail administrator Peter Brodersen at <a href="mailto:peter@ter.dk">peter@ter.dk</a>
		</li>
		<li>
			Hotkey to go up a page: <a href="../" accesskey="q">Hotkey + Q</a>.
		</li>
	</ul>
</p>

</div>

</body>
</html>
