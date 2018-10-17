{if $type eq "aut"}
{assign var="typename" value="Person"}
{include file="head.tpl"}
{include file="person.tpl"}
{include file="end.tpl"}
{elseif $type eq "sce"}
{assign var="typename" value="Scenarie"}
{include file="head.tpl"}
{include file="scenario.tpl"}
{include file="end.tpl"}
{elseif $type eq "sys"}
{assign var="typename" value="System"}
{include file="head.tpl"}
{include file="system.tpl"}
{include file="end.tpl"}
{elseif $type eq "convent"}
{assign var="typename" value="Kongres"}
{include file="head.tpl"}
{include file="convent.tpl"}
{include file="end.tpl"}
{elseif $type eq "conset"}
{assign var="typename" value="Kongres-serie"}
{include file="head.tpl"}
{include file="conset.tpl"}
{include file="end.tpl"}
{elseif $type eq "year"}
{assign var="typename" value="År"}
{include file="head.tpl"}
{include file="year.tpl"}
{include file="end.tpl"}
{/if}
