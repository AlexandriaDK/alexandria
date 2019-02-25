<div align="center" class="headlink">
<form action="find.php">
<a href="./" accesskey="i" style="text-decoration: none">Info</a>
·
<a href="person.php" accesskey="p" style="text-decoration: none">Person</a>
·
<a href="scenarie.php" accesskey="s" style="text-decoration: none">Scenarie</a>
·
<a href="convent.php" accesskey="c" style="text-decoration: none">Con</a>
·
<a href="conset.php" style="text-decoration: none">Con-serie</a>
·
<a href="system.php" accesskey="y" style="text-decoration: none">System</a>
·
<a href="tag.php" style="text-decoration: none">Tag</a>
·
<a href="showlog.php" accesskey="l" style="text-decoration: none">Log</a>
·
<a href="teknik.php" style="text-decoration: none">Teknik</a>
·
<a href="ticket.php" accesskey="t" style="text-decoration: none">Tickets</a>
<?php
if ($_SESSION['user_admin'] ) {
?>
·
<a href="achievements.php" accesskey="a" style="text-decoration: none">Achievements</a>
·
<a href="news.php" accesskey="n" style="text-decoration: none">Nyheder</a>
·
<a href="feeds.php" style="text-decoration: none">Feeds</a>
·
<a href="users.php" accesskey="u" style="text-decoration: none">Users</a>
·
<a href="debug.php" accesskey="d" style="text-decoration: none">Debug</a>
<?php
}
?>
<br>
<span style="font-size: 12px;"><label for="ffind" accesskey="k"><u>K</u>vik-find: <input id="ffind" type="text" name="find" value="<?php print stripslashes(htmlspecialchars($find));?>" size="20"></label>
<?php
$conlock = (int) $_COOKIE['conlock'];
if ($conlock) {
	print "<br>Default con: <a href=\"convent.php?con=$conlock\">#$conlock</a> <sup><a href=\"../data?con=$conlock\">(vis)</a></sup> - <a href=\"lock.php\">frigør</a>";
}
?></span></form>

</div>
