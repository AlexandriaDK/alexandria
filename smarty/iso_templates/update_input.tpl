{assign var="pagetitle" value="Indsend rettelser"}
{include file="head.tpl"}

<div id="contenttext">

		<h2 class="pagetitle">
			Indsend rettelser:
		</h2>

		<p>
			Vi tager meget gerne imod bidrag og rettelser
			fra folk. Mange af vores informationer stammer
			fra gamle con-programmer, der ikke n�dvendigvis
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
			Har du behov for at vedh�fte filer eller sende st�rre m�ngder rettelser, kan du i stedet e-maile
			os med den relevante information p� adressen <a href="mailto:rettelser@alexandria.dk">rettelser@alexandria.dk</a>
		</p>	


</div>

{include file="end.tpl"}
