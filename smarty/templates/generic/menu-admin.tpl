
    {if ! isset($dberror) && ! isset($installation) }
      <div class="leftmenucontent">

      {if ! isset($user_id)}
          <span class="menulogin">

            {$_top_login}:
          </span>
          
            <a href="../login/google/" accesskey="g">[Google]</a>
            <a href="../login/steam/" accesskey="e">[Steam]</a>
            <a href="../login/discord/" accesskey="d">[Discord]</a>
          


          {else}
          {$_top_loggedonas}:<br><span title="{$user_name|escape}">{$user_name|truncate:20|escape}</span><br>
          <br>
          <div class="mylinks">
            <a href="myhistory">{$_top_myoverview}</a><br>
            {if isset($user_editor)}
              <a href="profile">{$_top_profile}</a><br>
            {/if}
            <a href="logout">{$_top_logout}</a><br>
            {if isset($user_admin)}
              <br>
              <a href="adm/" accesskey="a">{$_top_admin}</a><br>
            {elseif isset($user_editor)}
              <br>
              <a href="adm/" accesskey="a">{$_top_editor}</a><br>
            {/if}
          </div>
        {/if}
      </div>

    {/if}





{if isset($user_id)}

        {if isset($type) && $type eq "game"}
          <div class="leftmenucontent">
            {if $boardgame}{$_top_dyn_boardgame}{else}{$_top_dyn_scenario}{/if}<br><br>
            <span id="data_read">
              {if $user_read}- {$_top_read_pt} <a
                href="javascript:changedata('data_read','remove','game','{$id}','read', '{$token}')">({$_switch})</a>{/if}
              {if not $user_read}- {$_top_not_read_pt} <a
                href="javascript:changedata('data_read','add','game','{$id}','read', '{$token}')">({$_switch})</a>{/if}
            </span><br>
            {if !$boardgame}
              <span id="data_gmed">
                {if $user_gmed}- {$_top_gmed_pt} <a
                  href="javascript:changedata('data_gmed','remove','game','{$id}','gmed', '{$token}')">({$_switch})</a>{/if}
                {if not $user_gmed}- {$_top_not_gmed_pt} <a
                  href="javascript:changedata('data_gmed','add','game','{$id}','gmed', '{$token}')">({$_switch})</a>{/if}
              </span><br>
            {/if}
            <span id="data_played">
              {if $user_played}- {$_top_played_pt} <a
                href="javascript:changedata('data_played','remove','game','{$id}','played', '{$token}')">({$_switch})</a>{/if}
              {if not $user_played}- {$_top_not_played_pt} <a
                href="javascript:changedata('data_played','add','game','{$id}','played', '{$token}')">({$_switch})</a>{/if}
            </span>
          </div>

          {if $user_admin || $user_editor}
            <div class="leftmenucontent">
              {$_top_popularity}:<br><br>

              {$_top_read_pt}: {$users_entries.read + 0} {$_users}<br>
              {if ! $boardgame}
                {$_top_gmed_pt}: {$users_entries.gmed + 0} {$_users}<br>
              {/if}
              {$_top_played_pt}: {$users_entries.played + 0} {$_users}
              <br><br>
              <a href="adm/userlog.php?category=game&data_id={$id}">{$_top_details}</a>

            </div>
          {/if}


        {/if}

        {if isset($type) && $type eq "convention"}
          <div class="leftmenucontent">
            {$_top_dyn_convention}<br><br>
            <span id="data_visited">
              {if $user_visited}
                - {$_top_visited_pt} <a
                  href="javascript:changedata('data_visited','remove','convention','{$id}','visited', '{$token}')">({$_switch})</a>
              {else}
                - {$_top_not_visited_pt} <a
                  href="javascript:changedata('data_visited','add','convention','{$id}','visited', '{$token}')">({$_switch})</a>
              {/if}
            </span>

          </div>
          {if $user_admin || $user_editor}
            <div class="leftmenucontent">
              {$_top_popularity}<br><br>

              {$_top_visited_pt}: {$users_entries.visited + 0} {$_users}
              <br><br>
              <a href="adm/userlog.php?category=convention&amp;data_id={$id}">{$_top_details}</a>

            </div>
          {/if}
        {/if}

        {if $user_editor && isset($recentlog) }
          <div class="leftmenucontent">
            {$_top_recentedits}:
            <div class="longblock">
              {foreach $recentlog as $log}
                {$log.linkhtml}<br>
                <span class="noteindtast">
                  {$log.note|escape}<br>
                  {$log.pubtime}<br>
                  {$_by} {$log.user|escape}<br>
                  <br></span>
              {/foreach}
            </div>
            <a href="adm/showlog.php" accesskey="l">{$_top_alledits}</a>

          </div>
        {/if}

        {if $user_editor && isset($translations) }
          <div class="leftmenucontent">
            {$_top_translationprogress}:
            <br><br>
            {foreach $translations as $translation}
              <a
                href="adm/language.php?setlang={$translation.isocode|rawurlencode}">{$translation.llanguage|ucfirst|escape}</a>:
              {$translation.percentagestring}<br>
            {/foreach}
          </div>
        {/if}

        {if isset($user_scenario_missing_players) && $user_scenario_missing_players }
          <div class="leftmenucontent">
            {$_top_help_sce_no|@nl2br}
            <br><br>
            {foreach $user_scenario_missing_players as $usmc}
              <a href="data?scenarie={$usmc.id}" class="game">{$usmc.title|escape}</a><br>
            {/foreach}
            <br>
            {$_top_help_sce_no2|@nl2br}
          </div>
        {/if}

        {if isset($user_scenario_missing_tags) && $user_scenario_missing_tags }
          <div class="leftmenucontent">
            {$_top_help_sce_tag|@nl2br}
            <br><br>
            {foreach $user_scenario_missing_tags as $usmt}
              <a href="data?scenarie={$usmt.id}" class="game">{$usmt.title|escape}</a><br>
            {/foreach}
            <br>
            {$_top_help_sce_tag2|@nl2br}
          </div>
        {/if}

        
      {/if}
