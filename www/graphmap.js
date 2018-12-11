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
				label: aut_name,
				group: 0
					
			});
			$.each(data.connections, function ( sce_id, data) {
				sce_id = data.id;
				sce_title = data.title;
				ns_id = 'sce_' + sce_id;
				if (!scenarios[sce_id]) {
					scenarios[sce_id] = sce_title;
					nodes.add({
						id: ns_id,
						label: sce_title,
						group: 1
							
					});
				}
				edgecount++;
				ne_id = 'edge_' + edgecount;
				edges.add({
					id: ne_id,
					from: n_id,
					to: ns_id
				});
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

