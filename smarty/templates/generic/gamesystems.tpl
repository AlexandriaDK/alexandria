{assign var="pagetitle" value="$_sys_title"}
{include file="head.tpl"}

<div id="content">

  <h2 class="pagetitle systemsselect">
    {$_sys_head}
  </h2>

  <div class="system">
    <div class="list">
      {foreach $syslist AS $id => $name}
        <a href="data?system={$id}">{$name|escape}</a><br>
      {/foreach}
    </div>
  </div>

</div>

{include file="footer.tpl"}
