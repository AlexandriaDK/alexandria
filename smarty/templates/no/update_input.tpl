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
			
{if $label}
			<tr><td>
			<input type="hidden" name="cat" value="{$category|escape}" >
			<input type="hidden" name="data_id" value="{$data_id|escape}" >
			Indsend rettelse for:</td>
			<td class="correctionlabel">{$label}</td></tr>
{else}
			<tr><td>Indtast navn eller titel:</td><td><input type="text" name="data_label" size="30" maxlength="250"><br><span class="noteindtast">Fx "Oculus Tertius" eller "Spiltræf XII"</span></td></tr>
{/if}
			<tr><td>Indtast din rettelse eller tilføjelse:</td><td><textarea name="data_description" cols="30" rows="8"></textarea></td></tr>
			<tr><td>Dit navn?</td><td><input type="text" name="user_name" size="30" value="{$user_name|escape}"></td></tr>
			<tr><td>Din e-mail-adresse?</td><td><input type="email" name="user_email" size="30"><br><span class="noteindtast">Vi skriver kun til dig, hvis vi har evt. spørgsmål</span></td></tr>
			<tr><td>Hvad er din kilde?</td><td><textarea name="user_source" cols="30" rows="3"></textarea><br><span class="noteindtast">Angiv evt. en URL, et con-program, "mig selv", "fra hukommelsen" eller lignende</span></td></tr>
			<tr><td>Indtast bogstavet <b>A</b>:<br>(for spamsikring)</td><td><input type="text" name="human" value="" size="3"></td></tr>

			<tr><td></td><td><input type="submit" value="Indsend din rettelse"></td></tr>
			</table>
		</form>

		<p>
			Har du behov for at vedhæfte filer eller sende større mængder tilføjelser og rettelser, kan du e-maile
			os med den relevante information på adressen <a href="mailto:kontakt@alexandria.dk">kontakt@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
