{if isset($URLLANG) }
<nav class="">

{* {$_chooselanguage} *}

  {foreach $ALEXLANGUAGES as $altlanguage => $altlanguagelocalname}
    <a href="/{$altlanguage}/{$BASEURI}" hreflang="{$altlanguage}" title="{$altlanguagelocalname|escape}">
      {$altlanguage}
    </a>
    
    {if not $altlanguagelocalname@last} |  {/if}
  {/foreach}

</nav>
{/if}
