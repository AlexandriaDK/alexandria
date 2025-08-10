<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'language';

$admin = ($_SESSION['user_admin'] ?? false);
$langlock = (string) ($_COOKIE['langlock'] ?? '');
$setlang = (string) ($_REQUEST['setlang'] ?? '');
$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$label = (string) ($_REQUEST['label'] ?? '');
$newlabel = (string) ($_REQUEST['newlabel'] ?? '');
$text = (array) ($_REQUEST['text'] ?? []);
$filter = (string) ($_REQUEST['filter'] ?? '');
if (!$admin) {
  $newlabel = $label; // only admins can change labels
}

$id = (int) ($_REQUEST['id'] ?? 0);

function findintemplates($string)
{
  $matches = [];
  $filelist = glob("../smarty/templates/generic/*.tpl");
  foreach ($filelist as $file) {
    $content = file_get_contents($file);
    if (preg_match('/\$_' . $string . '\b/', $content)) {
      $matches[] = basename($file, '.tpl');
    }
  }
  return $matches;
}

// Set language?
if ($setlang) {
  if ($setlang == 'none') {
    setcookie("langlock");
  } else {
    setcookie("langlock", $setlang);
  }
  header("Location: language.php?do=next");
  exit;
}

// Edit texts
if ($action == "update") {
  $old = getcolid("SELECT language, text FROM weblanguages WHERE label = '" .  dbesc($label) . "'");
  $q = "DELETE FROM weblanguages WHERE label = '" . dbesc($label) . "'";
  doquery($q);
  $log = [];
  foreach ($text as $language => $string) {
    $string = trim($string);
    if (strlen($string) > 0) {
      if ($string != $old[$language]) {
        if ($old[$language] == '') { //new
          $log[] = '+' . $language;
        } else { // edited
          $log[] = '~' . $language;
        }
      }
      $q = "INSERT INTO weblanguages (label, text, language, lastupdated) VALUES " .
        "('" . dbesc($newlabel) . "','" . dbesc($string) . "','" . dbesc($language) . "', NOW() )";
      doquery($q);
    } elseif (strlen($old[$language]) > 0) { // deleted
      $log[] = '-' . $language;
    }
  }
  $logtext = "Translation updated: " . $label;
  if ($label != $newlabel) {
    $logtext .= " → $newlabel";
  }
  if ($log) {
    $logtext .= " (" . implode(", ", $log) . ")";
  }
  chlog(NULL, $this_type, $logtext);
  $_SESSION['admin']['info'] = "Texts updated! " . dberror();
  rexit($this_type, ['label' => $newlabel]);
}

$overview = [];
$languages = [];
$languagecount = [];
$alltext = getall("SELECT label, text, language FROM weblanguages ORDER BY LOCATE('_', label), label");
foreach ($alltext as $text) {
  $overview[$text['label']][$text['language']] = $text['text'];
  if (!isset($languagecount[$text['language']])) {
    $languagecount[$text['language']] = 0;
  }
  $languagecount[$text['language']]++;
  $languages[$text['language']] = true;
}
ksort($languages);
$labelcount = count($overview);

htmladmstart("Translations");

?>
<script>
  $(document).ready(function() {
    $("#filterSearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#translations tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
</script>
<?php

// Edit?
$begin = $nextlabel = false;
foreach ($overview as $mylabel => $string) {
  if ($label == '') {
    $begin = true;
  }
  if ($begin == true) {
    foreach ($languages as $language => $dummy) {
      if ($langlock && $language != $langlock) {
        continue;
      }
      if (($string[$language] ?? '') == '') {
        $nextlabel = $mylabel;
        break;
      }
    }
  }
  if ($mylabel == $label) {
    $begin = true;
  }
  if ($nextlabel != false) {
    break;
  }
}

if ($do == "next" && $nextlabel != false) {
  header("Location: language.php?label=" . rawurlencode($nextlabel));
  exit;
}

if ($label) {
  // find next missing translation
  $matches = findintemplates($label);
  print "<form action=\"language.php\" method=\"post\">";
  print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
  print "<input type=\"hidden\" name=\"label\" value=\"" . htmlspecialchars($label) . "\">";
  print "<input type=\"hidden\" name=\"action\" value=\"update\">";
  print "<table>";
  print "<tr><td>Label</td><td><input type=\"text\" name=\"newlabel\" value=\"" . htmlspecialchars($label) . "\" " . ($admin ? "" : "readonly style=\"background: #ccc\"") . " size=\"40\" ></td></tr>";
  print "<tr><td>Used in</td><td>" . ($matches ? implode(", ", $matches) : "[none]") . "</td></tr>";
  foreach ($languages as $language => $dummy) {
    print "<tr><td>" . $language . "</td>";
    print "<td><textarea name=\"text[" . htmlspecialchars($language) . "]\" cols=\"100\" " . ($language == $langlock ? 'class="' . ($langlock == 'de' ? 'langfocusde' : 'langfocus') . '" autofocus' : '') . " lang=\"" . htmlspecialchars($language) . "\">" . htmlspecialchars($overview[$label][$language] ?? '') . "</textarea></td>";
    print "</tr>";
  }
  print "<tr><td></td><td><input type=\"submit\"></td></tr>";
  print "</table>";
  print "</form>";
} elseif ($admin) {
  print "<form action=\"language.php\"><div>New label: <input type=\"text\" name=\"label\" autofocus><input type=\"submit\"></div></form>";
}
if ($nextlabel != false) {
  print "<div class=\"nextlanguage\"><a href=\"language.php?label=" . rawurlencode($nextlabel) . "\">Go to next label with missing translation</a></div>";
}

print '<p>Filter translations: <input id="filterSearch" type="text" placeholder="Search term"></p>';

// overview
print "<table id=\"translations\"><thead>";
print "<tr><th>Label</th>";
foreach ($languages as $language => $dummy) {
  print "<th><a href=\"language.php?setlang=" . urlencode($language) . "\">" . htmlspecialchars($language) . "</a> (" . (floor($languagecount[$language] / $labelcount * 100)) . "%)</th>";
}
print "</tr></thead><tbody>" . PHP_EOL;
if ($admin) {
  print "<tr><td><a href=\"language.php?newlabel=1\">New label</a></td>";
  foreach ($languages as $dummy) {
    print "<td></td>";
  }
  print "</tr>";
}
foreach ($overview as $label => $string) {
  if (!$filter || ($filter && strpos($label, $filter) !== false)) {
    print "<tr onclick=\"location.href=this.firstChild.firstChild.href\">";
    print "<td><a href=\"language.php?label=" . rawurlencode($label) . "\" id=\"label_" . rawurlencode($label) . "\">" . $label . "</a></td>";
    foreach ($languages as $language => $dummy) {
      print "<td>" . htmlspecialchars($string[$language] ?? '') . "</td>";
    }
    print "</tr>" . PHP_EOL;
  }
}
print "</tbody></table>";


print "</body>\n</html>\n";

?>