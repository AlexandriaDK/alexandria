<footer class="">

{include file="menu-admin.tpl"}


<nav class="flex flex-col">

  <a href="todo">{$_top_helpalexandria}</a>
  <a href="rettelser">{$_top_submit}</a>
  <a href="findspec">{$_top_searchgame}</a>
  <a href="statistik">{$_top_statistics}</a>
  <a href="locations">{$_top_locations}</a>
  <a href="feeds">{$_top_blogfeeds}</a>
  <a href="awards">{$_top_awards}</a>
  <a href="jostspil">{$_top_jostgame}</a>
  <a href="privacy">{$_top_privacy}</a>
</nav>



{if isset($user_id)}
  {foreach $user_achievements_to_display as $achievement}
    <div class="achshow"
      style="bottom: {($achievement.c - 1 ) * 60}px; animation-delay: {($achievement.c - 1) * 200 }ms; -webkit-animation-delay: {($achievement.c - 1) * 200 }ms;"
      title="Achievement unlocked!" onclick="location.href='/myhistory#achievement_id_{$achievement.id}'">
      <span class="label">{$achievement.label}</span><br>
      {$achievement.description}
    </div>
  {/foreach}
  {achievements_shown}
{/if}

</footer>



</body>

</html>
