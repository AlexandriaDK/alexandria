<?php
#if ($REMOTE_ADDR != "192.168.1.30") {
#	include("wait.php");
#	exit;
#}
/*
[12:14] heel: hvad med de andre mystifisticonner?
[12:14] Dark Penguin: Øh... hvis du har programmer, så sig til :)
[12:15] Dark Penguin: Jeg havde kun ét mystifisticon-program, så hvis du har noget, der ikke er på listen, så sig endelig til!
[12:16] heel: det tror jeg sq ikke har, men jeg kan huske at jeg var med til at arrangerer et live, jeg tror det var på den første. Det hed "Kampen på Blodsletten" det var Midtdjurs Ungdomsskole der arrangerede det
[12:16] heel: der var også et smølfe scenarie det år, men kan ikke lige huske hvad det hed
*/

/*
18. november 2001:
Antal scenarier: 504
Antal personer: 306
Antal systemer: 26
Antal cons: 92

17. december 2001:
Antal scenarier: 1022
Antal personer: 550
Antal systemer: 39
Antal cons: 111

29. december 2001:
Antal scenarier: 1082
Antal personer: 571
Antal systemer: 41
Antal cons: 112

24. januar 2002:
Antal scenarier: 1181
Antal personer: 594
Antal systemer: 41
Antal cons: 117

18. februar 2002:
Antal scenarier: 1342
Antal personer: 659
Antal systemer: 46
Antal cons: 129

3. april 2002:
Antal scenarier: 1436
Antal personer: 694
Antal systemer: 48
Antal cons: 139

1. maj 2002:
Antal scenarier: 1466
Antal personer: 696
Antal systemer: 48
Antal cons: 145

25. maj 2002:
Antal scenarier: 1507
Antal personer: 710
Antal systemer: 48 
Antal cons: 147

9. juli 2002:
Antal scenarier: 1592 
Antal personer: 753 
Antal systemer: 52 
Antal cons: 150 

12. juli 2002:
Antal scenarier: 1592
Antal personer: 767
Antal systemer: 52
Antal cons: 150

11. august 2002:
Antal scenarier: 1625 
Antal personer: 779 
Antal systemer: 58 
Antal cons: 153 

25. september 2002:
Antal scenarier:  1666 
Antal personer:  787 
Antal systemer:  59 
Antal cons:  153 

15. oktober 2002:
Antal scenarier: 1742
Antal personer: 817
Antal systemer: 61
Antal cons: 155

16. oktober 2002:
Antal scenarier: 1804
Antal personer: 831
Antal systemer: 64
Antal cons: 157

17. oktober 2002:
Antal scenarier: 1923
Antal personer: 854
Antal systemer: 70
Antal cons: 164

12. november 2002:
Antal scenarier: 1924
Antal personer: 854
Antal systemer: 69
Antal cons: 164

9. januar 2003:
Antal scenarier:  1927
Antal personer:  855
Antal systemer:  69
Antal cons:  164

15. januar 2003:
Antal scenarier: 1941
Antal personer: 854
Antal systemer: 70
Antal cons: 165

18. januar 2003:
Antal scenarier: 1962
Antal personer: 859
Antal systemer: 70
Antal cons: 165

17. februar 2003:
Antal scenarier: 1965
Antal personer: 859
Antal systemer: 70
Antal cons: 166

28. februar 2003:
Antal scenarier: 1971
Antal personer: 861
Antal systemer: 70
Antal cons: 167


Takkeliste: (takkelisten er flyttet over i OSS'en)
Jakob Vestergren Bavnshøj (58 con-programmer!)
Kristoffer Apollo (Fastaval 92+93, MANGE rettelser og afklaringer)
Thomas Jakobsen (Saga, Fønix, gamle con-datoer)
Morten Juul (egne scenarier, stor Orkon-oversigt)
Merlin Mann
Atlantis Scenarie Service (atlantis.trc.dk - liste over mange scenarier)
Kristoffer Apollo, Lars Kroll, Sebastian Flamant, m.fl (liste over Otto-vindere og kandidater i tidens løb, skrevet på RPGForum)
Ask Agger (liste over egne scenarier og VP-scenarier i det hele taget)
Kjeld Johansen (Con Dôme)
Claustrum Con hjemmeside
Michael Erik Næsby (egne scenarier)
Peter Bengtsen (liste over Claustrum Cons)
Natural Born Holmers (egne scenarier + novelle)
Geert Lund (Pentacon, bl.a. datoer)
Mikkel Bækgaard (egne scenarier)
André Just Simonsen (DRF-cons - forhåbentligt!)


Ideer til forside:
nyeste tilføjelser
nyeste anmeldelser
ugens scenarie?
links til div. top 10-lister - eller direkte på forsiden?
links til genrer? systemer?
afstemning?
link til "min side" - den personlige login
link til con, scenarie, forfatter oversigt?
søgefelt
kommende cons

Forslag: Citatboks, folks citater

Kent Hytten:
TRoA 4, 5, intern, 6

Christian Nørgaard:
Mafia Big Time
TRoA 2000 18-20/8
TRoA 1999
MagicMania
CAH9
TRoA Intern Con 1996
TRoA Intern JuleHyggecon
Atani AlCapone Mafia Bigtime
Spiltræf 9 nyhedsbrev
Mystifisticon 1997 (2 stk)
VinterVap
Tanken
Krikkit 95
Nordcon 95
Fastas Magic- og brætspilscon 1995
Viborg con 93
Placebo-con 1995
The Battle 1995
ConDome 1996
EsCon96
Tanken?! 1996
Krikkit III
KG II
Chop Con 2000
JuleCon 98 - ATANI
Hyggecon 98
Pentacon 1996
Chop Con 99
CAH3
TRoA X
CAH2
CAH1 (!)
TRoA V
CAH5
CAH4
TRoA VI
CAH6
FiskeCon III
Sconnert
BEAST 2K
Claustrum III
BEAST 98

*/

