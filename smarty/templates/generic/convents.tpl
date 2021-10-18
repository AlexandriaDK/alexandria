{assign var="pagetitle" value="{$_cons_title}"}
{include file="head.tpl"}


<div id="content">
	<h2 class="pagetitle">
		{$_cons_list}
	</h2>

	<p class="gameslinks countryselector">
	<span class="countryselector">{$_conset_all}</span> • {foreach $countries as $countrycode}<span class="countryselector" data-countrycode="{$countrycode|escape}">{$countrycode|getCountryName|escape}</span>{if not $countrycode@last} • {/if}{/foreach}
	</p>

	<div class="con concolumns">
{foreach $cons as $conset}
		<div class="conblock" data-countries="{" "|implode:$conset.countrieslist}">
{* conset 40 is "other" *}
			<h3><a href="data?conset={$conset@key}">{if $conset@key != 40}{$conset.setname|escape}{else}{$_cons_other|escape}{/if}</a></h3>
			<ul style="display: block;">
{foreach $conset.cons as $con}
			<li data-country="{$con.country|escape}">{if $con.userloghtml}{$con.userloghtml}{/if}{con dataset=$con}</li>
{/foreach}
			</ul>
		</div>
{/foreach}

	</div>
</div>

<script>
$( "span.countryselector" ).click(function() {
	$('.conblock[data-countries]').show();
	$('.conblock ul li').show();
	if ( this.dataset.countrycode ) {
		$('.conblock[data-countries]').not( '[data-countries~="' + this.dataset.countrycode + '"]').hide();
		$('.conblock ul li[data-country]').not( '[data-country="' + this.dataset.countrycode + '"]').hide();
	}
});
</script>

{include file="end.tpl"}
