document.getElementById('startup').style.display = 'block';

var sortcache = [];

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
    $('a[href="#tags"]').click(showTags);
    $('a[href="#magazines"]').click(showMagazines);

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
    categories_without_ids = ['person_game_title_relations', 'game_convention_presentation_relations', 'person_convention_relations', 'tags', 'gametags', 'gamedescriptions', 'files', 'sitetexts', 'links', 'trivia'];
    categories_with_ids.forEach(function(category) {
        a[category] = {};
        for (element of data.result[category]) {
            a[category][element.id] = element;
        }
    });
    categories_without_ids.forEach(function(category) {
        a[category] = data.result[category];
    });
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
    $('a[data-category="system"]').click(showRPGSystem);
    $('a[data-category="tag"]').click(showTag);
}

function showPersons() {
    title = 'Persons';
    category = 'persons';
    datatype = 'person';
    anchor = 'person';

    list = getPersons();
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
        var title = 'Board games';
    } else {
        var title = 'Scenarios';
    }
    var category = 'games'
    var datatype = 'game';
    var anchor = 'game';

    var allgames = getGames(boardgames);
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of allgames) {
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

    if (sortcache.conventionsets) {
        var list = sortcache.conventionsets
    } else {
        var list = [];
        for (var element in a[category]) {
            list.push(a[category][element]);
        }
        list.sort(function(a,b) {
            return (a.name > b.name ? 1 : -1);
        });
        sortcache.conventionsets = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, element.name );
    }
    html += '</ul>';
    showContent(html);
    onlineLink('cons');
}

function showConventions(data) {
    var id = data.target.dataset.id;
    var conset = a.conventionsets[id]
    var category = 'conventions';
    var title = 'Conventions for ' + esc(conset.name);
    var files = a.files.filter(rel => rel.data_id == id && rel.category == 'conset');

    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    list = list.filter(convent => convent.conset_id == id)

    list.sort(function(a,b) {
        return a.year - b.year;
    });

    html = '<h2>' + esc(title) + '</h2>';
    if (conset.description) {
        html += '<h3>About the convention:</h3><p>' + esc(conset.description).replace(/\n/g, '</br>'); + '</p>';
    }
    html += makeFileSection(files, id, 'conset');

    html += '<h3>Conventions</h3>';
    html += '<table>';
    for (con of list) {
        var country = a.conventions[con.id].country || conset.country;
        html += '<tr>';
        html += '<td>' + conLink(con.id) + '</td>';
        html += '<td>' + a.conventions[con.id].place + (country ? (a.conventions[con.id].place ? ', ' : '') + country.toUpperCase() : '') + '</td>';
        html += '</tr>';
    }
    html += '</table>';
    showContent(html);
    onlineLink('data?conset=' + id);
}

function showRPGSystems() {
    showCategoryList('RPG Systems', 'systems', 'rpgsystem', 'system', 'name');
    onlineLink('systemer');
}

function showRPGSystems() {
    showCategoryList('RPG Systems', 'systems', 'rpgsystem', 'system', 'name');
    onlineLink('systemer');
}

