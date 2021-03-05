$(function() {
    initialize();
});

function initialize() {
    loadData();
    $('a[href="#persons"]').click(showPersons);
    $('a[href="#scenarios"]').click(showGames);
    $('a[href="#boardgames"]').click(showGames);
    $('a[href="#conventionsets"]').click(showConventionSets);
    $('a[href="#rpgsystems"]').click(showRPGSystems);

    $( "#search" ).submit(function( event ) {
        search( $('#searchtext').val() );
        event.preventDefault();
    });
}

function loadData() {
    loadAlexandria();
    a = [];
    license = data.license;
    access = data.access;
    categories = ['persons', 'conventions', 'systems', 'games', 'conventionsets', 'asrel', 'csrel', 'acrel', 'tag', 'tags'];
    categories.forEach(function(category) {
        a[category] = {};
        for (element of data.result[category]) {
            a[category][element.id] = element;
        }
    });

    $("#startup").hide();
    $("#menu").show();
    $("#currentdatainfo").text("Date of current data set: " + data.request.received)
    data = null; // Free up memory
}

function showContent(html) {
    $("#content").html(html);
}

function showPersons() {
    title = 'Persons';
    category = 'persons';
    datatype = 'person';
    anchor = 'person';

    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list.sort(function(a,b) {
        return (a.firstname + a.surname) > (b.firstname + b.surname);
    });
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, (element.firstname + ' ' + element.surname) );
    }
    html += '</ul>';
    showContent(html);
    $('a[href="#' + anchor + '"]').click(showPerson);
}

function showGames(data) {
    boardgames = (data.target.hash == '#boardgames');
    if (boardgames) {
        title = 'Board games';
    } else {
        title = 'Scenarios';
    }
    category = 'games'
    datatype = 'game';
    anchor = 'game';
    var list = [];
    for (var element in a[category]) { list.push(a[category][element]); }
    list = list.filter(game => game.boardgame == (boardgames ? 1 : 0) )
    list.sort(function(a,b) { return a.title > b.title; });
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, element.title );
    }
    html += '</ul>';
    showContent(html);
    $('a[href="#' + anchor + '"]').click(showGame);
}

function showConventionSets() {
    title = 'Convention sets';
    category = 'conventionsets';
    anchor = 'conventionset';
    datatype = 'conventionset';
    var list = [];

    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list.sort(function(a,b) {
        return a.name > b.name;
    });
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, element.name );
    }
    html += '</ul>';
    showContent(html);
    $('a[href="#' + anchor + '"]').click(showConventions);
}

function showConventions(data) {
    id = data.target.dataset.id;
//    showCategoryList('Conventions', 'conventions', 'convention', 'convention', 'name')
    title = 'Conventions';
    category = 'conventions';
    datatype = 'convention';
    anchor = 'convention';

    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list = list.filter(convent => convent.conset_id == id)

    list.sort(function(a,b) {
        return a.year > b.year;
    });

    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, element.name + ' (' + element.year + ')');
    }
    html += '</ul>';
    showContent(html);
    $('a[href="#' + anchor + '"]').click(showConvention);
}

function makeLink(anchor, datatype, elementid, linktext) {
    html = '<li><a href="#' + anchor + '" data-type="' + datatype + '" data-id="' + elementid + '">' + esc(linktext) + '</a></li>';
    return html;
}

function showRPGSystems() {
    showCategoryList('RPG Systems', 'systems', 'rpgsystem', 'system', 'name')
}

function showCategoryList(title, category, anchor, datatype, label) {
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list.sort(function(a,b) { 
        return a[label].toUpperCase() > b[label].toUpperCase();
    });
    for (element of list) {
        html += '<li><a href="#' + anchor + '" data-type="' + datatype + '" data-id="' + element.id + '">' + element[label] + '</a></li>';
    }
    html += '</ul>';
    showContent(html);
    $('a[href="#' + anchor + '"]').click(showSingleData);
}

function showRPGSystem(data) {
    id = data.target.dataset.id;
    s = a.systems[id];
}

function showConvention(data) {
    id = data.target.dataset.id;

}

function showPerson(data) {
    id = data.target.dataset.id;

}

function showGame(data) {
    id = data.target.dataset.id;
    game = a.games[id];
    title = game.title;
    console.log(game);
    html = '<h2>' + esc(title) + '</h2>';
    showContent(html);
    authors = a.
}

function showSingleData(data) {
    id = data.target.dataset.id;
    type = data.target.dataset.type;
    if (type == 'person') {
        showPerson(data);
    } else if ( type == 'game' ) {
        showGame(data);
    } else if ( type == 'convention' ) {
        showConvention(data);
    } else if ( type == 'system' ) {
        showRPGSystem(data);
    }
}
function esc (text) {
    return text.replace(/[\"&<>]/g, function (a) {
        return { '"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;' }[a];
    });
}

function search() {
    html = 'Search does not work yet.';
    showContent(html);
    return false;
}