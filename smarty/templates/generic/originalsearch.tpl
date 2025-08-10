{if isset($searchterm)}
  <div class="originalsearch">{$_find_originalsearch} <a
      href="find?find={$searchterm|rawurlencode}&redirect=no">{$searchterm|truncate:30|escape}</a>?</div>
{/if}