function showTags() {
    title = 'Tags';
    category = 'gametags';
    anchor = datatype = label = 'tag';
    if (sortcache.tagslist) {
        var list = sortcache.tagslist;
    } else {
        var list = [];
        for (var element in a['gametags']) {
            list.push(a['gametags'][element]);
        }
        for (var element in a['tags']) {
            list.push(a['tags'][element]);
        }
        list.sort(function(a,b) {
            return (a[label].toUpperCase() > b[label].toUpperCase() ? 1 : -1 ) ;
        });
        sortcache.tagslist = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    var seen = {};
    for (element of list) {
        if (! seen[element[label]] ) {
            html += '<li><a href="#' + anchor + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + esc(element[label]) + '">' + esc(element[label]) + '</a></li>';
        }
        seen[element[label]] = true;
    }
    html += '</ul>';
    showContent(html);
    onlineLink('tags');
}

function showRPGSystem(data) {
    var id = data.target.dataset.id;
    var system = a.systems[id];
    var files = a.files.filter(rel => rel.data_id == id && rel.category == 'sys');
    var links = a.links.filter(rel => rel.data_id == id && rel.category == 'system');
    var trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'system');
    var sysgames = [];
    for (var game in a.games) { sysgames.push(a.games[game]); }
    sysgames = sysgames.filter(game => game.system_id == id )
    var html = '';
    html += '<h2>' + esc(system.name) + '</h2>';
    html += makeFileSection(files, id, 'system');
    if (system.description) {
        html += '<p>' + esc(system.description).replace(/\n/g, '</br>'); + '</p>';
    }
    if (sysgames.length > 0) {
        sysgames.sort(function(x,y) {
            return (x.title > y.title ? 1 : -1);
        });
        html += '<h3>Scenarios</h3>';
        html += '<table>';
        for (game of sysgames) {
            var gid = game.id;
            var isDownloadable = a.files.find(file => file.category == 'sce' && file.data_id == gid);
            var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && ( rel.title_id == 1 || rel.title_id == 5 ) );
            var cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == gid);
            html += '<tr>';
            html += '<td>' + (isDownloadable ? typeLink(gid, 'game', '💾') : '') + '</td>';
            html += '<td>' + gameLink(gid) + '</td>';
            html += '<td>';
            for (person of persons) {
                pid = person.person_id;
                html += personLink(person.person_id);
                html += '<br>';
            }
            html += '</td>';
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

    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);

    showContent(html);
    onlineLink('data?system=' + id);
}

function showMagazines() {
    return false;
}

function showCategoryList(title, category, anchor, datatype, label, unique = false) {
    if (sortcache[category]) {
        var list = sortcache[category];
    } else {
        var list = [];
        for (var element in a[category]) {
            list.push(a[category][element]);
        }
        list.sort(function(a,b) {
            return (a[label].toUpperCase() > b[label].toUpperCase() ? 1 : -1 ) ;
        });
        sortcache[category] = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    var seen = {};
    for (element of list) {
        if (! seen[element[label]] ) {
            html += '<li><a href="#' + anchor + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + element.id + '">' + element[label] + '</a></li>';
        }
        if (unique) {
            seen[element[label]] = true;
        }
    }
    html += '</ul>';
    showContent(html);
}

function showConvention(data) {
    var id = data.target.dataset.id;
    var con = a.conventions[id];
    var nom = con.name + ' (' + con.year + ')';
    var datetext = niceDateSet(con.begin, con.end)
    var country = (con.country || a.conventionsets[con.conset_id].country)
    var files = a.files.filter(rel => rel.data_id == id && rel.category == 'convent');
    var organizers = a.person_convention_relations.filter(rel => rel.convention_id == id);
    var links = a.links.filter(rel => rel.data_id == id && rel.category == 'convent');
    var trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'convent');
    var games = a.game_convention_presentation_relations.filter(rel => rel.convention_id == id);
    var scenarios = games.filter(rel => a.games[rel.game_id].boardgame == 0);
    var boardgames = games.filter(rel => a.games[rel.game_id].boardgame == 1);
    var cancelled = parseInt(a.conventions[id].cancelled);
    var html = '<h2>' + esc(nom) + '</h2>';
    var location = ''
    if (con.place) {
        location = con.place;
        if (country) {
            location += ', ';
        }
    }
    if (country) {
        location += country.toUpperCase()
    }
    if (location) {
        html += '<p>Location: ' + esc(location) + '</p>';
    }
    if (datetext) {
        html += '<p>Date: ' + datetext + '</p>';
    }
    html += '<h3>Part of: ' + consetLink(con.conset_id) + '</h3>';
    if (cancelled) {
        html += '<h3 class="cancelnotice">This convention was cancelled.</h3>';
    }
    if (con.description) {
        html += '<h3>About the convention:</h3><p>' + esc(con.description).replace(/\n/g, '</br>'); + '</p>';
    }
    html += makeFileSection(files, id, 'convent');
    html += makeConGameList('Scenarios', scenarios);
    html += makeConGameList('Board games', boardgames);

    if (organizers.length > 0) {
        html += '<h3>Organizers</h3>';
        html += '<table>';
        for (organizer of organizers) {
            html += '<tr>';
            html += '<td>' + esc(organizer.role) + '</td>';
            html += '<td>' + (organizer.person_id ? personLink(organizer.person_id) : organizer.person_extra) + '</td>';
            html += '</tr>';
        }
        html += '</table>';
    }
    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);

    showContent(html);
    onlineLink('data?con=' + id);

}

