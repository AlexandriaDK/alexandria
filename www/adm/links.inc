<div align="center" class="headlink">
<form action="find.php">
<a href="./" accesskey="i">Info</a>
·
<a href="person.php" accesskey="p">Person</a>
·
<a href="scenarie.php" accesskey="s">Scenario</a>
·
<a href="convent.php" accesskey="c">Con</a>
·
<a href="conset.php">Con series</a>
·
<a href="system.php" accesskey="y">RPG System</a>
·
<a href="tag.php">Tag</a>
·
<a href="language.php" accesskey="o">Translations</a>
·
<a href="showlog.php" accesskey="l">Log</a>
·
<a href="teknik.php">Technical</a>
·
<a href="ticket.php" accesskey="t">Tickets</a>
<?php
if ($_SESSION['user_admin'] ) {
?>
·
<a href="achievements.php" accesskey="a">Achievements</a>
·
<a href="news.php" accesskey="n">News</a>
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
<span style="font-size: 12px;"><label for="ffind" accesskey="k">Quic<u>K</u> find: <input id="ffind" type="text" name="find" value="<?php print htmlspecialchars($find); ?>" size="20"></label>
<?php
$conlock = (int) $_COOKIE['conlock'];
$langlock = (string) $_COOKIE['langlock'];
if ($conlock) {
	print "<br>Default con: <a href=\"convent.php?con=$conlock\">#$conlock</a> <sup><a href=\"../data?con=$conlock\">(show)</a></sup> - <a href=\"lock.php\">release</a>";
}
if ($langlock) {
	print "<br>Language: <a href=\"language.php\">" . htmlspecialchars( $langlock ). "</a> <sup><a href=\"/" . htmlspecialchars( $langlock ) ."/\">(show)</a></sup> - <a href=\"language.php?setlang=xx\">release</a>";
}
?></span><br></form>

</div>
