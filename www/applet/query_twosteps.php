<?php
require_once("../../inc/rpgconnect.inc");
require_once("../base.inc.php");
$q = $_REQUEST['q'];

list($category, $dataid) = explode("_",$q);
if (!$category) $category = 'aut';
$other_category = 'aut';
if (!$dataid) $dataid = '1';

$query = "
          SELECT t1.aut_id, t2.aut_id
          FROM asrel AS t1, asrel AS t2
          WHERE t1.aut_id = '$dataid' AND t1.sce_id = t2.sce_id AND t2.aut_id != '$dataid' AND t1.tit_id = 1 AND t2.tit_id = 1
          GROUP BY t2.aut_id
";

$main_id = $dataid;
$main_label = getentry('aut',$main_id);
$main_fromid = $category.'_'.$main_id;

$dataset = $firstfound = $names = array();
$result = mysql_query($query) or die("ERROR: ".mysql_error() );
while (list($first,$second) = mysql_fetch_row($result)) {
	$firstfound[] = $second;
	$dataset[] = $first."_".$second;
	$names[$first] = TRUE;
	$names[$second] = TRUE;
}

if (count($firstfound) > 0) {
	$datasetlist = join(",",$firstfound);
	$query = "
	          SELECT t1.aut_id, t2.aut_id
	          FROM asrel AS t1, asrel AS t2
	          WHERE t1.aut_id IN ($datasetlist) AND t1.aut_id != t2.aut_id
						AND t1.sce_id = t2.sce_id
						AND t1.tit_id = 1 AND t2.tit_id = 1
	          GROUP BY t2.aut_id
		";
	$result = mysql_query($query) or die("ERROR: ".mysql_error() );
	while (list($first,$second) = mysql_fetch_row($result)) {
		$dataset[] = $first."_".$second;
		$names[$first] = TRUE;
		$names[$second] = TRUE;
	}
}

// begin output
header("Content-Type: text/xml");
print '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";
print "<TGGB version=\"1.00\">\n";

// EDGESETS

$edgeid = 0;
print "<EDGESET>\n";
foreach($dataset AS $data) {
	list($first, $second) = explode("_",$data);
// Sortering for at lade pile pege fra forfattere til scenarier
	$edgeid++;
	print "<EDGE fromID=\"aut_$first\" toID=\"aut_$second\" linkNumber=\"$edgeid\" length=\"200\" lastEdge=\"false\"/>\n";
}
print "</EDGESET>\n\n";


// NODESETS

print "<NODESET>\n";

foreach($names AS $aut_id => $foo) {
	$name = getentry('aut',$aut_id);
#	$current_hint = htmlspecialchars($datahint[$id]);
#	$current_hint = str_replace("\n","<br>\n",$current_hint);
	$current_hint = '';
	print "<NODE nodeID=\"aut_$aut_id\">\n";
	print "<NODE_LABEL label=\"".htmlspecialchars($name)."\"/>\n";
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
