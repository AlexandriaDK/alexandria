<?php
function nib($hex) {
	return (hexdec($hex)%4);
}

function oct($hex) {
	return (hexdec($hex)%8);
}

function aprildescription($id) {
	$h = md5($id . "Aprilsnar2015");
	
	$description = "";

	$start = [
		0 => 'I en verden fuld af ondskab, hvor drager med sylespidse tænder hærger byerne, ',
		1 => 'I den lille landsby har orker i tusindvis af år stjålet de lokales spædbørn. Nu ',
		2 => 'I Pandrup i Nordjylland taler naboerne ikke længere med hinanden. Men så ',
		3 => 'Rumstationen Bjældeklang er i orbit om planeten Jacqueline. På Stardate 2-718-281828 ',
		4 => 'Neo-Miami, år 6174. Under fusionen af de to megacorps Tacit Trinary og iButler ',
		5 => 'Efter den stigende mængde vold på Amager, hvor politiet ikke er i stand til at udrette noget, ',
		6 => 'På den frygtede borg Craggamoor sidder necromanceren Zoras i sit tårn, bygget af menneskeknogler. Mens han planlægger at vælte Lordan, herskeren over det nordlige kongerige, ',
		7 => 'Til en ellers hyggelig reunion for gamle elever fra Elverhøj Gymnasium '
	];

	$heroes = [
		0 => 'tager seks personer - en fighter, en cleric, en thief, en magic-user, en ranger og en bard - kampen op mod ',
		1 => 'møder et lille detektivbureau af smarte københavnere op for at snakke med ',
		2 => 'lander der et rumskib med seks mærkelige væsener, medbringende ',
		3 => 'står stjernerne denne aften præcis som legenden fortalte. Ud fra sprækker i jorden angriber ',
		4 => 'udgiver en klummeskribent en kronik om den stigende gentrificering af København. Hun bliver derefter kontaktet af ',
		5 => 'opstår nye følelser (hver følelse i scenariet er spillet af en spiller) i mødet med ',
		6 => 'prøver en gruppe gamle venner at starte en virksomhed op med hjælp fra ',
		7 => 'vil skæbnen, at en række sjæle mødes på et Internet-forum for '

	];

	$encounter_prefix = [
		0 => 'overtroiske ',
		1 => 'grønne ',
		2 => 'level 20 ',
		3 => 'en hær af kraftigt bevæbnede ',
		4 => 'maniodepressive ',
		5 => 'misogyne ',
		6 => 'insolvente ',
		7 => 'misforståede '
	];

	$encounter = [
		0 => 'teater-elever ',
		1 => 'Deep Ones ',
		2 => 'nazisatanzombier ',
		3 => 'dræberpingviner ',
		4 => 'kultister ',
		5 => 'talende ænder ',
		6 => 'slimboffer ',
		7 => 'ådselsluskere '
	];

	$encounter_suffix = [
		0 => 'som har brug for 40 liter blod inden morgengry.',
		1 => 'med voldsomme tømmermænd.',
		2 => 'uden nogen hæmninger i livet.',
		3 => 'hvoraf én af dem er laktoseintolerant.',
		4 => 'på hævntogt.',
		5 => 'på flugt fra en korrupt sherif.',
		6 => 'som ikke tager imod bestikkelse.',
		7 => 'på vej til halbal.'
	];

	$event_prefix = [
		0 => 'Efter en længere forhandling, ',
		1 => 'Efter en ophedet diskussion, ',
		2 => 'Efter en hård kamp, ',
		3 => 'Efter en uge i stasis-pods, ',
		4 => 'Efter en omlægning af realkreditlån, ',
		5 => 'Efter 4000 år i dvale, ',
		6 => 'Efter flere uger på flugt, ',
		7 => 'Efter et majestætisk fyrværkeri, '
	];

	$event_prefix2 = [
		0 => 'hvor der hverken er adgang til chokolade eller bacon, ',
		1 => 'hvor heltene adskillige gange er ved at dø af magic missiles, ',
		2 => 'med intet andet musik end Mariah Careys "Greatest Hits" fra 2001, ',
		3 => 'hvor en NPC leverer en nuanceret kritik, ',
		4 => 'som har taget pusten for alle tilstedeværende, ',
		5 => 'hvor Socialdemokraterne benytter lejligheden til at lancere en ny kampagne rettet mod indvandrere, ',
		6 => 'hvor OMXC20-aktieindekset slår alle tidligere rekorder, ',
		7 => 'hvor Grækenland melder sig ud af EU, ',
	];

	$event = [
		0 => 'bliver verden oversvømmet af varulve ',
		1 => 'opnår alle en større forståelse for, hvad det vil sige, at være en moderne kvinde ',
		2 => 'åbner porten til Helvede sig, og frem træder selveste Satan ',
		3 => 'opdager spillerne, at forræderen i virkeligheden er deres egen missionsofficer ',
		4 => 'bryder Christiansborg i brand. Fra stedet flygter en ungdomspolitiker fra Liberal Alliance ',
		5 => 'dukker en gigantisk protest op bestående af gluten-intolerante autonome ',
		6 => 'synes nogen pludselig, at der kommer en drage ',
		7 => 'annoncerer TV3 deres nye realityshow "Halløj i Rockerland", hvor deltagerne er tidligere rockere ',
		
	];

	$event_suffix = [
		0 => 'med maskinpistoler!',
		1 => 'med tribal-tatoveringer lavet under en vild uge på Sunny Beach.',
		2 => 'som konstant støder mod glasloftet skabt af patriarkatet.',
		3 => 'i ledtog med Cthulhu.',
		4 => 'som er begyndt at tvivle på Adam Smiths betragtninger om Den Usynlige Hånd.',
		5 => 'med en vogn fuld af guld og ædelstene.',
		6 => '(som nu giver dobbelt XP).',
		7 => 'på kokain.',

	];

	$end = [
		0 => 'Men i virkeligheden var de alle døde.',
		1 => 'Og så viser det sig, at det hele var en drøm.',
		2 => 'Eller er det...?',
		3 => 'Men noget er ikke helt, som det ser ud til at være ...',
		4 => 'Kan spillerne nå at redde verden, før det er for sent?',
		5 => 'Til sidst dukker der en overraskelse op...',
		6 => 'Men noget går galt.',
		7 => 'I virkeligheden er spillerne simulationer i et computerprogram styret af en AI.'
	];

	$gametype_prefix = [
		0 => 'Bemærk: ',
		1 => 'ADVARSEL! ',
		2 => 'Note: ',
		3 => 'Forfatteren understreger: '
	];

	$gametype = [
		0 => 'Scenariet er sat til at vare 351 timer non-stop.',
		1 => 'Scenariet foregår i fuldstændig mørke.',
		2 => 'Scenariet foregår i en skov i Finland. Der er ikke adgang til toiletter under scenariet.',
		3 => 'Spillerne skal have bind for øjnene under hele scenariet.',
		4 => 'Der er obligatorisk 3-ugers workshop før scenariet for samtlige spillere. Der gives ikke dispensation.',
		5 => 'Der vil forekomme fysisk kontakt under scenariet. Både af erotisk og af voldelig natur.',
		6 => 'Spillerne skal underskrive en NDA (non-disclosure agreement) og må intet fortælle om scenariet efter afviklingen.',
		7 => 'Scenariet foregår under vand. CMAS/PADI/BSAC-certificering eller tilsvarende er et krav.'
	];

	$players = [
		0 => 'Spillerkrav: Dungeons & Dragons-spillere med mindst 20 års erfaring.',
		1 => 'Spillertype: Folk, som ikke er bange for at tage en sjov hat på.',
		2 => 'Ikke for personer mellem 28 og 43 år!',
		3 => 'Medbring billeder fra din egen fødsel samt anbefaling fra din engelsklærer i folkeskolen. Det skal bruges i scenariet.',
		4 => 'Efter scenariet vil professionelle psykologer foretage en ansvarlig debriefing.',
		5 => 'Pris: 1.400 kr. pr. deltager (9.800 kr. hvis du ikke har eget kostume)',
		6 => 'Spillerne vil modtage spillermateriale inden afvikling. Sørg for at være hjemme efter aftale, idet vi ikke har råd til at sende fragtmanden ud flere gange.',
		7 => 'Arrangørerne tager intet ansvar for fysiske eller psykiske skader, mistede legemsdele, identitetstyveri, hovedpine, brand eller oversvømmelser. Arrangørerne tager i det hele taget intet ansvar.'
	];

	$description = 
		$start[oct($h[0])] .
		$heroes[oct($h[1])] .
		$encounter_prefix[oct($h[2])] .
		$encounter[oct($h[3])] .
		$encounter_suffix[oct($h[4])] .
		"\n\n" .
		$event_prefix[oct($h[5])] .
		$event_prefix2[oct($h[6])] .
		$event[oct($h[7])] .
		$event_suffix[oct($h[8])] .
		"\n\n" .
		$end[oct($h[9])] .
		"\n\n\n" .
		$gametype_prefix[nib($h[10])] .
		$gametype[oct($h[11])] .
		"\n\n" .
		$players[oct($h[12])] 
		
	;

	return $description;	
}

?>
