{assign var="pagetitle" value="Indsend rettelser"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			Indsend rettelser:
		</h2>

		<p>
			Vi tager meget gerne imod bidrag og rettelser
			fra folk. Mange af vores informationer stammer
			fra gamle con-programmer, der ikke nødvendigvis
			afspejler de faktiske forhold. Ydermere mangler
			vi foromtaler for en del scenarier.
		</p>

		<form action="rettelser_indsend" method="post">
			<table>
			
			{$content}

				<tr><td></td><td><input type="submit" value="Indsend din rettelse" /></td></tr>
			</table>
		</form>

		<p>
			Har du behov for at vedhæfte filer eller sende større mængder rettelser, kan du i stedet e-maile
			os med den relevante information på adressen <a href="mailto:rettelser@alexandria.dk">rettelser@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
