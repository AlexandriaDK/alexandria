{assign var="pagetitle" value="{$_my_title}"}
{include file="head.tpl"}

<div id="contentwide">

  <h2 class="pagetitle">
    {$_my_title}
  </h2>

  <div id="kongres" class="mylist">
    {if not $content_myconvents}
      <h3 class="parttitle">{$_conventions|ucfirst}</h3>
      <p>
        {$_my_nocons}
      </p>
    {else}
      <h3 class="parttitle">{$_conventions|ucfirst} ({$con_count})</h3>
      <table>
        <tr>
          <td></td>
          <td><a href="myhistory?o=5">{$_conname|ucfirst}</a></td>
          <td><a href="myhistory?o=7">{$_series|ucfirst}</a></td>
          <td><a href="myhistory?o=6">{$_year|ucfirst}</a></td>
        </tr>
        {$content_myconvents}
      </table>
    {/if}
  </div>

  <div id="scenarier" class="mylist">
    {if not $content_myscenarios}
      <h3 class="parttitle">{$_games|ucfirst}</h3>
      <p>
        {$_my_nogames}
      </p>
    {else}
      <div style="float: left;">
        <h3 class="parttitle">{$_games|ucfirst} ({$game_count}: <span title="{$_top_read_pt}">{$game_read}</span>/<span
            title="{$_top_gmed_pt}">{$game_gmed}</span>/<span title="{$_top_played_pt}">{$game_played}</span>)</h3>
        <table>
          <tr>
            <td><a href="myhistory?o=1">{$_title|ucfirst}</a></td>
            <td><a href="myhistory?o=2">{$_top_read_pt}</a></td>
            <td><a href="myhistory?o=3">{$_top_gmed_pt}</a></td>
            <td><a href="myhistory?o=4">{$_top_played_pt}</a></td>
          </tr>
          {$content_myscenarios}
        </table>
      </div>
    {/if}
  </div>

  <div id="achievements" class="mylist">
    <h3>{$_achievements|ucfirst} ({$achievement_count})</h3>
    {$content_personal_achievements}
  </div>

</div>

{include file="footer.tpl"}
