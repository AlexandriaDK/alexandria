$(function() {
	$( "#ffind" ).autocomplete({
		source: '../ajax.php',
		minLength: 2,
		delay: 100,
		select: function( event, ui ) {
			window.location = 'redir.php?cat=' + ui.item.linkpart + '&data_id=' + ui.item.id;
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append(" <a>" + item.label + "</a>" )
			.append(" <br><span class='autosearchnote'>" + item.note + "</span>" )
			.appendTo ( ul );
	}
	$( ".gamelookup" ).autocomplete({
		source: 'lookup.php?type=games',
		delay: 100
	});
});

function reviewSubmit() {
	var data_id = parseInt( $( "#data_id" ).val() );
	var review_title = $( "input[name=review_title]" ).val();
	var language = $( "#language" ).val();
	if ( ! data_id ) {
		alert('No game selected.');
		return false;
	}
	if ( review_title.length == 0 ) {
		alert('No title entered');
		return false;
	}
	if ( language == 'dk' ) {
		alert('Language \'dk\' does not exist. Use \'da\' for Danish.')
		return false;
	}
	if ( language == 'us' || language == 'gb'  ) {
		alert('Language \'' + language + '\' does not exist. Use \'en\' for English.')
		return false;
	}
	if ( language == 'se' ) {
		return confirm('Language \'se\' is Northern Sami, not Swedish (sv). Are you sure you want to submit?')
	}
	if ( language == 'uk' ) {
		return confirm('Language \'uk\' is Ukrainian, not English (en). Are you sure you want to submit?')
	}
	
	return true;
}
function filenameToDescription(filename) {
	description = filename.charAt(0).toUpperCase() + filename.slice(1);
	description = description.substr(0,description.lastIndexOf('.'));
	return description;
}

