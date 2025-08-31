<?php
require_once "../../inc/rpgconnect.inc";
require_once "../base.inc.php";

list($category, $dataid) = explode("_", $_REQUEST['q']);
if (!$dataid) $dataid = '1';

$buffer = $visited = array($dataid);
$dataset = array();

while ($person_id = array_shift($buffer)) {
  $i++;
  $query = "
	          SELECT t1.person_id, t2.person_id
	          FROM pgrel AS t1, pgrel AS t2
	          WHERE t1.person_id = '$person_id' AND t1.person_id != t2.person_id
						AND t1.game_id = t2.game_id
						AND t1.title_id = 1 AND t2.title_id = 1
	          GROUP BY t2.person_id
		";
  $result = mysql_query($query) or die("ERROR: " . mysql_error());
  while (list($first, $second) = mysql_fetch_row($result)) {
    $dataset[] = $first . "_" . $second;
    $names[$first] = true;
    $names[$second] = true;
    if (!in_array($second, $visited)) {
      $buffer[] = $second;
      $visited[] = $second;
    }
  }
}

// begin output
header("Content-Type: text/xml");
print '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
print "<TGGB version=\"1.00\">\n";

// EDGESETS

$edgeid = 0;
print "<EDGESET>\n";
foreach ($dataset as $data) {
  list($first, $second) = explode("_", $data);
  // Sortering for at lade pile pege fra forfattere til scenarier
  $edgeid++;
  print "<EDGE fromID=\"aut_$first\" toID=\"aut_$second\" linkNumber=\"$edgeid\" length=\"200\" lastEdge=\"false\"/>\n";
}
print "</EDGESET>\n\n";


// NODESETS

print "<NODESET>\n";

foreach ($names as $person_id => $foo) {
  $name = getentry('person', $person_id);
  #	$current_hint = htmlspecialchars($datahint[$id]);
  #	$current_hint = str_replace("\n","<br>\n",$current_hint);
  $current_hint = '';
  print "<NODE nodeID=\"aut_$person_id\">\n";
  print "<NODE_LABEL label=\"" . htmlspecialchars($name) . "\"/>\n";
  print "<NODE_HINT isHTML=\"true\" hint=\"" . htmlspecialchars($current_hint) . "\"/>\n";
  print "</NODE>\n\n";
}

// end nodesets

print "</NODESET>\n";

// END TGGB

print "</TGGB>\n";


/*
 <TGGB version="1.00"
<EDGESET>
<EDGE fromID="forfatter_1" toID="scenarie_53" linkNumber="1" length="200" lastEdge="false"/>
</EDGESET>

<NODESET>

<NODE nodeID="forfatter_1">
<NODE_LABEL label="Peter Brodersen"/>
<NODE_HINT isHTML="true" hint="Lidt info om Peter Brodersen"/>
</NODE>

<NODE nodeID="scenarie_53">
<NODE_LABEL label="Paranoia the Gathering"/>
<NODE_HINT isHTML="true" hint="I begyndelsen var computeren..."/>
</NODE>

</NODESET>
</TGGB>
*/
