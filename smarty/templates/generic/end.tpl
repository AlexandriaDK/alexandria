{if isset($user_id)}
{foreach $user_achievements_to_display as $achievement}
<div class="achshow" style="bottom: {($achievement.c - 1 ) * 60}px; animation-delay: {($achievement.c - 1) * 200 }ms; -webkit-animation-delay: {($achievement.c - 1) * 200 }ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#achievement_id_{$achievement.id}'">
<span class="label">{$achievement.label}</span><br>
{$achievement.description}
</div>
{/foreach}
{achievements_shown}
{/if}
</body>
</html>
