{assign var="pagetitle" value="{$_feeds|ucfirst}"}
{include file="head.tpl"}

<div id="content">

  <h2 class="pagetitle">
    {$_feeds|ucfirst}
  </h2>

  <p>
    {$_feeds_presentation|sprintf:'feedrss'|nl2br}
  </p>

  <table>
    <tr>
      <th>{$_feeds_title}</th>
      <th>{$_feeds_by}</th>
      <th>{$_feeds_commentsno}</th>
      <th>{$_feeds_postdate}</th>
    </tr>
    {foreach $feeddata as $feed}
      <tr>
        <td title="{$feed.title|escape}">
          <a href="{$feed.link|escape}">{if $feed.title == ""}<i>({$_feeds_notitle})</i>{else}{$feed.title|truncate:55|escape}{/if}{if $feed.podcast}
          🔊{/if}</a>
      </td>
      <td>
        {if $feed.person_id}
          <a href="data?person={$feed.person_id}" class="person">{$feed.owner|escape}</a>
        {else}
          {$feed.owner|escape}
        {/if}
      </td>
      <td style="text-align: right; padding-right: 35px;">
        {$feed.comments}
      </td>
      <td style="text-align: right;">
        {$feed.printdate|ucfirst}
      </td>
    </tr>
    {/foreach}
  </table>

  <h3>
    {$_feeds_sources}
  </h3>

  <p class="close">
    {foreach $feedlist as $feed}
      <a href="{$feed.pageurl|escape}">{$feed.owner|escape}: {$feed.name|escape}</a><br>
    {/foreach}
  </p>

  <h3>
    {$_feeds_yourblog}
  </h3>

  <p>
    {$_feeds_yourblogdetails|sprintf:'kontakt'}
  </p>
</div>

{include file="end.tpl"}
