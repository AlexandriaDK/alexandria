document.getElementById('startup').style.display = 'block';

var sortcache = [];

$(function () {
    initialize();
});

function initialize() {
    loadData();
    locationHashChanged();
    window.onhashchange = locationHashChanged;
    $("#search").submit(function (event) {
        var searchHash = '#search=' + $('#searchtext').val()
        if (history.pushState) {
            history.pushState(null, null, searchHash);
        }
        else {
            location.hash = searchHash;
        }
        search($('#searchtext').val());
        event.preventDefault();
    });
}

function loadData() {
    loadAlexandria();
    a = [];
    license = data.license;
    access = data.access;
    categories_with_ids = ['persons', 'conventions', 'systems', 'games', 'conventionsets', 'titles', 'presentations', 'magazines', 'issues', 'articles', 'award_nominees', 'award_categories', 'locations'];
    categories_without_ids = ['person_game_title_relations', 'game_convention_presentation_relations', 'person_convention_relations', 'tags', 'gametags', 'gamedescriptions', 'files', 'sitetexts', 'links', 'trivia', 'contributors', 'article_reference', 'gameruns', 'award_nominee_entities', 'location_reference'];
    categories_with_ids.forEach(function (category) {
        a[category] = {};
        for (element of data.result[category]) {
            a[category][element.id] = element;
        }
    });
    categories_without_ids.forEach(function (category) {
        a[category] = data.result[category];
    });
    $("#startup").hide();
    $("#menu").show();
    $("#currentdatainfo").text("This data set is from the following date: " + niceDate(data.request.received))
    showContent('');
}

function locationHashChanged() {
    var h = location.hash.substring(1);
    // Overviews
    if (h == 'persons') {
        showPersons();
        return;
    }
    if (h == 'scenarios' || h == 'boardgames') {
        showGames();
        return;
    }
    if (h == 'conventionsets') {
        showConventionSets();
        return;
    }
    if (h == 'gamesystems') {
        showGameSystems();
        return;
    }
    if (h == 'tags') {
        showTags();
        return;
    }
    if (h == 'magazines') {
        showMagazines();
        return;
    }
    if (h == 'locations') {
        showLocations();
        return;
    }
    // Individual entries
    var [key, id] = h.split('=');
    if (key == 'gamesystem') {
        showGameSystem(id);
        return;
    }
    if (key == 'game') {
        showGame(id);
        return;
    }
    if (key == 'person') {
        showPerson(id);
        return;
    }
    if (key == 'conventionset') {
        showConventions(id);
        return;
    }
    if (key == 'convention') {
        showConvention(id);
        return;
    }
    if (key == 'tag') {
        showTag(id);
        return;
    }
    if (key == 'magazine') {
        showMagazine(id);
        return;
    }
    if (key == 'issue') {
        showIssue(id);
        return;
    }
    if (key == 'location') {
        showLocation(id);
        return;
    }
    if (key == 'search') {
        search(id);
        return;
    }
    return false;
}

function showContent(html) {
    $("#content").html(html);
}

function showPersons() {
    title = 'Persons';
    category = 'persons';
    datatype = 'person';
    anchor = 'person';

    list = getPersons();
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, (element.firstname + ' ' + element.surname));
    }
    html += '</ul>';
    showContent(html);
    onlineLink('personer');
}

function showGames() {
    boardgames = (location.hash == '#boardgames');
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
        html += makeLink(anchor, datatype, element.id, element.title);
    }
    html += '</ul>';
    showContent(html);
    onlineLink(boardgames ? 'boardgames' : 'scenarier');
}

function showConventionSets() {
    var title = 'Convention sets';
    var category = 'conventionsets';
    var anchor = 'conventionset';
    var datatype = 'conventionset';

    if (sortcache.conventionsets) {
        var list = sortcache.conventionsets
    } else {
        var list = ota(category);
        list.sort(function (a, b) {
            return (a.name > b.name ? 1 : -1);
        });
        sortcache.conventionsets = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    for (element of list) {
        html += makeLink(anchor, datatype, element.id, element.name);
    }
    html += '</ul>';
    showContent(html);
    onlineLink('cons');
}

function showConventions(id) {
    // var id = data.target.dataset.id;
    var conset = a.conventionsets[id]
    var category = 'conventions';
    var title = 'Conventions for ' + esc(conset.name);
    var links = a.links.filter(rel => rel.conset_id == id);
    var trivia = a.trivia.filter(rel => rel.conset_id == id);
    var files = a.files.filter(rel => rel.conset_id == id);
    var conlist = ota(category).filter(convention => convention.conset_id == id)

    conlist.sort(function (a, b) {
        return a.year - b.year;
    });

    html = '<h2>' + esc(title) + '</h2>';
    if (conset.description) {
        html += '<h3>About the convention:</h3>' + getDescription(conset.description);
    }
    html += makeFileSection(files, id, 'conset');

    html += '<h3>Conventions</h3>';
    html += '<table>';
    for (con of conlist) {
        var country = a.conventions[con.id].country || conset.country;
        html += '<tr>';
        html += '<td>' + conLink(con.id) + '</td>';
        html += '<td>' + a.conventions[con.id].place + (country ? (a.conventions[con.id].place ? ', ' : '') + getCountryName(country) : '') + '</td>';
        html += '</tr>';
    }
    html += '</table>';

    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);
    html += makeArticleReferenceSection('conset', id);

    showContent(html);
    onlineLink('data?conset=' + id);
}

