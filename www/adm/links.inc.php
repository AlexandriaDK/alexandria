<?php
$find = $_REQUEST['find'] ?? '';
?>
<div align="center" class="headlink">
  <nav>
    <form action="find.php">
      <a href="./" accesskey="i">Info</a> · <a href="person.php" accesskey="p" title="Hotkey: P">Person</a> · <a href="game.php" accesskey="g" title="Hotkey: G">Game</a> · <a href="convention.php" accesskey="c" title="Hotkey: C">Con</a> · <a href="conset.php" accesskey="s" title="Hotkey: S">Con series</a> · <a href="gamesystem.php">RPG System</a> · <a href="tag.php">Tag</a> · <a href="magazine.php" accesskey="n" title="Hotkey: N">Magazine</a> · <a href="locations.php" accesskey="l" title="Hotkey: L">Locations</a> · <a href="news.php" accesskey="n">News</a> · <a href="rpgforum.php">RPGFORUM</a> · <a href="review.php">Reviews</a> · <a href="language.php" accesskey="o" title="Hotkey: O">Translations</a> ·
      <a href="showlog.php">Log</a> · <a href="ticket.php">Tickets</a> · <a href="checkup.php">Checkup</a> · <a href="feeds.php">Feeds</a>
      <?php
      if ($_SESSION['user_admin']) {
      ?>
        · <a href="achievements.php">Achievements</a> · <a href="users.php" accesskey="u">Users</a> · <a href="markup.php" accesskey="m">Markup</a> · <a href="debug.php" accesskey="d">Debug</a>
      <?php
      }
      ?>
      <br>
      <span style="font-size: 12px;"><label for="ffind" accesskey="k"><span title="Hotkey: K">Quic<u>K</u> find:</span> <input id="ffind" type="search" name="find" value="<?php print htmlspecialchars($find); ?>" size="20"></label>
        <?php
        $conlock = (int) ($_COOKIE['conlock'] ?? 0);
        $langlock = (string) ($_COOKIE['langlock'] ?? '');
        if ($conlock) {
          print "<br>Default con: <a href=\"convention.php?con=$conlock\">#$conlock</a> <sup><a href=\"../data?con=$conlock\">(show)</a></sup> - <a href=\"lock.php\">release</a>";
        }
        if ($langlock) {
          print "<br>Language: <a href=\"language.php?do=next\">" . htmlspecialchars($langlock) . "</a> <sup><a href=\"/" . htmlspecialchars($langlock) . "/\">(show)</a></sup> - <a href=\"language.php?setlang=none\">release</a>";
        }
        ?></span><br>
    </form>
  </nav>
</div>