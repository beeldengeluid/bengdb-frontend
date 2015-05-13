console.log('BengDB plugin loaded');

var ENDPOINT = 'http://bengdb.beeldengeluid.nl/api/search?q=';

function parse() {
    console.log('Parsing page');

    var matches = ''.concat(
        '[ng-if="metadata.personen"] dd,',
        '[ng-if="metadata.makers"] dd'
    );

    function getTooltip(person, data) {
        var $el = $(''.concat(
            '<span><a href="http://bengdb.beeldengeluid.nl/' + data.gtaa + '">',
            person + '</a>;</span>'
        ));

        var tt = '<span>';

        if (data.item.image) {
            tt += '<img width="200" style="margin-bottom:10px;" src="' + data.item.image.thumb + '"><br>';
        }

        tt += ''.concat(
            '<p>' + data.item.descriptions,
            ' (' + data.item.birthDeathDetails + ')</p></span>'
        );

        $el.tooltipster({
            content : $(tt),
            theme: 'tooltipster-light',
            maxWidth : 220
        });

        return $el;
    }

    function getLinkForPerson(person, cb) {
        var lookup = person.trim().toLowerCase();
        var url = ENDPOINT + encodeURIComponent(lookup);
        console.log(url);

        $.get(url, function(data) {
            console.log('Got data');
            if (data.error) {
                cb(false, person + ';');
            } else {
                var tooltip = getTooltip(person, data);
                cb(false, tooltip);
            }
        });
    }

    $(matches).each(function() {
        var $el = $(this);
        var people = $el.text().split(';');

        async.map(people, getLinkForPerson, function(err, results) {
            $el.html(results);
        });
    });
}

// Wait until 'ui-view="details" is loaded'
var tries = 5;

function goTry() {
    var isLoaded = !!$('[metadata="details.metadata"]').length;

    if (isLoaded) {
        parse();
    } else {
        tries--;

        if (tries > 0) {
            setTimeout(goTry, 500);
        }
    }
}

goTry();