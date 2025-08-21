<nav class="nav-hozintal">
  <a href="personer" class="person">{$_persons|ucfirst}</a>
  <a href="scenarier" class="game">{$_scenarios|ucfirst}</a>
  <a href="boardgames" class="game">{$_boardgames|ucfirst}</a>
  <a href="cons" class="con">{$_conventions|ucfirst}</a>
  <a href="systemer" class="system">{$_rpgsystems|ucfirst}</a>
  <a href="magazines" class="magazines">{$_top_magazines|ucfirst}</a>
</nav

      <form action="find" itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">      
        {literal}
        <meta itemprop="target" content="https://alexandria.dk/find?find={find}" />
      {/literal}

      <div class="topmenublockfind">    
        <label for="ffind" accesskey="s">{$_search|ucfirst}: <input id="ffind" type="search" name="find"
            value="{if isset($find)}{$find|escape}{/if}" size="15" class="find" itemprop="query-input" required
            autofocus>
          </label>
      </div>
  </form>
