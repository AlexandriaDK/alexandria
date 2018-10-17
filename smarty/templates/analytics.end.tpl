{literal}
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1986951-6']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
{/literal} 

{section name=i loop=$user_achievements_to_display}
<div class="achshow" style="bottom: {($user_achievements_to_display[i].c - 1 ) * 60}px; animation-delay: {($user_achievements_to_display[i].c - 1) * 200 }ms; -webkit-animation-delay: {($user_achievements_to_display[i].c - 1) * 200 }ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#achievement_id_{$user_achievements_to_display[i].id}'">
<span class="label">{$user_achievements_to_display[i].label}</span><br />
{$user_achievements_to_display[i].description}
</div>
{/section}

{if $user_admin}
{*
<div class="achshow_bottom" style="animation-delay: 200ms; -webkit-animation-delay: 200ms;" title="Achievement unlocked!" onclick="location.href='/myhistory#'">
<span class="label">Tester</span><br />
Dette er en beskrivelses-test!
</div>
*}
{/if}

{achievements_shown}

</body>
</html>
