{include file="head.tpl"}
{if $type eq "person"}
{include file="person.tpl"}
{elseif $type eq "game"}
{include file="game.tpl"}
{elseif $type eq "gamesystem"}
{include file="gamesystem.tpl"}
{elseif $type eq "convention"}
{include file="convention.tpl"}
{elseif $type eq "conset"}
{include file="conset.tpl"}
{elseif $type eq "year"}
{include file="year.tpl"}
{elseif $type eq "tag"}
{include file="tag.tpl"}
{elseif $type eq "review"}
{include file="review.tpl"}
{/if}
{include file="end.tpl"}
