<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Main page</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/uistyle.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script>
function getStats() {
	$( "#frontstat" ).load( "frontstat.php" );
}
</script>
</head>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000" onload="getStats()">

<?php
include("links.inc");
printinfo();
?>

<div style="font-family: Verdana, Tahoma; font-size: 12px; width: 600px;">

<p style="font-style: italic;">Translator? <a href="language.php">Over here!</a></p>

<p>
	You are logged in as: <b><?php print htmlspecialchars($authuser); ?></b>.
</p>

<p>
	Welcome to the editor section of Alexandria. Feel free to join <a href="https://www.facebook.com/groups/1602088646679278/">our Facebook group</a>.
</p>

<h3>
	Active users
</h3>

<div id="frontstat">Henter ...</div>

<script>

</script>


<h3>
	Oversigt
</h3>

<p style="width: 600px;">
	Her finder du information om datamodellen, samt de enkelte administrations-punkter:
	<ul>
		<li><a href="#datamodel">Datamodellen</a></li>
		<li><a href="#navigation">Navigation og redigering</a></li>
		<li>Menupunkter:
			<ul>
				<li><a href="#personer">Personer</a></li>
				<li><a href="#scenarier">Scenarier</a></li>
				<li><a href="#con">Con</a></li>
				<li><a href="#conserie">Con-serie</a></li>
				<li><a href="#system">System</a></li>
				<li><a href="#teknik">Teknik</a></li>
				<li><a href="#tickets">Tickets</a></li>
			</ul>
		</li>
		<li><a href="#tips">Tips &amp; tricks</a></li>
	</ul>
</p>

<h3 id="datamodel">
	Datamodellen
</h3>

<p>
	For at sørge for at der ikke kommer rod i datamodellen, er det nødvendigt at nævne en række
	retningslinjer, samt fortælle om systemets fleksibilitet og begrænsninger. Dette er måske lidt
	kedelig læsning, men nødvendigt for at forhindre at forskellige folks data-bidrag gør mere skade
	end gavn.
</p>

<p>
	Alexandria-databasen består bl.a. af "mange-til-mange"-relationer. Det betyder i korte træk, at
	en person fx kan have skrevet flere scenarier, men samtidig også at et scenarie kan være skrevet af
	flere personer. Endnu et eksempel på dette er, at en con kan rumme flere scenarier, men at et scenarie
	kan være spillet på flere con'er (ifbm. reruns og lignende).
</p>

