<div id="content">
  {include file="originalsearch.tpl"}

  <h2 class="datatitle{if $cancelled} cancelled{/if}">{$name|escape} ({$year|yearname})</h2>
  <div class="arrows">
    {if $arrowset.prev.active}
      <a href="data?con={$arrowset.prev.conid}" title="{$arrowset.prev.name|escape}" rel="prev">←</a>
    {else}
      <span class="inactive">←</span>
    {/if}
    {if $arrowset.next.active}
      <a href="data?con={$arrowset.next.conid}" title="{$arrowset.next.name|escape}" rel="next">→</a>
    {else}
      <span class="inactive">→</span>
    {/if}
  </div>
  {if $pic}
    <div style="float: right;">
      <a href="/gfx/convent/l_{$id}.jpg">
        <img src="/gfx/convent/s_{$id}.jpg" alt="{$name|escape}" title="{$name|escape}"
          style="border: 1px solid black; margin: 2px;" />
      </a>
    </div>
  {/if}

  {include file="alias.tpl"}

  {if $place || $dateset || $countrycode}
    <p class="indata">
      {if $place || $countrycode}
        {$_location|ucfirst}: {if $place}{$place}{/if}{if $place && $countrycode},
        {/if}{if $countrycode}{$countrycode|getCountryNameFallback}{/if}
        {if $haslocations}<a href="locations?convention_id={$id}">🗺️</a>{/if}
        <br>
      {/if}
      {if $dateset}
        {$_date|ucfirst}: {$dateset}
      {/if}
    </p>
  {/if}

  {if $partof != ""}
    <h3 class="parttitle">{$_con_partof}: {$partof}</h3>
  {/if}

  {if $cancelled}
    <h3 class="cancelnotice">
      {$_con_cancelled|nl2br}
    </h3>
  {/if}

  {if $description != ""}
    <h3 class="parttitle">
      {$_con_about}:
    </h3>

    <p class="indata">
      {$description|textlinks|nl2br}
    </p>
  {/if}

  {include file="filelist.tpl"}

  {if $confirmed == 0}
    <p class="indata needhelp">
      {$_con_noinfo|nl2br}
      <a href="rettelser?cat=convention&amp;data_id={$id}">{$_con_sendcorrection}</a>.
    </p>
  {elseif $confirmed == 1}
    <p class="indata needhelp">
      {$_con_helpwithlist|nl2br|sprintf:'kontakt'}
    </p>
  {elseif $confirmed == 3}
    <p class="indata needhelp">
      {$_con_helpwithcontent|nl2br|sprintf:'kontakt'}
    </p>
  {/if}

  {* clear for picture *}
  <div style="clear: both;">
  </div>

  <!--
<script>
$(document).ready(function(){
  $("#filterSearch").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".conlist tr:not(.listhead)").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

<p style="text-align: right; margin: 0px; padding: 0px;"><input id="filterSearch" type="text" placeholder="🝖 Filter list"></p>
-->
  {if $gamecount > 0}

    <table class="indata conlist">

      {foreach $gamelistdata AS $gamecategory => $gamedata}
        {if ! $gamedata.games}{continue}{/if}
        <tr class="listhead">
          <td colspan="8">
            <h3 class="parttitle" style="margin: 0px; padding: 0px" id="{$gamecategory|escape}">
              {$gamedata.label|ucfirst}
            </h3>
          </td>
        </tr>
        {foreach $gamedata.games AS $game}
          <tr>
            <td>{$game.userdyn.read}</td>
            <td>{if ! $game.boardgame}{$game.userdyn.gmed}{/if}</td>
            <td>{$game.userdyn.played}</td>
            <td style="width: 10px;"></td>
            <td>{if $game.filescount}<a href="data?scenarie={$game.id}" alt="Download"
                title="{$_sce_downloadable|escape}">💾</a>{/if}</td>
            <td>{$game.runsymbol}</td>
            <td><a href="data?scenarie={$game.id}" class="game">{$game.title|escape}</a></td>
            <td style="padding-left: 10px">{$game.personhtml}{if $game.personextracount}<br><span
                  onclick="this.nextSibling.style.display='inline';this.style.display='none';" class="moreauthors"
                  title="{$game.personextracount} {$_con_morepersons}">[…]</span><span
                class="authorlistextra">{$game.personextrahtml}{/if}</td>
            <td style="padding-left: 10px">{if $game.system_id}<a href="data?system={$game.system_id}"
                  class="system">{$game.system_translation}</a>{if $game.system_extra}
                {$game.system_extra|escape}{/if}
                {elseif $game.system_extra}{$game.system_extra|escape}
                {/if}</td>
            {/foreach}
          {/foreach}
      </table>
    {/if}

    {if $award}
      <h3 id="awards">{$_con_awards}</h3>
      {$award}
    {/if}

    <h3 class="parttitle{if ! $organizerlist && ! $editorganizers} organizerhidden{/if}" id="organizers">
      {$_organizers|ucfirst}</h3>
    <table class="indata">
      {foreach $organizerlist as $organizer}
        <tr>
          <td style="padding-right: 10px">
            {$organizer.role|escape}
          </td>
          <td>
            {if $organizer.person_id}
              <a href="data?person={$organizer.person_id}" class="person">{$organizer.name|escape}</a>
            {else}
              {$organizer.person_extra|escape}
            {/if}
          </td>
          <td style="text-align: center;">
            {foreach $user_can_edit_organizers AS $pcrel_id => $true}
              {if $organizer.id == $pcrel_id}
                <a
                  href="adm/user_organizers.php?convention={$id}&amp;pcrel_id={$pcrel_id}&amp;action=delete&amp;token={$token}">[{$_remove}]</a>
                {break}
              {/if}
            {/foreach}
          </td>
        </tr>
      {/foreach}

      {if $user_id}
        <form action="adm/user_organizers.php" method="post">
          <input type="hidden" name="convention" value="{$id}">
          <input type="hidden" name="token" value="{$token}">
          <input type="hidden" name="action" value="add">
          <tr style="vertical-align: top" {if ! $editorganizers}class="organizerhidden" {/if}>
            <td style="padding-bottom: 250px">
              <input type="text" name="role" id="neworganizer" placeholder="{$_con_organizerrole|escape}" autofocus>
            </td>
            <td>
              <input type="text" name="person_text" value="" placeholder="{$_name|escape}" class="peopletags"
                style="width: 250px;">
            </td>
            <td>
              <input type="submit" value="{$_add|escape}">
            </td>
          </tr>
        </form>
      {/if}
    </table>

    {if $organizerlist && isset($user_id)}
      <p class="addorganizersyourself">
        <a href="#neworganizer">{$_con_addorganizers}</a>
      </p>
    {/if}

    {include file="articlereference.tpl"}
    {include file="trivialink.tpl"}
    {include file="internal.tpl"}
    {include file="updatelink.tpl"}

  </div>
