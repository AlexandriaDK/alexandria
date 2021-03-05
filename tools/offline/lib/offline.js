document.getElementById('startup').style.display = 'block';

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
    categories_with_ids = ['persons', 'conventions', 'systems', 'games', 'conventionsets', 'titles', 'presentations'];
    categories_without_ids = ['person_game_title_connections', 'game_convention_presentation_connections', 'person_convention_connections', 'tags', 'gametags', 'gamedescriptions', 'files', 'sitetexts', 'links', 'trivia'];
    categories_with_ids.forEach(function(category) {
        a[category] = {};
        for (element of data.result[category]) {
            a[category][element.id] = element;
        }
    });
    categories_without_ids.forEach(function(category) {
        a[category] = data.result[category];
    });
    console.log(a.files);
    $("#startup").hide();
    $("#menu").show();
    $("#currentdatainfo").text("This data set is from the following date: " + niceDate(data.request.received))
//    data = null; // Free up memory
}

function showContent(html) {
    $("#content").html(html);
    $('a[data-category="conventionset"]').click(showConventions);
    $('a[data-category="convention"]').click(showConvention);
    $('a[data-category="game"]').click(showGame);
    $('a[data-category="person"]').click(showPerson);
    $('a[data-category="person"]').click(showRPGSystem);
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
    onlineLink('personer');
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
    onlineLink(boardgames ? 'boardgames' : 'scenarier' );
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
    onlineLink('cons');
}

function showConventions(data) {
    id = data.target.dataset.id;
    category = 'conventions';
    datatype = 'convention';
    anchor = 'convention';
    title = 'Conventions for ' + esc(a.conventionsets[id].name);

    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list = list.filter(convent => convent.conset_id == id)

    list.sort(function(a,b) {
        return a.year > b.year;
    });

    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (con of list) {
        html += '<li>' + conLink(con.id) + '</li>';
    }
    html += '</ul>';
    showContent(html);
    onlineLink('data?conset=' + id);

}

function showRPGSystems() {
    showCategoryList('RPG Systems', 'systems', 'rpgsystem', 'system', 'name');
    onlineLink('systemer');
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
        html += '<li><a href="#' + anchor + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + element.id + '">' + element[label] + '</a></li>';
    }
    html += '</ul>';
    showContent(html);
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
    person = a.persons[id];
    nom = person.firstname + ' ' + person.surname;
    birth = person.birth; // not yet exposed
    death = person.death; // not yet exposed
    games = a.person_game_title_connections.filter(rel => rel.person_id == id);
    // awards :TODO:
    organizers = a.person_convention_connections.filter(rel => rel.person_id == id);
    links = a.links.filter(rel => rel.data_id == id && rel.category == 'aut');
    trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'aut');
    html = '<h2>' + esc(nom) + '</h2>';
    if (games.length > 0) {
        html += '<h3>Games</h3>';
        html += '<table>';
        for (game of games) {
            gid = game.game_id;
            isDownloadable = a.files.find(file => file.category == 'sce' && file.data_id == gid);
            cons = a.game_convention_presentation_connections.filter(rel => rel.game_id == gid);
            html += '<tr>';
            html += '<td>' + (isDownloadable ? typeLink(gid, 'game', '💾') : '') + '</td>';
            html += '<td>' + a.titles[game.title_id].title + (game.note ? ' (' + esc(game.note) + ')' : '' ) + '</td>';
            html += '<td>' + gameLink(gid) + '</td>';
            html += '<td>';
            for (con of cons) {
                cid = con.convention_id;
                html += conLink(cid) + '<br>';
            }
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
    }

    if (organizers.length > 0) {
        html += '<h3>Organizer roles</h3>';
        html += '<table class="personorganizerroles">';
        for (organizer of organizers) {
            html += '<tr>';
            html += '<td>' + conLink(organizer.convention_id) + '</td>';
            html += '<td>' + esc(organizer.role) + '</td>';
            html += '</tr>';
        }
        html += '</table>';
    }
    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);

    showContent(html);
    onlineLink('data?person=' + id);
}

function showGame(data) {
    id = data.target.dataset.id;
    game = a.games[id];
    title = game.title;
    persons = a.person_game_title_connections.filter(rel => rel.game_id == id);
    descriptions = a.gamedescriptions.filter(rel => rel.game_id == id);
    cons = a.game_convention_presentation_connections.filter(rel => rel.game_id == id);
    // awards :TODO:
    // runs :TODO:
    // tags :TODO:
    files = a.files.filter(rel => rel.data_id == id && rel.category == 'sce');
    links = a.links.filter(rel => rel.data_id == id && rel.category == 'sce');
    trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'sce');

    html = '<h2>' + esc(title) + '</h2>';
    if (game.gms_min || game.players_min) {
        html += '<p class="indata">';
        if (game.gms_min) {
            html += game.gms_min + (game.gms_max > game.gms_min ? '-' + game.gms_max : '') + ' ' + (game.gms_min == 1 && game.gms_max <= 1 ? 'GM' : 'GMs') + (game.players_min ? ', ' : '');
        }
        if (game.players_min) {
            html += game.players_min + (game.players_max > game.players_min ? '-' + game.players_max : '') + ' ' + (game.players_min == 1 && game.players_max <= 1 ? 'player' : 'players');
        }
        if (game.participants_extra) {
            html += ', ' + esc(game.participants_extra);
        }
        html += '</p>';
    }
    if (persons.length > 0) {
        html += '<h3>By</h3>';
        html += '<ul>';
        for (element of persons) {
            pid = element.person_id;
            html += makeLink('person', 'person', element.person_id, a.persons[pid].firstname + ' ' + a.persons[pid].surname, a.titles[element.title_id].title + (element.note ? ' (' + esc(element.note) + ')' : '' ) );
        }
        html += '</ul>';
    }
    if (files.length > 0) {
        html += makeFileSection(files, id, 'sce');
    }
    if (descriptions.length > 0) {
        html += '<h3>Description</h3>';
        for (description of descriptions) {
            if (descriptions.length > 1) {
                html += '<hr>';
                html += "<h4>" + esc(description.language) + "</h4>";
            }
            html += '<p>' + esc(description.description).replace(/\n/g, '</br>');
        }
    }
    if (cons.length > 0) {
        html += '<h3>Played at</h3>';
        html += '<ul>';
        for (con of cons) {
            cid = con.convention_id;
            html += '<li>' + conLink(cid) + esc(' (' + replaceTemplateDirect(a.presentations[con.presentation_id].event_label) + ')' ) + '</li>';
        }
        html += '</ul>';
    }
    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);

    showContent(html);
    onlineLink('data?scenarie=' + id);
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
function esc (text) { // escape, replace templates and then parse [[[links]]]
    text = text.replace(/[\"&<>]/g, function (a) {
        return { '"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;' }[a];
    });
    text = replaceTemplate(text);
    text = bracketTemplate(text);
    return text;
}