<p>
	Formålet med at have en så opdelt datamodel fremfor blot en simpel tabel med scenarier i,
	er at give mulighed for at have selvstændig information for fx personer og con'er. Havde
	man kun en scenarie-tabel, skulle hver con (og for den sags skyld start- og slut-datoen for
	con'en) stå for hvert enkelt scenarie. Skulle man efterfølgende rette i person-data'en,
	eksempelvis rette en stavefejl, tilføje en fødselsdato, etc., ville dette
	skulle rettes mange steder, fremfor bare ét centralt sted. Der er mange andre
	tekniske fordele ved at lave en datamodel på denne måde, men
	det rækker ud over denne vejledning at gå i dybden med disse.
</p>

<p>
	Modellen kan omtrent fremstilles på følgende måde:
</p>

<table cellspacing="1" cellpadding="1" style="font-size: 14px; border: 1px solid black; padding: 2px 2px 2px 2px;">

	<tr>
		<th title="Fx &quot;Peter Brodersen&quot;, &quot;Palle Schmidt&quot;">Person</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<td style="font-size: 10px" colspan=3>(mange-til-mange-relation)</td>
		<th>⇔</th>
		<th align="left" title="Fx &quot;Forfatter&quot;, &quot;Illustrator&quot;, &quot;Layouter&quot;">Stilling</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<th title="Fx &quot;Dødens Skygge&quot;, &quot;Dogme#1 - Pesten&quot;, &quot;De Professionelle&quot;">Scenarie</th>
		<th>⇔</th>
		<th align="left" title="Fx &quot;AD&amp;D&quot;, &quot;Paranoia&quot;, &quot;GURPS&quot;">System</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<td style="font-size: 10px" colspan=3>(mange-til-mange-relation)</td>
		<th>⇔</th>
		<th align="left" title="Fx &quot;Premiere&quot;, &quot;Re-run&quot;, &quot;Aflyst&quot;">Præsentation</th>
	</tr>

	<tr>
		<th>⇕</th>
	</tr>

	<tr>
		<th title="Fx &quot;Fastaval 1996&quot;, &quot;Viking Con 20&quot;">Con</th>
		<th>⇔</th>
		<th align="left" title="fx &quot;Fastaval&quot;, &quot;Viking Con&quot;">Con-serie</th>
	</tr>

</table>		

<p>
	En <b>person</b> kan således have medvirkende til flere scenarier, både som
	illustrator på ét scenarie og forfatter på et andet (kaldet <b>stilling</b>).
	Således er en person ikke overordnet stemplet som fx "Illustrator".
</p>

<p>
	Et <b>scenarie</b> kan altså have flere personer bag sig. Til gengæld kan et
	system af praktiske årsager kun have ét <b>system</b> tilknyttet. Et scenarie
	kan således ikke både være AD&amp;D og Warhammer Fantasy Roleplay. I de meget få
	tilfælde hvor det alligevel er tilfældet, må man blot angive det som en note/trivia til
	scenariet.
</p>

<p>
	En <b>con</b> kan rumme flere scenarier - også scenarier, der allerede er tilknyttet andre
	con'er. I forbindelsen mellem scenarie og con angives det også hvilken form for
	<b>præsentation</b>, der var tale om, fx om det er premiere (første gang, scenariet afvikles),
	et re-run eller om scenariet til lige præcis denne con blev aflyst. Således er et scenarie ikke
	overordnet set markeret som "Aflyst", idet et scenarie kan være aflyst til én con og
	efterfølgende spillet til en anden.
</p>

<p>
	 En <b>con-serie</b> er en række con'er, der hører under samme navn eller arrangørfortegnelse.
	 Hver con kan kun være med i én serie - fx er con'erne "Fastaval 1990", "Fastaval 1996", etc.
	 alle del af con-serien "Fastaval" (og kun denne).
</p>

<p>
	Et eksempel på brug af vores datamodel er, at hvis vi skal finde ud af hvilke personer,
	der nogensinde har skrevet til en bestemt con-serie, fx Fastaval, skal vi blot kigge på
	tegningen og se hvordan vi kommer fra "person" til "con-serie".
	Her skal vi bevæge os fra personer, over scenarier, over con'er, til con-serien. Eller sagt
	på en anden måde: Vi skal finde de personer, der har skrevet scenarier, der har været spillet
	på con'er, der er en del af Fastaval.
</p>

<h3 id="navigation">
	Navigation og redigering
</h3>

<p>
	Øverst på hver side er der menupunkter til at bladre imellem de forskellige kategorier.
</p>

<p>
	For at gøre datamodellen mere overskuelig og lettere at rette i, er de fleste sammenknytninger
	flyttet ind under <b>Scenarier</b>. For hvert scenarie er der her mulighed for at angive
	en liste af con'er og personer, der er tilknyttet.
</p>

<p>
	Den letteste måde at finde scenarier og personer på, er ved at bruge "Kvik-find"-feltet, som
	du i øvrigt i de fleste browsere kan vælge, blot ved at trykke <b>Alt-k</b> på tastaturet.
	"Kvik-find"-feltet virker ved at man blot indtaster en del af personens navn eller scenariets
	titel, og trykker return. Man vil nu få en liste over søgeresultater, eller evt. blive smidt
	direkte hen på den rigtige post, hvis man har været præcis nok i sin indtastning.
</p>

<p>
	For et scenarie vil man få en liste over eksisterende personer, con'er og systemer. For at
	oprette eller rette et scenarie, er det således en forudsætning, at de personer, der er medvirkende,
	er oprettet i forvejen. Det samme gælder de con'er, scenariet er tilknyttet.
</p>

<p>
	For en con vil man tilsvarende få en liste over eksisterende con-serier. For at
	tilføje en con til en con-serie, er det ligeledes en forudsætning, at con-serien er oprettet
	i forvejen.
</p>

<p>
	Under enhver kategori (med undtagelse af "Scenarier") vil der i bunden være en liste over
	alle poster i denne kategori, hvorfra man også kan vælge den post, man vil gå ind på.
</p>

<h3 id="personer">
	Personer
</h3>

<p>
	<b>Definition:</b> En "person" er ét selvstændigt individ. Følgende betegnes <em>ikke</em>
	som personer:
	<ul>
		<li>pseudonym/alias (fx "El Prez", "Anonym")</li>
		<li>gruppe af personer (fx "Albertslund Ungdomsskole", "Dogme-kollektivet", "Natural Born Holmers")</li>
	</ul>
	
	<b>Felter:</b>
	<ul>
		<li><b>Navn</b><br>
			Personens rigtige navn og evt. også mellemnavne, hvis disse er kendt.
		</li>
		<li><b>Intern note</b><br>
			Evt. intern data til administrativ brug - fx
			dokumentation af stavemåder og andre relevante detaljer
			til øvrige administratorer. Disse noter vil ikke
			fremgå på den offentlige del af Alexandria. Skriv evt.
			initialer, hvis du indtaster spørgsmål eller svar.
		</li>
		<li><b>Fødselsdato</b><br>
			Personens fødselsdato (år, måned, dag), hvis
			denne kendes, og personen i øvrigt ikke har noget
			imod at have denne offentliggjort. Ellers bør feltet
			efterlades blankt.
		</li>
		<li><b>RPGForum-ID</b><br>
			Såfremt brugeren er oprettet på RPGForum, vil man
			på hans egen profil her i adresselinjen kunne finde
			id-nummeret, fx "...&amp;id=18". I dette tilfælde indtastes
			"18". Denne information er relevant i forbindelse med en
			evt. senere dataudveksling med RPGForum.
		</li>
		<li><b>Billede</b><br>
			Filnavn på profilbillede - kun for administratorer,
			der har direkte adgang til serveren. Der er ikke
			mulighed for at uploade billeder eller lignende.
		</li>
	</ul>
</p>

<h3 id="scenarier">
	Scenarier
</h3>

<p>
	<b>Definition:</b> Et "scenarie" er ét selvstændigt, offentligt spillet, tidsbegrænset
		scenarie. Dette inkluderer almindelige con-scenarier, con-live-scenarier, selvstændige
		live-scenarier.

	<b>Felter:</b>
	<ul>
		<li><b>Titel</b><br>
			Scenariets titel. Ved meget lange titler dog blot en del af den,
			eller evt. en brugt forkortelse. Ved fortsættelser med eget navn,
			bør den fælles titel kun stå først, hvis pladsen byder det (fx "Dogme #1: Pesten").
			Hvis titlen blot er systemangivelsen, fx "Vampire" eller "AD&D-turnering",
			bør man lave en titel med angivelse af premierecon'en, fx
			"AD&D (Spiltræf 90)".
		</li>
		<li><b>Foromtale</b><br>
			Såvidt muligt scenariets/forfatterens oprindelige foromtale. Evt.
			blot en del af den (dog markeret med fx ".." for at indikere, at
			ikke hele foromtalen er tastet ind, og resten skal tastes ind
			på et senere tidspunkt).
		</li>
		<li><b>Intern note</b><br>
			Evt. intern data til administrativ brug. For eksempel
			relevante informationer i forbindelse med databehandling
			og evt. fremtidig udvidelse af datamodellen (fx genrer,
			m.m.). Skriv evt. initialer, hvis du indtaster spørgsmål
			eller svar.
		</li>
		<li><b>System</b> og <b>systemnote</b><br>
			Systemet, scenariet er skrevet til. Her er en liste over
			eksisterende systemer. Der kan tillige indtastes bi-data
			til systemet under systemnote, fx "2nd edition",
			"Forgotten Realms" og "11.-15. level". Er systemet ukendt,
			kan man nøjes med at indtaste system-navnet under systemnote
			og lade system forblive "[Ukendt eller uspecificeret system]".
			Findes systemet, skal det dog vælges fra system-listen i stedet
			for at blive tastet ind under systemnote. Bemærk i øvrigt,
			at "Systemløst" i denne model også er system. Systemløse scenarier
			skal altså sættes til systemet "Systemløst" fremfor "[Ukendt eller
			uspecificeret system]".
		</li>
		<li><b>Con</b><br>
			De con's, scenariet har været spillet til. På venstresiden er en liste
			over de con's, der er knyttet til scenariet, og på højresiden er en
			liste over alle con's. Man tilknytter en con ved at vælge den fra højresiden,
			og derefter trykke på en passende knap for under hvilke omstændigheder,
			scenariet blev spillet på den con (fx "Premiere"). En con fjernes fra et scenarie
			ved at vælge con'en i venstre side, og så vælge "Fjern".
		</li>
		<li><b>Af</b><br>
			De medvirkende personer til scenariet. På venstresiden er en liste
			over de personer, der er knyttet til scenariet, og på højresiden er en
			liste over alle personer. Man tilknytter en person ved at vælge ham/hende
			fra højresiden, og derefter trykke på den knap, der omhandler personens
			tilknytning til scenariet (fx "Forfatter" eller "Illustrator"). En person
			kun være listet én gang under et scenarie, under den "højeste" stilling.
			Har en person fx både skrevet og illustreret et scenarie, skal
			han kun optræde som forfatter. En person fjernes fra et scenarie ved at vælge
			personen i venstre side, og så vælge "Fjern".
		</li>
		<li><b>Evt. arrangør</b><br>
			Nogle scenarier præsenteres under et fællesnavn eller en forening, bl.a. en del
			live-scenarier. Her er det muligt at angive et arrangør-navn, evt. som alternativ
			til at angive specifikke personer, uden at det er nødvendigt at katalogisere
			arrangør-navnet noget sted.
		</li>
		<li><b>Status</b><br>
			Mulighed for at skjule enkelte entries på de offentlige sider, hvis det af en
			eller anden årsag pludselig skulle blive nødvendig. Funktionaliteten er udelukkende
			med som sikkerhedsventil, så medmindre, der er en meget god grund, bør man
			altid vælge "Offentlig data".
		</li>

	</ul>
