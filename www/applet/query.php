<?php
include_once "query_showall.php";
exit;

require_once "../../inc/rpgconnect.inc";

$q = $_REQUEST['q'];

list($category, $dataid) = explode("_", $q);
if (!$category) $category = 'person';
if (!$dataid) $dataid = '1';


if ($category == 'person') {
  $other_category = 'person';
  $query_maininfo = "SELECT p.id, CONCAT(firstname,' ',surname) AS name FROM person p WHERE id = '$dataid'";
  $query = "SELECT g.id, g.title, g.description FROM game g, pgrel WHERE pgrel.game_id = g.id AND pgrel.person_id = '$dataid'";
} else {
  $category = 'game';
  $other_category = 'person';
  $query_maininfo = "SELECT g.id, g.title, g.description FROM game g WHERE id = '$dataid'";
  $query = "SELECT p.id, CONCAT(firstname,' ',surname) AS name FROM person p, pgrel WHERE pgrel.person_id = p.id AND pgrel.game_id = '$dataid'";
}


$result = mysql_query($query_maininfo) or die("ERROR: " . mysql_error());
list($main_id, $main_label, $main_hint) = mysql_fetch_row($result);
$main_fromid = $category . '_' . $main_id;
if (strlen($main_hint) > 400) $main_hint = substr($main_hint, 0, 400) . "...";

$dataset = array();
$result = mysql_query($query) or die("ERROR: " . mysql_error());
while (list($id, $data, $hint) = mysql_fetch_row($result)) {
  if (strlen($hint) > 400) $hint = substr($hint, 0, 400) . "...";
  $dataset[$id] = $data;
  $datahint[$id] = $hint;
}


// begin output
header("Content-Type: text/xml");
print '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
print "<TGGB version=\"1.00\">\n";

// EDGESETS

$edgeid = 0;
print "<EDGESET>\n";
foreach ($dataset as $id => $data) {
  $edgeid++;
  $toid = $other_category . '_' . $id;
  // Sortering for at lade pile pege fra forfattere til scenarier
  if ($category == "person") {
    $out_fromid = $main_fromid;
    $out_toid = $toid;
  } else {
    $out_fromid = $toid;
    $out_toid = $main_fromid;
  }
  print "<EDGE fromID=\"$out_fromid\" toID=\"$out_toid\" linkNumber=\"$edgeid\" length=\"200\" lastEdge=\"false\"/>\n";
}
print "</EDGESET>\n\n";


// NODESETS

print "<NODESET>\n";

// main node
$current_hint = htmlspecialchars($main_hint);
$current_hint = str_replace("\n", "<br>\n", $current_hint);
print "<NODE nodeID=\"$main_fromid\">\n";
print "<NODE_LABEL label=\"" . htmlspecialchars($main_label) . "\"/>\n";
print "<NODE_HINT isHTML=\"true\" hint=\"" . htmlspecialchars($current_hint) . "\"/>\n";
print "</NODE>\n\n";

// other nodes

foreach ($dataset as $id => $data) {
  $current_hint = htmlspecialchars($datahint[$id]);
  $current_hint = str_replace("\n", "<br>\n", $current_hint);
  $toid = $other_category . '_' . $id;
  print "<NODE nodeID=\"$toid\">\n";
  print "<NODE_LABEL label=\"" . htmlspecialchars($data) . "\"/>\n";
  print "<NODE_HINT isHTML=\"true\" hint=\"" . htmlspecialchars($current_hint) . "\"/>\n";
  print "</NODE>\n\n";
}

// end nodesets

print "</NODESET>\n";

// END TGGB

print "</TGGB>\n";


/*
 <TGGB version="1.00">
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
