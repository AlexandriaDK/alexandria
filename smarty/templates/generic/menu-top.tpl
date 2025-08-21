<header class="header flex flex-col">
<div class="flex flex-row ">
  <a href="about">{$_top_aboutalex}</a>
  <a href="kontakt">{$_top_contact}</a>

{include file="menu-lang.tpl"}


</div>

<h1 class="header__title"><a href="./" class="header__logo">Alexandria</a></h1>

<nav class="">
  
  <a href="scenarier" class="game">{$_scenarios|ucfirst}</a>
  <a href="systemer" class="system">{$_rpgsystems|ucfirst}</a>
  <a href="cons" class="con">{$_conventions|ucfirst}</a>
  <a href="calendar">{$_top_calendar}</a>
  <a href="personer" class="person">{$_persons|ucfirst}</a>
  <a href="magazines" class="magazines">{$_top_magazines|ucfirst}</a>
  <a href="tags">{$_top_tags}</a>
  <a href="boardgames" class="game">{$_boardgames|ucfirst}</a>
</nav>

{* search *}
<form action="find" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">      
  {literal}
    <meta itemprop="target" content="https://alexandria.dk/find?find={find}" />
  {/literal}

  <div class="topmenublockfind">    
    <label for="ffind" accesskey="s">{$_search|ucfirst}: 
      <input id="ffind" type="search" name="find" value="{if isset($find)}{$find|escape}{/if}" 
        size="15" class="find" itemprop="query-input" required autofocus> 
    </label>
  </div>
</form>

<div id="resultbox"></div>
{* /search *}
</header>