</p>

<h3 id="con">
	Con
</h3>

<p>
	<b>Definition:</b> En "con" er ét planlagt, selvstændigt, åbent arrangement
		med fokus på rollespil. Dette inkluderer de fleste fler-dages-arrangmenter,
		men <em>ikke</em>:
	<ul>
		<li>Klubbers arrangmenter, primært/kun for daglige medlemmer (fx "TRC's hyttetur 1994")</li>
		<li>Tilfældige overordnede arrangementer (fx biblioteksarrangementer, "Åbent hus i fritidscenteret")</li>
		<li>Små, vilkårlige con'er (typisk under 20-30 personer med ukendte scenarier og ukendte folk)</li>
	</ul>

	<b>Felter:</b>
	<ul>
		<li><b>Navn</b><br>
			Con'ens navn uden årstal. Flere con'er kan sagtens have det samme navn, fx blot
			"Fastaval".
		</li>
		<li><b>Årstal</b><br>
			Fire-cifret årstal for con'ens afviklingstidspunkt.
		</li>
		<li><b>Startdato</b><br>
			Præcis dato for con'ens starttidspunkt, hvis kendt. Indtastes i formatet ÅÅÅÅ-MM-DD, fx
			"2002-04-01" for "1. april 2002". Efter indtastning vil ugedagen tillige
			fremgå til højre for indtastningsfeltet.
		</li>
		<li><b>Slutdato</b><br>
			Præcis dato for con'ens sluttidspunkt, hvis kendt. Indtastes i formatet ÅÅÅÅ-MM-DD.
			Efter indtastning vil ugedagen tillige fremgå til højre for indtastningsfeltet.
		</li>
		<li><b>Sted</b><br>
			Navn på sted for con'en inkl. bynavn - fx "Katrinebjergskolen, Århus".
		</li>
		<li><b>Info om connen</b><br>
			Diverse objektiv information om arrangører, gæster, evt. indgangspris, større begivenheder på con'en
			(fx helcon live), m.m.
		</li>
		<li><b>Con-serie</b><br>
			Navnet på den serie, con'en er en del af. Enhver con bør være del af en con-serie,
			også selvom der kun er én con i denne serie.
		</li>
		<li><b>Datavaliditet</b><br>
			Status på indsamling af data for den aktuelle con. Vil som udgangspunkt være "Scenarieliste mangler",
			kan sættes til "Scenarieliste under indtastning", hvis man har erhvervet sig det aktuelle
			con-program, og "Scenarieliste komplet jf. program", hvis alle scenarier fra programmet er lagt
			ind i databasen (evt. uden foromtaler).
		</li>

	</ul>
