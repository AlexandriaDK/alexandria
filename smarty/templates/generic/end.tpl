{foreach from=$user_achievements_to_display item=$uatd}
<div class="achshow" style="bottom: {($uatd.c - 1 ) * 60}px; animation-delay: {($uatd.c - 1) * 200 }ms; -webkit-animation-delay: {($uatd.c - 1) * 200 }ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#achievement_id_{$uatd.id}'">
<span class="label">{$uatd.label}</span><br>
{$uatd.description}
</div>
{/foreach}
{achievements_shown}
</body>
</html>