function bracketTemplate(text) {
    var text = text.replace(/\[\[\[(c|s|p|cs)(\d+)\|([^\]]+)\]\]\]/g, bracketSections);
    return text;
}

function bracketSections(match, category, data_id, label) {
    if (category == 's') {
        return gameLink(data_id, label);
    } else if (category == 'p') {
        return personLink(data_id, label);
    } else if (category == 'c') {
        return conLink(data_id, label);
    } else if (category == 'cs') {
        return consetLink(data_id, label);
    }
    return match;
}

function onlineLink(parturl) {
    var url = 'https://alexandria.dk/en/' + parturl;
    $('#onlinelink').html('<a href="' + url + '">Online version of current page</a>');
}

function makeLink(anchor, datatype, elementid, linktext, optional = '') {
    var html = '<li><a href="#' + anchor + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + elementid + '">' + esc(linktext) + '</a> ' + esc(optional) + '</li>';
    return html;
}

function makeFileSection(files, id, category) {
    var html = '<h3>Download (from online archive)</h3>';
    html += '<ul>';
    for (file of files) {
        html += makeFileLink(id, category, file.filename, file.description, file.language)
    }
    html += '</ul>';
    return html;
}

function makeTriviaSection(trivia) {
    if (trivia.length == 0) {
        return '';
    }
    var html = '<h3>Trivia</h3>';
    html += '<ul>';
    for (fact of trivia) {
        html += '<li>' + esc(fact.fact) + '</li>';
    }
    html += '</ul>';
    return html;
}

function makeLinkSection(links) {
    if (links.length == 0) {
        return '';
    }
    var html = '<h3>Links</h3>';
    html += '<ul>';
    for (link of links) {
        html += '<li><a href="' + link.url + '">' + esc(link.description) + '</a></li>';
    }
    html += '</ul>';
    return html;
}

function makeFileLink(data_id, category, filename, description, language) {
    var map = {
        'sce': 'scenario',
        'convent': 'convention',
        'conset': 'conset'
    };
    var url = 'https://alexandria.dk/download/' + map[category] + '/' + data_id + '/' + encodeURIComponent(filename);
    var html = '<li><a href="' + url + '">' + esc(description) + '</a> ' + (language ? '(' + esc(language) + ')' : '') + '</li>';
    return html;
}

function typeLink(data_id, category, linktext, title = '') {
    var html = '<a href="#" class="' + category + '" title="' + esc(title) + '" data-category="' + category + '" data-id="' + data_id + '">' + esc(linktext) + '</a>';
    return html;
}

function conLink(cid, label = '') {
    var text = a.conventions[cid].name + ' (' + a.conventions[cid].year + ')';
    var begin = niceDate(a.conventions[cid].begin);
    var end = niceDate(a.conventions[cid].end);
    title = '';
    if (begin && end && (begin != end) ) {
        title = begin + ' - ' + end;
    } else if (begin) {
        title = begin;
    }
    return typeLink(cid, 'convention', (label ? label : text), title)
}

function consetLink(csid, label = '') {
    return typeLink(csid, 'conventionset', (label ? label : a.conventionset[csid].name) );
}

function gameLink(gid, label = '') {
    return typeLink(gid, 'game', (label ? label : a.games[gid].title) );
}

function personLink(pid, label = '') {
    return typeLink(pid, 'person', (label ? label : (a.person[pid].firstname + ' ' + a.person[pid].surname) ) );
}


function downloadable(game_id) {
    files = a.files.filter(rel => rel.data_id == game_id && rel.category == 'sce');
    return (files.length > 0);
}

function replaceTemplate(string) {
    return string.replace(/\{\$_(.*?)\}/g, function (capture, label) {
        return replaceTemplateDirect(label);
    });
}

function replaceTemplateDirect(label) {
    translation = a.sitetexts.filter(text => text.language == 'en' && text.label == label )
    if (translation.length) {
        return translation[0].text;
    }
    return label;
}

function search() {
    html = 'Search does not work yet.';
    showContent(html);
    return false;
}

function niceDate(isodate) {
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    var date = new Date(Date.parse(isodate));
    if (isNaN(date.getTime())) {
        return '';
    }
    return new Intl.DateTimeFormat('en-GB', options).format(date)
}