<!DOCTYPE html>
<html lang="{$LANG|escape}">

<head>
  <title>
    {if $pagetitle != ""}{$pagetitle|escape} - {/if}Alexandria
  </title>
  <meta name="viewport" content="width=1024">
  <meta name="robots" content="index, follow" />
  {if isset($ogimage) && $ogimage != ''}
    <meta property="og:image" content="https://alexandria.dk/{$ogimage}" />
  {else}
    <meta property="og:image" content="https://alexandria.dk/gfx/alexandria_logo_og_crush.png" />
  {/if}

  <meta property="fb:admins" content="745283070">
  <link rel="stylesheet" type="text/css" href="/alexstyle__.css" />
  <link rel="stylesheet" type="text/css" href="/uistyle.css" />
  <link rel="stylesheet" type="text/css" href="/css/alex.css?2" />
  
  <link rel="alternate" type="application/rss+xml" title="Alexandria" href="https://alexandria.dk/rss.php" />
  <link rel="icon" type="image/png" href="/gfx/favicon_ti.png">
  <link rel="search" type="application/opensearchdescription+xml" title="Alexandria" href="/opensearch.xml" />
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  {if isset($URLLANG) }
    {foreach $ALEXLANGUAGES as $altlanguage => $altlanguagelocalname}
      {if $URLLANG != $altlanguage}
        <link rel="alternate" hreflang="{$altlanguage}" href="https://alexandria.dk/{$altlanguage}/{$BASEURI}" />
      {/if}
    {/foreach}
  {/if}
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="/helper.js"></script>
  {if isset($json_tags) }
    <script>
      $(function() {
        $(".newtag").autocomplete({
          source: 'ajax.php?type=tag',
          autoFocus: true,
          delay: 10
        });
      });
    </script>
  {/if}

  {if isset($todo_tabs) }
    <script>
      $(function() {
        $("#tabslist").tabs();
        $("#tabsguide").tabs();
        $("#tabsmissing").tabs();
      });
    </script>
  {/if}

  {if isset($editmode)}
    <script>
      $(function() {
        $(".peopletags").autocomplete({
          source: 'ajax.php?type=person&with_id=1',
          autoFocus: true,
          delay: 10,
          minLength: 3
        });
      });
    </script>
  {/if}

  {if isset($type) && $type == 'jostgame' }
    <script>
      $(function() {
        $(".peopletags").autocomplete({
          source: 'ajax.php?type=person',
          autoFocus: true,
          delay: 10,
          minLength: 3
        });
      });
    </script>
  {/if}
  {if isset($type) && $type == 'locations'}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css"
      integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"
      integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
      rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster/dist/MarkerCluster.Default.css" />

  {/if}
</head>
<body>



{include file="menu-top.tpl"}





  
