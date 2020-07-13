<div align="center" class="headlink">
<form action="find.php">
<a href="./" accesskey="i">Info</a>
·
<a href="person.php" accesskey="p" title="Hotkey: P">Person</a>
·
<a href="game.php" accesskey="g" title="Hotkey: G">Game</a>
·
<a href="convent.php" accesskey="c" title="Hotkey: C">Con</a>
·
<a href="conset.php" accesskey="s" title="Hotkey: S">Con series</a>
·
<a href="system.php" accesskey="r" title="Hotkey: R">RPG System</a>
·
<a href="tag.php">Tag</a>
·
<a href="news.php" accesskey="n">News</a>
·
<a href="language.php" accesskey="o" title="Hotkey: O">Translations</a>
·
<a href="showlog.php" accesskey="l" title="Hotkey: L">Log</a>
·
<a href="teknik.php">Technical</a>
·
<a href="ticket.php" accesskey="t" title="Hotkey: T">Tickets</a>
<?php
if ($_SESSION['user_admin'] ) {
?>
·
<a href="achievements.php" accesskey="a">Achievements</a>
·
<a href="feeds.php">Feeds</a>
·
<a href="users.php" accesskey="u">Users</a>
·
<a href="markup.php" accesskey="m">Markup</a>
·
<a href="debug.php" accesskey="d">Debug</a>
<?php
}
?>
<br>
<span style="font-size: 12px;"><label for="ffind" accesskey="k"><span title="Hotkey: K">Quic<u>K</u> find:</span> <input id="ffind" type="text" name="find" value="<?php print htmlspecialchars($find); ?>" size="20"></label>
<?php
$conlock = (int) $_COOKIE['conlock'];
$langlock = (string) $_COOKIE['langlock'];
if ($conlock) {
	print "<br>Default con: <a href=\"convent.php?con=$conlock\">#$conlock</a> <sup><a href=\"../data?con=$conlock\">(show)</a></sup> - <a href=\"lock.php\">release</a>";
}
if ($langlock) {
	print "<br>Language: <a href=\"language.php?do=next\">" . htmlspecialchars( $langlock ). "</a> <sup><a href=\"/" . htmlspecialchars( $langlock ) ."/\">(show)</a></sup> - <a href=\"language.php?setlang=none\">release</a>";
}
?></span><br></form>

</div>
