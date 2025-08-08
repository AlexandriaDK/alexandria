var count = 0;
function requestData(data) {
  aut_id = data.id;
  aut_name = data.name;
  result = data.result;
  var author_add = 0;
  if (result == "scenarios") {
    if (!g_authors[aut_id]) {
      author_add++;
      g_authors[aut_id] = aut_name;

      n_id = "aut_" + aut_id;
      nodes.add({
        id: n_id,
        label: aut_name,
        group: 0,
      });
      $.each(data.connections, function (sce_id, data) {
        sce_id = data.id;
        sce_title = data.title;
        ns_id = "sce_" + sce_id;
        if (!scenarios[sce_id]) {
          scenarios[sce_id] = sce_title;
          nodes.add({
            id: ns_id,
            label: sce_title,
            group: 1,
          });
        }
        edgecount++;
        ne_id = "edge_" + edgecount;
        edges.add({
          id: ne_id,
          from: n_id,
          to: ns_id,
        });
      });
    }
  } else if (result == "peers") {
    if (!g_authors[aut_id] || !g_authors[aut_id].peers) {
      n_id = "aut_" + aut_id;
      if (!g_authors[aut_id]) {
        author_add++;
        nodes.add({
          id: n_id,
          label: aut_name,
          group: 0,
        });
      }
      g_authors[aut_id] = { name: aut_name, peers: true };
      $.each(data.connections, function (aut_id, data) {
        aut_id = data.id;
        aut_name = data.name;
        scenarios = data.scenarios;
        na_id = "aut_" + aut_id;
        if (!g_authors[aut_id]) {
          author_add++;
          g_authors[aut_id] = aut_name;
          nodes.add({
            id: na_id,
            label: aut_name,
            group: 0,
          });
          if ($("#autoexpand").prop("checked")) {
            // expand ad infinitum - add delay?
            setTimeout(function () {
              lookup({ author_id: aut_id, action: "getPeers" });
            }, 500);
          }
        }
        edgecount++;
        ne_id = "edge_" + edgecount;
        edges.add({
          id: ne_id,
          from: n_id,
          to: na_id,
          value: scenarios,
          title: "Scenarier",
        });
      });
    }
  }
  $("#authorcount").text(Object.keys(g_authors).length);
  if (author_add > 0) {
    $("#authoraddition")
      .text("(+" + author_add + ")")
      .stop()
      .fadeIn(100)
      .fadeOut(1000);
  }
}

function getAuthorGraph() {
  authorname = $("#authorinput").val();
  lookup({ name: authorname, action: "getPeers" });
  return false;
}

function lookup(parameters) {
  $.getJSON("graph.js.php", parameters, requestData);
}

var g_authors = [];
var scenarios = [];
var edgecount = 0;

var nodes = new vis.DataSet();
var edges = new vis.DataSet();

// create a network
var container = document.getElementById("myrpggraphnetwork");
var data = {
  nodes: nodes,
  edges: edges,
};
var options = {
  interaction: {
    hideEdgesOnDrag: true,
  },
};
var network = new vis.Network(container, data, options);

network.on("click", function (params) {
  // todo: "fix click to drag bagground"
  dom = this.getNodeAt(params.pointer.DOM);
  if (dom.substring(0, 4) == "aut_") {
    aut_id = dom.substring(4);
    lookup({ author_id: aut_id, action: "getPeers" });
  } else {
    return false;
  }
});
