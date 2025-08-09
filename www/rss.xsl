<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" /> 
<xsl:variable name="title" select="/rss/channel/title"/>

<xsl:template match="/">
    <html>
        <head>
            <title>XML Feed fra <xsl:value-of select="$title"/></title>
            <link rel="stylesheet" href="/rss.css" type="text/css"/>
        </head>	
        <xsl:apply-templates select="rss/channel"/>
    </html>
</xsl:template>

<xsl:template match="channel">
    <body>		
        <div id="top">
            <h1>Hvad er det her?</h1>
            <p><strong>Du er havnet p� et RSS-feed fra <em><xsl:value-of select="$title"/></em>.</strong></p>

            <p>RSS st�r for Really Simple Syndication, og bruges til at g�re det nemmere at f�lge med i flowet p� ofte opdaterede
            websites s� som fx weblogs.</p>

            <p>Teknologien fungerer p� den m�de, at et site tilbyder et feed, som best�r af sitets indhold i en XML-version, som er
            specielt indrettet til at forskellige former for nyhedsl�sere kan l�se den. Ved at s�tte en nyhedsl�ser op til med
            j�vne mellemrum at bes�ge s�dan et feed og scanne det for �ndringer, kan man f�lge med i opdateringer uden at skulle
            bes�ge sitet selv. Det s�tter en i stand til at f�lge med p� et stort antal sites p� en gang.</p>

            <p>Det, brugeren modtager i sin nyhedsl�ser, er et kort resume der linker til websitet.
            Selve nyhedsl�seren kan man som bruger enten have liggende p� sin egen computer, eller man kan benytte en
            online-nyhedsl�sere.</p>

            <p>Du kan kende RSS-feeds p� dette ikon <img src="http://www.smartlog.dk/img/xml.gif" alt="RSS ikon"/></p>
        </div>
        
        <div id="menu">
            <div id="leftmenu">
                <ul>
                    <xsl:apply-templates select="item"/>
                </ul>
            </div>

            <div id="rightmenu">
                <h3>Hvordan kan du abonnere p� dette feed?</h3>
                <p>Du kan abonnere p� RSS-feed'et p� mange m�der. Smartlog anbefaler f�lgende:</p>
                <ul>
                    <li><a href="http://www.bloglines.com/">Bloglines.com</a></li>
                    <li><a href="http://www.bradsoft.com/feeddemon/">FeedDemon</a></li>
                </ul>
            </div>

        </div>

    </body>
</xsl:template>

<xsl:template match="item">
    <li><a href="{link}" class="item"><xsl:value-of select="title"/></a></li>
</xsl:template>

</xsl:stylesheet>