function showPerson(data) {
    var id = data.target.dataset.id;
    var person = a.persons[id];
    var nom = person.firstname + ' ' + person.surname;
    var birth = person.birth; // not yet exposed
    var death = person.death; // not yet exposed
    var games = a.person_game_title_relations.filter(rel => rel.person_id == id);
    // awards :TODO:
    var organizers = a.person_convention_relations.filter(rel => rel.person_id == id);
    var links = a.links.filter(rel => rel.data_id == id && rel.category == 'aut');
    var trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'aut');
    var html = '<h2>' + esc(nom) + '</h2>';
    if (games.length > 0) {
        html += '<h3>Games</h3>';
        html += '<table>';
        for (game of games) {
            gid = game.game_id;
            isDownloadable = a.files.find(file => file.category == 'sce' && file.data_id == gid);
            cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == gid);
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
    var id = data.target.dataset.id;
    var game = a.games[id];
    var title = game.title;
    var persons = a.person_game_title_relations.filter(rel => rel.game_id == id);
    var descriptions = a.gamedescriptions.filter(rel => rel.game_id == id);
    var cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == id);
    // awards :TODO:
    // runs :TODO:
    var tags = a.gametags.filter(rel => rel.game_id == id);
    var files = a.files.filter(rel => rel.data_id == id && rel.category == 'sce');
    var links = a.links.filter(rel => rel.data_id == id && rel.category == 'sce');
    var trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'sce');

    var html = '<h2>' + esc(title) + '</h2>';
    if (tags.length > 0) {
        html += '<p class="indata">';
        for (tag of tags) {
            html += '<a href="#tag" class="tag" data-category="tag" data-id="' + esc(tag.tag) + '">' + esc(tag.tag) + '</a> ';
        }
        html += '</p>';
    }

    if (game.system_id || game.system_extra) {
        html += '<p class="indata">RPG System: ' + (game.system_id ? RPGSystemLink(game.system_id) : '' ) + ' ' + game.system_extra + '</p>';
    }
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
    html += makeFileSection(files, id, 'sce');
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

function showTag(data) {
    var tagname = data.target.dataset.id;
    var aid = a.tags.filter(rel => rel.tag == tagname);
    if (aid.length > 0) {
        var tag = aid[0];
        var id = tag.id;
    } else {
        var tag = [];
        var id = null;
    }
    var files = a.files.filter(rel => rel.data_id == id && rel.category == 'tag');
    var links = a.links.filter(rel => rel.data_id == id && rel.category == 'tag');
    var trivia = a.trivia.filter(rel => rel.data_id == id && rel.category == 'tag');
    var taggames = a.gametags.filter(rel => rel.tag == tagname);
    var html = '';
    html += '<h2>' + esc(tagname) + '</h2>';
    html += makeFileSection(files, id, 'tag');
    if (tag.description) {
        html += '<p>' + esc(tag.description).replace(/\n/g, '</br>'); + '</p>';
    }
    if (taggames.length > 0) {
        taggames.sort(function(x,y) {
            return (x.title > y.title ? 1 : -1);
        });
        html += '<h3>Scenarios</h3>';
        html += '<table>';
        for (game of taggames) {
            var gid = game.game_id;
            var isDownloadable = a.files.find(file => file.category == 'sce' && file.data_id == gid);
            var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && ( rel.title_id == 1 || rel.title_id == 5 ) );
            var cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == gid);
            html += '<tr>';
            html += '<td>' + (isDownloadable ? typeLink(gid, 'game', '💾') : '') + '</td>';
            html += '<td>' + gameLink(gid) + '</td>';
            html += '<td>';
            for (person of persons) {
                pid = person.person_id;
                html += personLink(person.person_id);
                html += '<br>';
            }
            html += '</td>';
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

    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);

    showContent(html);
    onlineLink('data?tag=' + esc(tagname));
    return false;
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
    var text = text.replace(/\[\[\[(c|s|p|cs|sys)(\d+)\|([^\]]+)\]\]\]/g, bracketSections);
    var text = text.replace(/\[\[\[tag\|([^\]]+)\]\]\]/g, bracketTagSections);
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
    } else if (category == 'sys') {
        return RPGSystemLink(data_id, label);
    }
    return match;
}

