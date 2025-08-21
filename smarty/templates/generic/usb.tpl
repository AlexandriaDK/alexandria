{assign var="pagetitle" value="{$_usb_title}"}
{include file="head.tpl"}

<div id="content">
  <h2 class="pagetitle">
    {$_usb_title}
  </h2>

  <div class="thumb">
    <a href="/gfx/usbdrives.jpg">
      <img src="/gfx/usbdrives_thumb.jpg" width="200" height="150" alt="USB drives with Alexandria.dk logo"
        title="USB drives">
    </a>
  </div>

  <p>
    {$_usb_description|nl2br}
  </p>

</div>

{include file="footer.tpl"}
