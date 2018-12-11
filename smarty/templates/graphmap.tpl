{assign var="pagetitle" value="Graf over forfattere og scenarier"}
{include file="head.tpl"}
<script src="//cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
<div id="content">

	<h2 class="pagetitle">
		Graf over forfattere og scenarier
	</h2>

	{$content}

	<div id="myrpggraphnetwork" style="width: 800px; height: 800px;"></div>

{literal}
<script type="text/javascript">
var count = 0;
function getAuthorGraph() {
	authorname = $('#authorinput').val();
	$.getJSON( "graph.js.php", { name: authorname }, function( data ) {
		aut_id = data.id;
		aut_name = data.name;
		if (!authors[aut_id]) {
			authors[aut_id] = aut_name;

			n_id = 'aut_' + aut_id;
			nodes.add({
				id: n_id,
				label: aut_name
					
			});
			$.each(data.connections, function ( sce_id, data) {
				sce_id = data.id;
				sce_title = data.title;
				ns_id = 'sce_' + sce_id;
				if (!scenarios[sce_id]) {
					scenarios[sce_id] = sce_title;
					nodes.add({
						id: ns_id,
						label: sce_title
							
					});
				}
				edgecount++;
				ne_id = 'edge_' + edgecount;
				console.log(edges.add({
					id: ne_id,
					from: n_id,
					to: ns_id
				}));
					
			});
		}
		
	});

	return false;
}

var authors = [];
var scenarios = [];
var edgecount = 0;

var nodes = new vis.DataSet();
var edges = new vis.DataSet();

// create a network
var container = document.getElementById('myrpggraphnetwork');
var data = {
	nodes: nodes,
	edges: edges
};
var options = {};
var network = new vis.Network(container, data, options);

network.on("click", function (params) {
	params.event = "[original event]";
	console.log('click event, getNodeAt returns: ' + this.getNodeAt(params.pointer.DOM));
});

</script>
{/literal}

</div>

{include file="end.tpl"}