function showGameSystems() {
    showCategoryList('RPG Systems', 'systems', 'gamesystem', 'system', 'name');
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
        list.sort(function (a, b) {
            return (a[label].toUpperCase() > b[label].toUpperCase() ? 1 : -1);
        });
        sortcache.tagslist = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    var seen = {};
    for (element of list) {
        if (!seen[element[label]]) {
            anchorTag = 'tag=' + encodeURI(element[label]);
            html += '<li><a href="#' + anchorTag + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + esc(element[label]) + '">' + esc(element[label]) + '</a></li>';
        }
        seen[element[label]] = true;
    }
    html += '</ul>';
    showContent(html);
    onlineLink('tags');
}

function showMagazines() {
    showCategoryList('Magazines', 'magazines', 'magazine', 'magazine', 'name');
    onlineLink('magazines');
    return false;
}

function showLocations() {
    showCategoryList('Locations', 'locations', 'location', 'location', 'name');
    onlineLink('locations');
    return false;
}

function showCategoryList(title, category, anchor, datatype, label, unique = false) {
    if (sortcache[category]) {
        var list = sortcache[category];
    } else {
        var list = ota(category);
        list.sort(function (a, b) {
            return (a[label].toUpperCase() > b[label].toUpperCase() ? 1 : -1);
        });
        sortcache[category] = list;
    }
    html = '<h2>' + esc(title) + '</h2><ul class="datalist">';
    var seen = {};
    for (element of list) {
        if (!seen[element[label]]) {
            var anchorID = anchor + '=' + element.id;
            html += '<li><a href="#' + anchorID + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + element.id + '">' + element[label] + '</a></li>';
        }
        if (unique) {
            seen[element[label]] = true;
        }
    }
    html += '</ul>';
    showContent(html);
}