function bracketTagSections(match, tag) {
    return tagLink(tag);
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
    if (files.length == 0) {
        return '';
    }
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
        'convent': 'convent',
        'conset': 'conset'
    };
    var url = 'https://alexandria.dk/download/' + map[category] + '/' + data_id + '/' + encodeURIComponent(filename);
    var html = '<li><a href="' + url + '">' + esc(description) + '</a> ' + (language ? '(' + esc(language) + ')' : '') + '</li>';
    return html;
}

function makeConGameList(title, games) {
    if (games.length == 0) {
        return '';
    }
    games.sort(function(x,y) {
        return (a.games[x.game_id].title > a.games[y.game_id].title ? 1 : -1);
    });
    var html = '';
    html += '<h3>' + title + '</h3>';
    html += '<table>';
    for (game of games) {
        var gid = game.game_id;
        var isDownloadable = a.files.find(file => file.category == 'sce' && file.data_id == gid);
        var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && ( rel.title_id == 1 || rel.title_id == 5 ) );
        html += '<tr>';
        html += '<td>' + (isDownloadable ? typeLink(gid, 'game', '💾') : '') + '</td>';
        html += '<td>' + gameLink(gid) + '</td>';
        html += '<td>';
        for (person of persons) {
            pid = person.person_id;
            html += personLink(person.person_id);
            html += '<br>';
        }
        html += '</td>';
        html += '<td>' + (a.games[gid].system_id ? RPGSystemLink(a.games[gid].system_id) : '' ) + ' ' + a.games[gid].system_extra + '</td>';
        html += '</tr>';
    }
    html += '</table>';

    return html;

}

function typeLink(data_id, category, linktext, title = '', extraClass = '') {
    var html = '<a href="#" class="' + category + (extraClass ? ' ' + extraClass : '') + '" title="' + esc(title) + '" data-category="' + category + '" data-id="' + data_id + '">' + esc(linktext) + '</a>';
    return html;
}

function conLink(id, label = '') {
    var text = a.conventions[id].name + ' (' + a.conventions[id].year + ')';
    var begin = niceDate(a.conventions[id].begin);
    var end = niceDate(a.conventions[id].end);
    var cancelled = parseInt(a.conventions[id].cancelled);
    var title = '';
    if (begin && end && (begin != end) ) {
        title = begin + ' - ' + end;
    } else if (begin) {
        title = begin;
    }
    return typeLink(id, 'convention', (label ? label : text), title, (cancelled ? 'cancelled' : '') )
}

function consetLink(id, label = '') {
    return typeLink(id, 'conventionset', (label ? label : a.conventionsets[id].name) );
}

function gameLink(id, label = '') {
    return typeLink(id, 'game', (label ? label : a.games[id].title) );
}

function personLink(id, label = '') {
    return typeLink(id, 'person', (label ? label : (a.persons[id].firstname + ' ' + a.persons[id].surname) ) );
}

function RPGSystemLink(id, label = '') {
    return typeLink(id, 'system', (label ? label : a.systems[id].name ) );
}

function tagLink(tag) {
    return typeLink(tag, 'tag', tag);
}

function downloadable(game_id) {
    var files = a.files.filter(rel => rel.data_id == game_id && rel.category == 'sce');
    return (files.length > 0);
}

function replaceTemplate(string) {
    return string.replace(/\{\$_(.*?)\}/g, function (capture, label) {
        return replaceTemplateDirect(label);
    });
}

function replaceTemplateDirect(label) {
    var translation = a.sitetexts.find(text => text.language == 'en' && text.label == label )
    if (translation) {
        return translation.text;
    }
    return label;
}

function getCache(category, sortfunction) {
    if (sortcache[category]) {
        var list = sortcache[category];
    } else {
        var list = [];
        for (var element in a[category]) {
            list.push(a[category][element]);
        }
        list.sort(sortfunction);
        sortcache[category] = list;
    }
    return list;
}

function getPersons() {
    return getCache('persons', function(a,b) {
        return (a.firstname + a.surname > b.firstname + b.surname ? 1 : -1);
    });
}

