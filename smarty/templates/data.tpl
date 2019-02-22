{if $user_admin}
{include file="head.tpl"}
{else}
{include file="head.tpl"}
{/if}
{if $type eq "aut"}
{assign var="typename" value="Person"}
{include file="person.tpl"}
{elseif $type eq "sce"}
{if $boardgame}
{assign var="typename" value="Brætspil"}
{else}
{assign var="typename" value="Scenarie"}
{/if}
{include file="scenario.tpl"}
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
{/if}
{include file="end.tpl"}
