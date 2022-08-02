<br><br>
{if isset($user_id) && $type == 'convention'}
<a href="#neworganizer" class="addorganizer"><span class="updatelinktext">{$_update_addorganizers}</span></a>
-
{/if}
{if not ($type == 'magazine' && ! $id)}
<a href="rettelser?cat={$type}&amp;data_id={$id}"><span class="updatelinktext">{$_update_submit}</span></a>
{/if}
{if isset($user_editor)}
{if not ($type == 'magazine' && ! $id)}
- 
{/if}
{if ! $id && $type == 'tag'}
<a href="adm/tag.php?tag={$tag|rawurlencode}" accesskey="r" title="Hotkey: R"><span class="updatelinktext">{$_update_edit}</span></a>
{else}
<a href="adm/redir.php?cat={$type}&amp;data_id={$id}" accesskey="r" title="Hotkey: R"><span class="updatelinktext">{$_update_edit}</span></a>
{/if}
{/if}

