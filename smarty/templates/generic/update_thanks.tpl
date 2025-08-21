{assign var="pagetitle" value="{$_update_thanks_title}"}
{include file="head.tpl"}

<div id="contenttext">

  <h2 class="pagetitle">
    {$_update_thanks_head}
  </h2>

  <p>
    {$_update_thanks_text|nl2br}
  </p>

</div>

{include file="footer.tpl"}
