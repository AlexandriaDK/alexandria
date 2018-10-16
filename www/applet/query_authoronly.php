<?php
require_once("../../inc/rpgconnect.inc");

$q = $_REQUEST['q'];

list($category, $dataid) = explode("_",$q);
if (!$category) $category = 'aut';
if (!$dataid) $dataid = '1';

if ($category == 'aut') {
	$other_category = 'aut';
	$query_maininfo = "SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$dataid'";
	$query = "
	          SELECT t2.aut_id, CONCAT(firstname,' ',surname) AS name
	          FROM asrel AS t1, asrel AS t2, aut
	          WHERE t1.aut_id = '$dataid' AND t1.sce_id = t2.sce_id AND t2.aut_id != '$dataid' AND t2.aut_id = aut.id AND t1.tit_id = 1 AND t2.tit_id = 1
	          GROUP BY t2.aut_id
		";

}

$result = mysql_query($query_maininfo) or die("ERROR: ".mysql_error() );
list($main_id,$main_label) = mysql_fetch_row($result);
$main_fromid = $category.'_'.$main_id;

$dataset = array();
$result = mysql_query($query) or die("ERROR: ".mysql_error() );
while (list($id,$data) = mysql_fetch_row($result)) {
	$dataset[$id] = $data;
}

if (count($dataset) > 0) {
	$commalist  = array();
	foreach($dataset AS $key => $value) $commalist[] = $key;
	$datasetlist = join(",",$commalist);
	$query = "
	          SELECT t2.aut_id, CONCAT(firstname,' ',surname) AS name
	          FROM asrel AS t1, asrel AS t2, aut
	          WHERE t1.aut_id IN ($datasetlist) AND t1.sce_id = t2.sce_id AND t2.aut_id NOT IN ($datasetlist) AND t2.aut_id = aut.id AND t1.tit_id = 1 AND t2.tit_id = 1
	          GROUP BY t2.aut_id
		";
	$result = mysql_query($query) or die("ERROR: ".mysql_error() );
	while (list($id,$data) = mysql_fetch_row($result)) {
#		$dataset[$id] = $data;
	}

}


// begin output
header("Content-Type: text/xml");
print '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
print "<TGGB version=\"1.00\">\n";

// EDGESETS

$edgeid = 0;
print "<EDGESET>\n";
foreach($dataset AS $id => $data) {
	$toid = $other_category.'_'.$id;
// Sortering for at lade pile pege fra forfattere til scenarier
	if ($category == "aut") {
		$out_fromid = $main_fromid;
		$out_toid = $toid;
	} else {
		$out_fromid = $toid;
		$out_toid = $main_fromid;
	}
	$edgeid++;
	print "<EDGE fromID=\"$out_fromid\" toID=\"$out_toid\" linkNumber=\"$edgeid\" length=\"200\" lastEdge=\"false\"/>\n";
	$edgeid++;
	print "<EDGE fromID=\"$out_toid\" toID=\"$out_fromid\" linkNumber=\"$edgeid\" length=\"200\" lastEdge=\"false\"/>\n";
}
print "</EDGESET>\n\n";


// NODESETS

print "<NODESET>\n";

// main node
$current_hint = htmlspecialchars($main_hint);
$current_hint = str_replace("\n","<br>\n",$current_hint);
print "<NODE nodeID=\"$main_fromid\">\n";
print "<NODE_LABEL label=\"".htmlspecialchars($main_label)."\"/>\n";
print "<NODE_HINT isHTML=\"true\" hint=\"".htmlspecialchars($current_hint)."\"/>\n";
print "</NODE>\n\n";

// other nodes

foreach($dataset AS $id => $data) {
	$current_hint = htmlspecialchars($datahint[$id]);
	$current_hint = str_replace("\n","<br>\n",$current_hint);
	$toid = $other_category.'_'.$id;
	print "<NODE nodeID=\"$toid\">\n";
	print "<NODE_LABEL label=\"".htmlspecialchars($data)."\"/>\n";
	print "<NODE_HINT isHTML=\"true\" hint=\"".htmlspecialchars($current_hint)."\"/>\n";
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
?>