require("./connect.php");
require("base.inc");
require("template.inc");

$person = isset($_REQUEST['person']) ? intval($_REQUEST['person']) : 0;
$scenarie = isset($_REQUEST['scenarie']) ? intval($_REQUEST['scenarie']) : 0;
$game = isset($_REQUEST['game']) ? intval($_REQUEST['game']) : 0;
$con = isset($_REQUEST['con']) ? intval($_REQUEST['con']) : 0;
$conset = isset($_REQUEST['conset']) ? intval($_REQUEST['conset']) : 0;
$system = isset($_REQUEST['system']) ? intval($_REQUEST['system']) : 0;
$year = isset($_REQUEST['year']) ? intval($_REQUEST['year']) : 0;
$tag = isset($_REQUEST['tag']) ? (string) $_REQUEST['tag'] : NULL;

/*
if ($year < 1970 || $year > 2100) { // :TODO: Fix Y2K100-problem 
	header("Location: calendar");
	$year = date("Y");
}
*/

#if (!$bgcolor) $bgcolor="#f4c8ff";
#if (!$rammecolor) $rammecolor="#c000fb";

$t->assign('ip',$_SERVER['REMOTE_ADDR']);
$t->assign('penguin_ip',isset($_ENV['PenguinIP']) ? $_ENV['PenguinIP'] : NULL);

if ($person) {
	include ("person_t.inc");
} elseif ($scenarie) {
	include ("game_t.inc");
//	include ("scenario_t.inc");
} elseif ($game) {
	include ("game_t.inc");
} elseif ($con) {
	include ("convent_t.inc");
} elseif ($conset) {
	include ("conset_t.inc");
} elseif ($system) {
	include ("system_t.inc");
} elseif ($year) {
	include ("year_t.inc");
} elseif ($tag) {
	include ("tag_t.inc");
} else {
	include ("default.inc");
}

?>
