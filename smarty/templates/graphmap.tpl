{assign var="pagetitle" value="Graf over forfattere og scenarier"}
{include file="head.tpl"}
<script src="//cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
<div id="contentwide">

	<h2 class="pagetitle">
		Graf over forfattere og scenarier
	</h2>


<form style="margin-top: 3em;" action="" method="get" onsubmit="return getAuthorGraph();">Indtast forfatter: <input type="text" name="authorinput" id="authorinput" class="tags" value="" />
</form>

	<p>Forfattere i grafen: <span id="authorcount">0</span> <span id="authoraddition" style="color: green; font-weight: bold;"></p>
	<p>Udvid automatisk (!): <input type="checkbox" id="autoexpand"></p>



	<div id="myrpggraphnetwork" style="width: 1600px; height: 1000px;"></div>


</div>

<script src="/graphmap.js"></script>

{include file="end.tpl"}

