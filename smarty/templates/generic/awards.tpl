{if ! $type_name}
  {assign var="pagetitle" value="$_award_title"}
{else}
  {assign var="pagetitle" value="$_award_title - $type_name"}
{/if}
{include file="head.tpl"}

<div id="content">

  <h2 class="pagetitle">
    {$_award_awards}
  </h2>

  {if ! $cid && ! $tid}
    <p>
      {$_award_selectconset}:
    </p>
  {else}
    <p>
      {$_award_overview1} {if $cid}<a href="data?conset={$cid}" class="con">
        {elseif $tid}<a href="data?tag={$type_name|escape}" class="tag">
        {else}<a href="/">
            {/if}{$type_name|escape}</a>.
          {$_award_overview2}
    </p>
  {/if}

  <div class="allawards">
    {$html_content}
  </div>

  {include file="updatelink.tpl"}

</div>

{include file="end.tpl"}