function showGameSystem(id) {
    // var id = data.target.dataset.id;
    var system = a.systems[id];
    // :TODO: 
    var files = a.files.filter(rel => rel.gamesystem_id == id);
    var links = a.links.filter(rel => rel.gamesystem_id == id);
    var trivia = a.trivia.filter(rel => rel.gamesystem_id == id);
    var sysgames = [];
    for (var game in a.games) { sysgames.push(a.games[game]); }
    sysgames = sysgames.filter(game => game.gamesystem_id == id)
    var html = '';
    html += '<h2>' + esc(system.name) + '</h2>';
    html += makeFileSection(files, id, 'gamesystem');
    html += getDescription(system.description);
    if (sysgames.length > 0) {
        sysgames.sort(function (x, y) {
            return (x.title > y.title ? 1 : -1);
        });
        html += '<h3>Scenarios</h3>';
        html += '<table>';
        for (game of sysgames) {
            var gid = game.id;
            var isDownloadable = a.files.find(file => file.game_id == gid);
            var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && (rel.title_id == 1 || rel.title_id == 5));
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
    html += makeArticleReferenceSection('system', id);
    showContent(html);
    onlineLink('data?system=' + id);
}

function showConvention(id) {
    // var id = data.target.dataset.id;
    var con = a.conventions[id];
    var nom = con.name + ' (' + con.year + ')';
    var datetext = niceDateSet(con.begin, con.end)
    var country = (con.country || a.conventionsets[con.conset_id].country)
    var files = a.files.filter(rel => rel.convention_id == id);
    var organizers = a.person_convention_relations.filter(rel => rel.convention_id == id);
    var links = a.links.filter(rel => rel.convention_id == id);
    var trivia = a.trivia.filter(rel => rel.convention_id == id);
    var games = a.game_convention_presentation_relations.filter(rel => rel.convention_id == id);
    var scenarios = games.filter(rel => a.games[rel.game_id].boardgame == 0);
    var boardgames = games.filter(rel => a.games[rel.game_id].boardgame == 1);
    var cancelled = parseInt(a.conventions[id].cancelled);
    var html = '<h2>' + esc(nom) + '</h2>';
    var location = '';
    if (con.place) {
        location = con.place;
        if (country) {
            location += ', ';
        }
    }
    if (country) {
        location += getCountryName(country);
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
        html += '<h3>About the convention:</h3>' + getDescription(con.description);
    }
    html += makeFileSection(files, id, 'convention');
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
    html += makeArticleReferenceSection('convention', id);

    showContent(html);
    onlineLink('data?con=' + id);

}

function showPerson(id) {
    // var id = data.target.dataset.id;
    var person = a.persons[id];
    var nom = person.firstname + ' ' + person.surname;
    var birth = person.birth; // not yet exposed
    var death = person.death; // not yet exposed
    var games = a.person_game_title_relations.filter(rel => rel.person_id == id);
    // awards :TODO:
    var organizers = a.person_convention_relations.filter(rel => rel.person_id == id);
    var links = a.links.filter(rel => rel.person_id == id);
    var trivia = a.trivia.filter(rel => rel.person_id == id);
    var html = '<h2>' + esc(nom) + '</h2>';
    if (games.length > 0) {
        html += '<h3>Games</h3>';
        html += '<table>';
        for (game of games) {
            gid = game.game_id;
            isDownloadable = a.files.find(file => file.game_id == gid);
            cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == gid);
            html += '<tr>';
            html += '<td>' + (isDownloadable ? typeLink(gid, 'game', '💾') : '') + '</td>';
            html += '<td>' + a.titles[game.title_id].title + (game.note ? ' (' + esc(game.note) + ')' : '') + '</td>';
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
    html += makeArticleReferenceSection('person', id, 'contributor');
    html += makeArticleReferenceSection('person', id, 'reference');

    showContent(html);
    onlineLink('data?person=' + id);
}

function showGame(id) {
    // var id = data.target.dataset.id;
    var game = a.games[id];
    var title = game.title;
    var persons = a.person_game_title_relations.filter(rel => rel.game_id == id);
    var descriptions = a.gamedescriptions.filter(rel => rel.game_id == id);
    var cons = a.game_convention_presentation_relations.filter(rel => rel.game_id == id);
    // awards :TODO:
    var runs = a.gameruns.filter(rel => rel.game_id == id);
    var tags = a.gametags.filter(rel => rel.game_id == id);
    var files = a.files.filter(rel => rel.game_id == id);
    var links = a.links.filter(rel => rel.game_id == id);
    var trivia = a.trivia.filter(rel => rel.game_id == id);

    runs.sort(function (a, b) {
        return (a.begin > a.end ? 1 : -1);
    })

    var html = '<h2>' + esc(title) + '</h2>';
    if (tags.length > 0) {
        html += '<p class="indata">';
        for (tag of tags) {
            var anchorTag = 'tag=' + encodeURI(tag.tag);
            html += '<a href="#' + anchorTag + '" class="tag" data-category="tag" data-id="' + esc(tag.tag) + '">' + esc(tag.tag) + '</a> ';
        }
        html += '</p>';
    }
    if (game.gamesystem_id || game.gamesystem_extra) {
        html += '<p class="indata">RPG System: ' + (game.gamesystem_id ? GameSystemLink(game.gamesystem_id) : '') + ' ' + game.gamesystem_extra + '</p>';
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
            var pid = element.person_id;
            html += makeLink('person', 'person', pid, a.persons[pid].firstname + ' ' + a.persons[pid].surname, a.titles[element.title_id].title + (element.note ? ' (' + esc(element.note) + ')' : ''));
        }
        html += '</ul>';
    }
    html += makeFileSection(files, id, 'game');
    if (descriptions.length > 0) {
        html += '<h3>Description</h3>';
        for (description of descriptions) {
            if (descriptions.length > 1) {
                html += '<hr>';
                html += "<h4>" + esc(getLanguageName(description.language)) + "</h4>";
            }
            html += getDescription(description.description);
        }
    }
    if (cons.length > 0) {
        html += '<h3>Played at</h3>';
        html += '<ul>';
        for (con of cons) {
            cid = con.convention_id;
            html += '<li>' + conLink(cid) + esc(' (' + replaceTemplateDirect(a.presentations[con.presentation_id].event_label) + ')') + '</li>';
        }
        html += '</ul>';
    }
    if (runs.length > 0) {
        if (cons.length > 0) {
            html += '<h3>Other runs</h3>';
        } else {
            html += '<h3>Runs</h3>';
        }
        html += '<ul>';
        for (run of runs) {
            var cancelledClass = (run.cancelled == 1 ? 'cancelled' : '');
            html += '<li>';
            html += '<span class="' + cancelledClass + '">';
            if (run.begin && run.begin != '0000-00-00') {
                html += niceDateSet(run.begin, run.end);
                if (run.location || run.country) {
                    html += ', ';
                }
            }
            if (run.location) {
                html += esc(run.location);
                if (run.country) {
                    html += ', ';
                }
            }
            if (run.country) {
                html += esc(getCountryName(run.country));
            }
        }
        html += '</ul>';
    }
    html += makeTriviaSection(trivia);
    html += makeLinkSection(links);
    html += makeArticleReferenceSection('game', id, 'publishedgame');
    html += makeArticleReferenceSection('game', id, 'reference');
    html += makeAwardSection(getAwards('game', id));

    showContent(html);
    onlineLink('data?scenarie=' + id);
}

function showTag(tagname) {
    // var tagname = data.target.dataset.id;
    tagname = decodeURI(tagname);
    var aid = a.tags.filter(rel => rel.tag == tagname);
    if (aid.length > 0) {
        var tag = aid[0];
        var id = tag.id;
    } else {
        var tag = [];
        var id = 0;
    }
    var files = a.files.filter(rel => rel.tag_id == id);
    var links = a.links.filter(rel => rel.tag_id == id);
    var trivia = a.trivia.filter(rel => rel.tag_id == id);
    var taggames = a.gametags.filter(rel => rel.tag == tagname);
    var html = '';
    html += '<h2>' + esc(tagname) + '</h2>';
    html += makeFileSection(files, id, 'tag');
    html += getDescription(tag.description);
    if (taggames.length > 0) {
        taggames.sort(function (x, y) {
            return (x.title > y.title ? 1 : -1);
        });
        html += '<h3>Scenarios</h3>';
        html += '<table>';
        for (game of taggames) {
            var gid = game.game_id;
            var isDownloadable = a.files.find(file => file.game_id == gid);
            var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && (rel.title_id == 1 || rel.title_id == 5));
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
    html += makeArticleReferenceSection('tag', id);

    showContent(html);
    onlineLink('data?tag=' + esc(tagname));
    return false;
}

function showMagazine(id) {
    // var id = data.target.dataset.id;
    var magazine = a.magazines[id];
    var issues = [];
    for (var issue in a.issues) { issues.push(a.issues[issue]); }
    issues = issues.filter(i => i.magazine_id == id)

    var html = '';
    html += '<h2>' + esc(magazine.name) + '</h2>';
    html += getDescription(magazine.description);
    if (issues.length > 0) {
        issues.sort(function (x, y) {
            return (x.releasedate > y.releasedate);
        });
        html += '<h3>Issues</h3><ul>';
        for (issue of issues) {
            html += makeLink('issue', 'issue', issue.id, issue.title + (issue.releasetext ? ', ' + issue.releasetext : ''))
        }
        html += '</ul>';
    }
    html += makeArticleReferenceSection('magazine', id);

    showContent(html);
    onlineLink('magazines?id=' + id);
}

function showIssue(id) {
    var category = 'issue';

    // var id = data.target.dataset.id;
    var files = a.files.filter(rel => rel.issue_id == id);

    var issue = a.issues[id];
    var magazinename = a.magazines[issue.magazine_id].name;
    var title = magazinename + ': ' + issue.title + (issue.releasetext ? ', ' + issue.releasetext : '');
    var allarticles = [];
    for (var article in a.articles) { allarticles.push(a.articles[article]); }
    var articles = allarticles.filter(i => i.issue_id == id && (i.title || i.page));
    var colophones = allarticles.filter(i => i.issue_id == id && !(i.title || i.page));

    articles.sort(function (a, b) {
        return (parseInt(a.page) > parseInt(b.page) ? 1 : -1);
    });

    var html = '';
    html += '<h2>' + esc(title) + '</h2>';

    html += makeFileSection(files, id, 'issue');

    if (colophones.length) {
        html += '<h3>Colophon</h3>';
        html += '<table>';
        for (colophon of colophones) {
            var contributors = a.contributors.filter(c => c.article_id == colophon.id);
            for (var contributor of contributors) {
                var pid = contributor.person_id;
                html += '<tr><td class="issuerole">' + esc(contributor.role) + '</td>';
                html += '<td>';
                if (pid) {
                    html += makeLink('person', 'person', pid, a.persons[pid].firstname + ' ' + a.persons[pid].surname, '', false);
                } else {
                    html += esc(contributor.person_extra);
                }
                html += '</td></tr>';

            }
        }
        html += '</table>';
    }

    if (articles.length) {
        html += '<h3>Articles</h3>';
        html += '<table>';
        for (article of articles) {
            var contributors = a.contributors.filter(c => c.article_id == article.id);
            var references = a.article_reference.filter(c => c.article_id == article.id);

            html += '<tr>';
            html += '<td class="page">'
            if (article.page) {
                html += 'Page ' + article.page;
            }
            html += '</td>';
            html += '<td>' + esc(article.articletype) + '</td>';
            html += '<td>' + esc(article.title) + '<br><span class="articledescription">' + getDescription(article.description, false) + '</span>';
            if (references) {
                html += '<br><span class="references">'
                for (var reference of references) {
                    var category = getCategoryFromReference(reference);
                    var data_id = reference[category + '_id'];
                    html += linkFromReference(category, data_id) + ' ';
                }
                html += '</span>';
            }
            html += '</td>';
            html += '<td class="articlefullcontributor">';
            for (var contributor of contributors) {
                var pid = contributor.person_id;
                if (pid) {
                    html += makeLink('person', 'person', pid, a.persons[pid].firstname + ' ' + a.persons[pid].surname, (contributor.role != '' ? ' (' + contributor.role + ')' : ''), false);
                } else {
                    html += esc(contributor.person_extra) + (contributor.role != '' ? ' (' + contributor.role + ')' : '');
                }
                html += '<br>';
            }
            html += '</td>';
            html += '</tr>';
        }
        html += '</table>';
    }

    html += makeArticleReferenceSection('issue', id);

    showContent(html);
    onlineLink('magazines?issue=' + id);
}


function showLocation(id) {
    // var id = data.target.dataset.id;
    var location = a.locations[id];
    // var issues = [];
    // for (var issue in a.issues) { issues.push(a.issues[issue]); }
    // issues = issues.filter(i => i.magazine_id == id)

    var html = '';
    html += '<h2>' + esc(location.name) + '</h2>';
    html += '<p>';
    if (location.address) {
        html += getDescription(esc(location.address), false) + '<br>';
    }
    html += getDescription(esc(location.city), false);
    if (location.city != '' && location.country) {
        html += ', ';
    }
    html += getCountryName(location.country) + '<br>';
    html += getDescription(location.note, false);
    html += '</p>';
    if (location.latitude && location.longitude) {
        var url = `https://www.openstreetmap.org/?mlat=${location.latitude}&mlon=${location.longitude}#map=16/${location.latitude}/${location.longitude}`;
        html += `<p><a href="${url}">[Show on map]</a></p>`;
    }

    var lcons = a.location_reference.filter(rel => rel.location_id == id && rel.convention_id != null && rel.gamerun_id == null);
    var lgameruns = a.location_reference.filter(rel => rel.location_id == id && rel.convention_id == null && rel.gamerun_id != null);

    if (lcons.length > 0) {
        html += '<h3>Conventions</h3>';
        html += '<ul>';
        for (var con of lcons) {
            cid = con.convention_id;
            html += '<li>' + conLink(cid) + '</li>';
        }
        html += '</ul>';
    }

    if (lgameruns.length > 0) {
        html += '<h3>Games</h3>';
        html += '<ul>';
        for (runs of lgameruns) {
            [run] = a.gameruns.filter(rel => rel.id == runs.gamerun_id);
            var yearText = '(?)';
            if (run.begin) {
                yearText = '(' + run.begin.substring(0, 4) + ')';
            }
            html += '<li>';
            html += gameLink(run.game_id);
            html += ' ' + yearText;
            html += '</li>';
        }
        html += '</ul>';
    }
    showContent(html);
    onlineLink('locations?id=' + id);
}

function showSingleData(data) {
    id = data.target.dataset.id;
    type = data.target.dataset.type;
    if (type == 'person') {
        showPerson(data);
    } else if (type == 'game') {
        showGame(data);
    } else if (type == 'convention') {
        showConvention(data);
    } else if (type == 'system') {
        showGameSystem(data);
    }
}

function getDescription(description, p = true) {
    var html = '';
    if (description) {
        html += esc(description).replace(/\n/g, '</br>');
    }
    if (p) {
        html = '<p>' + html + '</p>';
    }
    return html;
}

function esc(text) { // escape, replace templates and then parse [[[links]]]
    if (!text) {
        return '';
    }
    text = text.replace(/[\"&<>]/g, function (a) {
        return { '"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;' }[a];
    });
    text = replaceTemplate(text);
    text = bracketTemplate(text);
    return text;
}

function bracketTemplate(text) {
    var text = text.replace(/\[\[\[(c|s|p|cs|sys|m)(\d+)\|([^\]]+)\]\]\]/g, bracketSections);
    var text = text.replace(/\[\[\[(?:t|tag)\|([^|\]]+)(?:\|([^\]]+))?\]\]\]/g, bracketTagSections);
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
        return GameSystemLink(data_id, label);
    } else if (category == 'm') {
        return magazineLink(data_id, label);
    }
    return match;
}

