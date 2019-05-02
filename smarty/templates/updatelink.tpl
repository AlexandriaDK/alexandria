<br><br>
{if $user_id && $type == 'convent'}
<a href="data?con={$id}&amp;edit=organizers#organizers"><span class="updatelinktext">Tilføj arrangører for denne kongres</span></a>
-
{/if}
<a href="rettelser?cat={$type}&amp;data_id={$id}"><span class="updatelinktext">Indsend rettelser for denne side</span></a>

{if $user_editor}
{if ! $id && $type == 'tag'}
- <a href="adm/tag.php?tag={$tag|escape}" accesskey="r" title="Hotkey: R"><span class="updatelinktext"><u>R</u>et data</span></a>
{else}
- <a href="adm/redir.php?cat={$type}&amp;data_id={$id}" accesskey="r" title="Hotkey: R"><span class="updatelinktext"><u>R</u>et data</span></a>
{/if}
{/if}