</p>

<h3 id="conserie">
	Con-serie
</h3>

<p>
	<b>Definition:</b> En "con-serie" er en gruppering af en række con'er, som har samme
		arrangerfortegnelse, eller er arrangeret som efterfølgere til hinanden.
	<b>Felter:</b>
	<ul>
		<li><b>Navn</b><br>
			Det overordnede navn for alle con'er i denne serie, typisk baseret på et gennemgående
			con-navn (fx "Fastaval") eller alternativt arrangørgruppen (fx "Con II Crew").
		</li>
		<li><b>Info om con-serie</b><br>
			Diverse objektiv information om con-serien som helhed.
		</li>

	</ul>

</p>

<h3 id="system">
	System
</h3>

<p>
	(information om system-siden er endnu ikke skrevet)
</p>

<h3 id="teknik">
	Teknik
</h3>

<p>
	Teknik-siden er udelukkende en informativ side, der oplyser om diverse
	statistik, samt database-mæssige uregelmæssigheder i systemet. Dette inkluderer
	blandt andet en liste over personer i systemet, der ikke er tilknyttet noget scenarie,
	con's uden nogen kendt startdato, m.m. Siden giver blandt andet en idé om evt. uklarheder,
	der gerne må følges op på og undersøges nærmere.
</p>

