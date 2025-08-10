{assign var="pagetitle" value="$_ps_title"}
{include file="head.tpl"}

<div id="content">

  <h2 class="pagetitle">
    {$_ps_people}
  </h2>

  <p class="personslinks"><a href="personer?b={$b|rawurlencode}&amp;s=f">{$_ps_sortbyfirst}</a> &nbsp; <a
      href="personer?b={$b|rawurlencode}">{$_ps_sortbysur}</a>
    <br><br>
    {$chars}
  </p>

  <h2>{$b|mb_strtoupper}</h2>

  <div class="person">
    <div class="list">{$list}</div>
  </div>

</div>

{include file="end.tpl"}
