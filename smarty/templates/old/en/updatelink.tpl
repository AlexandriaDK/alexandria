<br><br>
{if $user_id && $type == 'convent'}
<a href="data?con={$id}&amp;edit=organizers#organizers"><span class="updatelinktext">Add organizers for this convention</span></a>
-
{/if}
<a href="rettelser?cat={$type}&amp;data_id={$id}"><span class="updatelinktext">Send corrections for this page</span></a>

{if $user_editor}
{if ! $id && $type == 'tag'}
- <a href="adm/tag.php?tag={$tag|escape}" accesskey="r" title="Hotkey: R"><span class="updatelinktext">Edit data</span></a>
{else}
- <a href="adm/redir.php?cat={$type}&amp;data_id={$id}" accesskey="r" title="Hotkey: R"><span class="updatelinktext">Edit data</span></a>
{/if}
{/if}