function getGames(boardgames) {
    var cachename = (boardgames ? 'boardgames' : 'scenarios')
    var category = 'games';
    if (sortcache[cachename]) {
        var allgames = sortcache[cachename]
    } else {
        var allgames = [];
        for (var element in a[category]) { allgames.push(a[category][element]); }
        allgames = allgames.filter(game => game.boardgame == (boardgames ? 1 : 0) )
        allgames.sort(function(a,b) { return (a.title > b.title ? 1 : -1); });
        sortcache[cachename] = allgames;
    }
    return allgames;
}

function getScenarios() {
    return getGames(false);
}

function getBoardgames() {
    return getGames(true);
}

function getConventions() {
    return getCache('conventions', function(a,b) {
        if (a.conset_id == b.conset_id) { return a.year - b.year} else { return (a.name > b.name ? 1 : -1) }
    });
}

function getSystems() {
    return getCache('systems', function(a,b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getTags() {
    return getCache('tags', function(a,b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getTagsUsed() {
    return getCache('gametags', function(a,b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function search() {
    var search = $('#searchtext').val()
    if (search === '') {
        return false;
    }
    var searchUpper = search.toUpperCase();
    var result = [];
    result.persons = getPersons().filter(p => (p.firstname + ' ' + p.surname).toUpperCase().includes(searchUpper) );
    result.scenarios = getScenarios().filter(p => (p.title).toUpperCase().includes(searchUpper) );
    result.boardgames = getBoardgames().filter(p => (p.title).toUpperCase().includes(searchUpper) );
    result.conventions = getConventions().filter(p => (p.name).toUpperCase().includes(searchUpper) || (a.conventionsets[p.conset_id].name + ' ' + p.year).toUpperCase().includes(searchUpper)  );
    result.systems = getSystems().filter(p => (p.name).toUpperCase().includes(searchUpper) );
    result.tags = [];
    var tagsdefined = getTags().filter(p => (p.tag).toUpperCase().includes(searchUpper) );
    var tagsused = getTagsUsed().filter(p => (p.tag).toUpperCase().includes(searchUpper) );
    var resulttags = tagsdefined.concat(tagsused);
    var seen = {};
    for (thistag of resulttags) {
        if (!seen[thistag.tag]) {
            result.tags.push(thistag);
        }
        seen[thistag.tag] = true;
    }

    var html = '<h2>Search result</h2>';
    if (result.persons.length > 0) {
        html += '<h3>People</h3><ul>';
        for (element of result.persons) {
            html += makeLink('person', 'person', element.id, (element.firstname + ' ' + element.surname) );
        }
        html += '</ul>';
    }
    if (result.scenarios.length > 0) {
        html += '<h3>Scenarios</h3><ul>';
        for (element of result.scenarios) {
            html += makeLink('game', 'game', element.id, element.title );
        }
        html += '</ul>';
    }
    if (result.boardgames.length > 0) {
        html += '<h3>Board games</h3><ul>';
        for (element of result.boardgames) {
            html += makeLink('game', 'game', element.id, element.title );
        }
        html += '</ul>';
    }
    if (result.conventions.length > 0) {
        html += '<h3>Conventions</h3><ul>';
        for (element of result.conventions) {
            html += makeLink('convention', 'convention', element.id, element.name + ' (' + element.year + ')' );
        }
        html += '</ul>';
    }
    if (result.systems.length > 0) {
        html += '<h3>RPG Systems</h3><ul>';
        for (element of result.systems) {
            html += makeLink('system', 'system', element.id, element.name);
        }
        html += '</ul>';
    }
    if (result.tags.length > 0) {
        html += '<h3>Tags</h3><ul>';
        for (element of result.tags) {
            html += makeLink('tag', 'tag', element.tag, element.tag);
        }
        html += '</ul>';
    }
    if (result.persons.length + result.scenarios.length + result.boardgames.length + result.conventions.length + result.systems.length + result.tags.length == 0 ) {
        html += 'Nothing found.'
    }

    showContent(html);
    onlineLink('find?find=' + search )
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

function niceDateSet(begin, end) {
    var begin = niceDate(begin);
    var end = niceDate(end);
    var datetext = '';
    if (begin && end && (begin != end) ) {
        datetext = begin + ' - ' + end;
    } else if (begin) {
        datetext = begin;
    }
    return datetext;
}