function bracketTagSections(match, tag, text) {
    return tagLink(tag, text || tag);
}

function onlineLink(parturl) {
    var url = 'https://alexandria.dk/en/' + parturl;
    $('#onlinelink').html('<a href="' + url + '">Online version of current page</a>');
}

function ota(category) { // Object to array
    var list = [];
    for (var element in a[category]) {
        list.push(a[category][element]);
    }
    return list;
}

function getAwards(category, id) {
    var awards = [];
    if (category == 'game') {
        var cawards = [];
        awards = ota('award_nominees').filter(rel => rel.game_id == id);
        for (award_id in awards) {
            var award_category = a.award_categories[awards[award_id].award_category_id];
            var convention_id = award_category.convention_id;
            var convention = a.conventions[convention_id];
            var convention_name = convention.name + ' (' + convention.year + ')';
            awards[award_id].category_name = award_category.name;
            awards[award_id].convention_name = convention_name;
            awards[award_id].convention_id = convention_id;
            if (!cawards[convention_id]) {
                cawards[convention_id] = [];
                cawards[convention_id].name = convention_name;
                cawards[convention_id].id = convention_id;
                cawards[convention_id].awards = [];
            }
            cawards[convention_id].awards.push(awards[award_id]);
        }
        awards = cawards;
    }
    return awards;
}


