{assign var="pagetitle" value="Indsend rettelser"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			Indsend rettelser:
		</h2>

		<p>
			Vi tager meget gerne imod tilføjelser og rettelser
			fra folk. Mange af vores informationer stammer
			fra gamle con-programmer, og det er ikke sikkert,
			at de afspejler de faktiske forhold.
			Vi mangler også for en del scenarier.
		</p>
		<p>
			Hvis du <a href="fblogin">logger ind</a>, har du
			muligheden for selv at tilføje arrangører på
			kongresser direkte i bunden af kongres-siderne.
		</p>

{if $category == 'convent' && $label != ""}
		<p class="addorganizersyourself">
			Bemærk: Du kan <a href="/data?con={$data_id}&amp;edit=organizers#organizers">tilføje arrangører direkte</a> til
			en kongres, i stedet for at indsende en rettelse til gennemsyn. Det er lettere og hurtigere
			for alle.
		</p>
{/if}

		<form action="rettelser_indsend" method="post">
			<table>
			
			{$content}

				<tr><td></td><td><input type="submit" value="Indsend din rettelse" /></td></tr>
			</table>
		</form>

		<p>
			Har du behov for at vedhæfte filer eller sende større mængder tilføjelser og rettelser, kan du e-maile
			os med den relevante information på adressen <a href="mailto:kontakt@alexandria.dk">kontakt@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
