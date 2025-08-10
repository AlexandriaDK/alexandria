<div id="content">
  {include file="originalsearch.tpl"}

  <h2 class="datatitle">{$name|escape}</h2>

  {if $pic}
    <div style="float: right;">
      <a href="/gfx/system/l_{$id}.jpg">
        <img src="/gfx/system/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}"
          style="border: 1px solid black; margin: 2px;" />
      </a>
    </div>
  {/if}

  {include file="alias.tpl"}

  {if $description != ""}

    <p class="indata">
      {$description|textlinks|nl2br}
    </p>
  {/if}

  {include file="filelist.tpl"}

  {* clear for picture *}
  <div style="clear: both;">
  </div>

  {if $gamelist}
    <h3 class="parttitle">
      {$_scenarios|ucfirst}
    </h3>

    <table class="indata">
      {foreach $gamelist as $game}
        <tr>
          {if $game.userdata.html}
            <td>{$game.userdata.html.read}</td>
            <td>{$game.userdata.html.gmed}</td>
            <td>{$game.userdata.html.played}</td>
            <td style="width: 5px;"></td>
          {/if}
          <td>{if $game.game.files}<a href="data?scenarie={$game@key}" title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
          <td><a href="data?scenarie={$game@key}" class="game"
              title="{$game.game.origtitle|escape}">{$game.game.title|escape}</a></td>
          <td class="lpad">
            {foreach $game.person as $person}
              <a href="data?person={$person@key}" class="person">{$person|escape}</a><br>
            {/foreach}
          </td>
          <td>
            {foreach $game.convention AS $convention}
              {con dataset=$convention}<br>
            {/foreach}
        </tr>
      {/foreach}
    </table>
  {/if}

  {include file="articlereference.tpl"}
  {include file="trivialink.tpl"}
  {include file="updatelink.tpl"}

</div>