<h3 id="tickets">
	Tickets
</h3>

<p>
	Ticket-systemet er det organiserede feedback-system, når folk indsender rettelser.
	Her er der mulighed for løbende at se åbne sager, skrive kommentarer undervejs (da
	en del feedback kræver afklarende spørgsmål), og så fremdeles.
</p>

<h3 id="tips">
	Tips &amp; tricks
</h3>

<p>
	<ul>
		<li>Brug "Kvik-find"-feltet! Det sparer dig for meget tid ifbm.
			navigation i admin-systemet. Husk, at du også kan trykke
			Alt-k (under Windows) for at flytte markøren ind i
			"Kvik-find"-feltet.</li>
		<li>Før du opretter ny data, så vær sikker på at personen,
			scenariet, con'en, etc. ikke findes i forvejen - evt. blot
			under et andet navn. Søg først efter navnet eller titlen på
			det, du er ved at oprette.</li>
		<li>Husk at folk kan have mellemnavne. Så selvom "Morten Jaeger"
			fx ikke umiddelbart findes i systemet, viser en søgning på fx
			"Morten" eller lidt bladren i personlisten i bunden på "Personer"-siden,
			at "Morten Trøst Jaeger" faktisk findes, og således ikke skal
			(gen)oprettes.</li>
		<li>Er du ved at indtaste et helt con-program, kan du gå ind under den
			relevante con på con-siden, og klikke på linket "Brug con som default con".
			Når denne er valgt, vil systemet, når du er ved at oprette nye scenarier,
			automatisk vælge denne con i con-listen for dig.</li>
		<li>Du kan i de fleste indtastningsfelter lave links til andre data. Det gøres
			blot ved at omramme den relevante tekst med klammer, fx: [[[Peter Brodersen]]].
			På den offentlige side vil teksten så være blevet til et link,
			fx: <a href="../find?find=Peter%20Brodersen">Peter Brodersen</a></li>
		<li>Det er endnu ikke muligt at slette data. Det skyldes at der
			vil være for stor risiko for fx at slette et scenarie, der eksempelvis
			har en masse personer og con'er tilknyttet. Har du vitterligt behov
			for at få noget slettet, så se næste punkt.
		<li>Mangler du en eller anden triviel funktionalitet? Så tøv ikke med
			at kontakte Peter Brodersen på adressen <a href="mailto:peter@ter.dk">peter@ter.dk</a>
			eller smid en kommentar i <a href="https://www.facebook.com/groups/1602088646679278/">Facebook-gruppen</a>.
		<li>Kviklink til at gå et skridt bagud: <a href="../" accesskey="q">Hotkey+Q</a>.</li>
	</ul>
</p>


</div>


</body>
</html>
