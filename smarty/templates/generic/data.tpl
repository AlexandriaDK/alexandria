{include file="head.tpl"}
{if $type eq "aut"}
{assign var="typename" value="Person"}
{include file="person.tpl"}
{elseif isset($type2) && $type2 eq "game"}
{if $boardgame}
{assign var="typename" value="Brætspil"}
{else}
{assign var="typename" value="Scenarie"}
{/if}
{include file="game.tpl"}
{elseif $type eq "sys"}
{assign var="typename" value="System"}
{include file="system.tpl"}
{elseif $type eq "convent"}
{assign var="typename" value="Kongres"}
{include file="convent.tpl"}
{elseif $type eq "conset"}
{assign var="typename" value="Kongres-serie"}
{include file="conset.tpl"}
{elseif $type eq "year"}
{assign var="typename" value="År"}
{include file="year.tpl"}
{elseif $type eq "tag"}
{assign var="typename" value="Tag"}
{include file="tag.tpl"}
{elseif $type eq "review"}
{assign var="typename" value="Review"}
{include file="review.tpl"}
{/if}
{include file="end.tpl"}
