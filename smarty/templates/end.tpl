{section name=i loop=$user_achievements_to_display}
<div class="achshow" style="bottom: {($user_achievements_to_display[i].c - 1 ) * 60}px; animation-delay: {($user_achievements_to_display[i].c - 1) * 200 }ms; -webkit-animation-delay: {($user_achievements_to_display[i].c - 1) * 200 }ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#achievement_id_{$user_achievements_to_display[i].id}'">
<span class="label">{$user_achievements_to_display[i].label}</span><br>
{$user_achievements_to_display[i].description}
</div>
{/section}

{if $user_admin}
{*
<div class="achshow_bottom" style="animation-delay: 200ms; -webkit-animation-delay: 200ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#'">
<span class="label">Tester</span><br>
Dette er en beskrivelses-test!
</div>
*}
{/if}

{achievements_shown}

</body>
</html>