function makeLink(anchor, datatype, elementid, linktext, optional = '', li = true) {
    var anchorID = anchor + '=' + elementid;
    var html = '<a href="#' + anchorID + '" class="' + datatype + '" data-category="' + datatype + '" data-id="' + elementid + '">' + esc(linktext) + '</a> ' + esc(optional);
    if (li) {
        html = '<li>' + html + '</li>';
    }
    return html;
}

function makeFileSection(files, id, category) {
    var html = '';
    if (files.length == 0) {
        return '';
    }
    if (hasLocalFiles) {
        html += '<h3>Download (from local archive)</h3>';
        html += makeFileSectionPart(files, id, category, true);
    }
    html += '<h3>Download (from online website)</h3>';
    html += makeFileSectionPart(files, id, category, false);
    return html;
}

function makeFileSectionPart(files, id, category, localFiles) {
    if (files.length == 0) {
        return '';
    }
    var html = '';
    html += '<ul>';
    for (file of files) {
        html += makeFileLink(id, category, file.filename, file.description, file.language, localFiles);
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

function makeFileLink(data_id, category, filename, description, language, localFiles) {
    var map = {
        'game': 'scenario',
        'convention': 'convent',
        'conset': 'conset',
        'gamesystem': 'system',
        'issue': 'issue',
        'tag': 'tag',
    };
    var url = '';
    if (localFiles) {
        url = 'files/';
    } else {
        url = 'https://alexandria.dk/download/';
    }
    var linkpart = map[category] + '/' + data_id + '/' + encodeURIComponent(filename);
    url += linkpart;
    var html = '<li><a href="' + url + '">' + esc(description) + '</a> ' + (language ? '(' + esc(getLanguageName(language)) + ')' : '') + '</li>';
    return html;
}

function getFieldFromCategory(category) {
    if (['person', 'game', 'convention', 'conset', 'gamesystem', 'tag', 'magazine', 'issue'].includes(category)) {
        return category + '_id';
    }
    return 'person_id';
}

function getCategoryFromReference(object) {
    var categories = ['person', 'game', 'convention', 'conset', 'gamesystem', 'tag', 'magazine', 'issue']
    for (var category of categories) {
        var field = category + '_id';
        if (object[field] != null) {
            return category;
        }
    }
    return false;
}

function makeArticleReferenceSection(category, data_id, referencetype = 'reference') {
    var field = getFieldFromCategory(category);
    if (referencetype == 'reference') {
        var references = a.article_reference.filter(rel => rel[field] == data_id);
        var title = 'Referenced in the following articles';
    } else if (referencetype == 'contributor') {
        var references = a.contributors.filter(rel => rel.person_id == data_id);
        var title = 'Articles';
    } else if (referencetype == 'publishedgame') {
        var references = [];
        for (var article in a.articles) {
            if (a.articles[article].game_id == data_id) {
                var myarticle = a.articles[article];
                myarticle['article_id'] = myarticle.id;
                references.push(myarticle);
            }
        }
        var title = 'Articles';
    }
    if (references.length == 0) {
        return '';
    }
    var html = '<h3>' + esc(title) + '</h3>';
    html += '<table>';
    for (var reference of references) {
        var article = a.articles[reference.article_id];
        var issue = a.issues[article.issue_id];
        var magazine = a.magazines[issue.magazine_id];
        html += '<tr>';
        html += '<td>' + esc(article.title) + '</td>';
        html += '<td class="page">' + (article.page ? 'Page ' + article.page : '') + '</td>';
        html += '<td>' + makeLink('issue', 'issue', issue.id, issue.title, issue.releasetext, false) + '</td>';
        html += '<td>' + makeLink('magazine', 'magazine', magazine.id, magazine.name, '', false) + '</td>';
        html += '</tr>';
    }
    html += '</table>';
    return html;
}

function makeAwardSection(awards) {
    if (awards.length == 0) {
        return '';
    }
    var html = '<h3>Awards</h3>';
    for (con_id in awards) {
        html += '<p>' + makeLink('convention', 'convention', con_id, awards[con_id].name, '', false) + '<br>';
        for (award of awards[con_id].awards) {
            html += (award.winner == 1 ? 'Winner' : 'Nominated') + ', ' + esc(award.category_name) + '<br>';
        }
        html += '</p>';

    }
    return html;
}

function makeConGameList(title, games) {
    if (games.length == 0) {
        return '';
    }
    games.sort(function (x, y) {
        return (a.games[x.game_id].title > a.games[y.game_id].title ? 1 : -1);
    });
    var html = '';
    html += '<h3>' + title + '</h3>';
    html += '<table>';
    for (game of games) {
        var gid = game.game_id;
        var isDownloadable = a.files.find(file => file.game_id == gid);
        var persons = a.person_game_title_relations.filter(rel => rel.game_id == gid && (rel.title_id == 1 || rel.title_id == 5));
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
        html += '<td>' + (a.games[gid].gamesystem_id ? GameSystemLink(a.games[gid].gamesystem_id) : '') + ' ' + a.games[gid].gamesystem_extra + '</td>';
        html += '</tr>';
    }
    html += '</table>';

    return html;

}

function linkFromReference(category, id) {
    if (category == 'game') {
        return gameLink(id);
    } else if (category == 'person') {
        return personLink(id);
    } else if (category == 'tag') {
        return tagIdLink(id);
    } else if (category == 'gamesystem') {
        return GameSystemLink(id);
    } else if (category == 'convention') {
        return conLink(id);
    } else if (category == 'magazine') {
        return magazineLink(id);
    } else if (category == 'conset') {
        return consetLink(id);
    } else {
        return '';
    }
}

function typeLink(data_id, category, linktext, title = '', extraClass = '') {
    var anchorID = category + '=' + data_id;
    var html = '<a href="#' + anchorID + '" class="' + category + (extraClass ? ' ' + extraClass : '') + '" title="' + esc(title) + '" data-category="' + category + '" data-id="' + data_id + '">' + esc(linktext) + '</a>';
    return html;
}

function conLink(id, label = '') {
    var text = a.conventions[id].name + ' (' + a.conventions[id].year + ')';
    var begin = niceDate(a.conventions[id].begin);
    var end = niceDate(a.conventions[id].end);
    var cancelled = parseInt(a.conventions[id].cancelled);
    var title = '';
    if (begin && end && (begin != end)) {
        title = begin + ' - ' + end;
    } else if (begin) {
        title = begin;
    }
    return typeLink(id, 'convention', (label ? label : text), title, (cancelled ? 'cancelled' : ''))
}

function consetLink(id, label = '') {
    return typeLink(id, 'conventionset', (label ? label : a.conventionsets[id].name));
}

function gameLink(id, label = '') {
    return typeLink(id, 'game', (label ? label : a.games[id].title));
}

function personLink(id, label = '') {
    return typeLink(id, 'person', (label ? label : (a.persons[id].firstname + ' ' + a.persons[id].surname)));
}

function GameSystemLink(id, label = '') {
    return typeLink(id, 'gamesystem', (label ? label : a.systems[id].name));
}

function magazineLink(id, label = '') {
    return typeLink(id, 'magazine', (label ? label : a.magazines[id].name));
}

function issueLink(id, label = '') {
    return typeLink(id, 'issue', (label ? label : a.issue[id].title + (a.issue[id].releasetext ? ', ' + a.issue[id].releasetext : '')));
}

function tagLink(tag, text) {
    return typeLink(tag, 'tag', text);
}

function tagIdLink(tag_id) {
    tag = a.tags.filter(rel => rel.id == tag_id)[0].tag;
    //    tag = a.tags[tag_id].tag;
    return typeLink(tag, 'tag', tag);
}

function downloadable(game_id) {
    var files = a.files.filter(rel => rel.data_id == game_id);
    return (files.length > 0);
}

function replaceTemplate(string) {
    return string.replace(/\{\$_(.*?)\}/g, function (capture, label) {
        return replaceTemplateDirect(label);
    });
}

function replaceTemplateDirect(label) {
    var translation = a.sitetexts.find(text => text.language == 'en' && text.label == label)
    if (translation) {
        return translation.text;
    }
    return label;
}

function getCache(category, sortfunction) {
    if (sortcache[category]) {
        var list = sortcache[category];
    } else {
        var list = ota(category);
        list.sort(sortfunction);
        sortcache[category] = list;
    }
    return list;
}

function getPersons() {
    return getCache('persons', function (a, b) {
        return (a.firstname + a.surname > b.firstname + b.surname ? 1 : -1);
    });
}

function getGames(boardgames) {
    var cachename = (boardgames ? 'boardgames' : 'scenarios')
    var category = 'games';
    if (sortcache[cachename]) {
        var allgames = sortcache[cachename]
    } else {
        var allgames = ota(category).filter(game => game.boardgame == (boardgames ? 1 : 0))
        allgames.sort(function (a, b) { return (a.title > b.title ? 1 : -1); });
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
    return getCache('conventions', function (a, b) {
        if (a.conset_id == b.conset_id) { return a.year - b.year } else { return (a.name > b.name ? 1 : -1) }
    });
}

function getSystems() {
    return getCache('systems', function (a, b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getTags() {
    return getCache('tags', function (a, b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getTagsUsed() {
    return getCache('gametags', function (a, b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getMagazines() {
    return getCache('magazines', function (a, b) {
        return (a.name > b.name ? 1 : -1);
    });
}

function getLocations() {
    return getCache('locations', function (a, b) {
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
    result.persons = getPersons().filter(p => (p.firstname + ' ' + p.surname).toUpperCase().includes(searchUpper));
    result.scenarios = getScenarios().filter(p => (p.title).toUpperCase().includes(searchUpper));
    result.boardgames = getBoardgames().filter(p => (p.title).toUpperCase().includes(searchUpper));
    result.conventions = getConventions().filter(p => (p.name).toUpperCase().includes(searchUpper) || (a.conventionsets[p.conset_id].name + ' ' + p.year).toUpperCase().includes(searchUpper));
    result.systems = getSystems().filter(p => (p.name).toUpperCase().includes(searchUpper));
    result.magazines = getMagazines().filter(p => (p.name).toUpperCase().includes(searchUpper));
    result.locations = getLocations().filter(p => (p.name).toUpperCase().includes(searchUpper));
    result.tags = [];
    var tagsdefined = getTags().filter(p => (p.tag).toUpperCase().includes(searchUpper));
    var tagsused = getTagsUsed().filter(p => (p.tag).toUpperCase().includes(searchUpper));
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
            html += makeLink('person', 'person', element.id, (element.firstname + ' ' + element.surname));
        }
        html += '</ul>';
    }
    if (result.scenarios.length > 0) {
        html += '<h3>Scenarios</h3><ul>';
        for (element of result.scenarios) {
            html += makeLink('game', 'game', element.id, element.title);
        }
        html += '</ul>';
    }
    if (result.boardgames.length > 0) {
        html += '<h3>Board games</h3><ul>';
        for (element of result.boardgames) {
            html += makeLink('game', 'game', element.id, element.title);
        }
        html += '</ul>';
    }
    if (result.conventions.length > 0) {
        html += '<h3>Conventions</h3><ul>';
        for (element of result.conventions) {
            html += makeLink('convention', 'convention', element.id, element.name + ' (' + element.year + ')');
        }
        html += '</ul>';
    }
    if (result.systems.length > 0) {
        html += '<h3>RPG Systems</h3><ul>';
        for (element of result.systems) {
            html += makeLink('gamesystem', 'gamesystem', element.id, element.name);
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
    if (result.magazines.length > 0) {
        html += '<h3>Magazines</h3><ul>';
        for (element of result.magazines) {
            html += makeLink('magazine', 'magazine', element.id, element.name);
        }
        html += '</ul>';
    }
    if (result.locations.length > 0) {
        html += '<h3>Locations</h3><ul>';
        for (element of result.locations) {
            html += makeLink('location', 'location', element.id, element.name);
        }
        html += '</ul>';
    }
    if (result.persons.length + result.scenarios.length + result.boardgames.length + result.conventions.length + result.systems.length + result.tags.length + result.magazines.length + result.locations.length == 0) {
        html += 'Nothing found.'
    }

    showContent(html);
    onlineLink('find?find=' + search)
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
    if (begin && end && (begin != end)) {
        datetext = begin + ' - ' + end;
    } else if (begin) {
        datetext = begin;
    }
    return datetext;
}

function getCountryName(code) {
    if (code.length != 2) {
        return code.toUpperCase();
    }
    return new Intl.DisplayNames(['en'], { type: 'region' }).of(code);
}

function getLanguageName(codestring) { // can contain more languages, e.g. 'da,en'
    var languages = [];
    var codes = codestring.split(/\s*,\s*/);
    codes.forEach((code) => languages.push(getSingleLanguageName(code)));
    return languages.join(', ');
}

function getSingleLanguageName(code) {
    if (code.length < 2) {
        return code;
    }
    return new Intl.DisplayNames(['en'], { type: 'language' }).of(code.substring(0, 2)) + code.substring(2);

}