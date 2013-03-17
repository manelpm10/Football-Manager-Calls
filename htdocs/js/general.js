if(!jQuery) {
    include_jQuery();
    var $ = jQuery.noConflict();
} else {
    var $ = jQuery;
}

var footballTeam = {
	init: function() {
		var sAvailability = null;
		var sSpam = false;
		var sOldStatus = null;
		var sHtml = '';

		$('#team-tabs').tabs();

		$('input.join-button').live( 'click', function() {
			sAvailability = $(this).val();

			// Prevent spasmodic clicks.
			if ( sSpam === sAvailability  )
			{
				alert( 'We are processing your click' );
				return false;
			}
			sSpam = sAvailability;

			$.post( "?ctname=AjaxMatchController", { op: "join-player", available: sAvailability, match: $('#id-match').val() },
				function( response ){
					if ( true == response.state )
					{
						// Update counters.
						sOldStatus = $('#player_'+response.data.id_player).attr( 'class' );
						$('#counter-' + sOldStatus).text( parseInt( $('#counter-' + sOldStatus).text() ) - 1 );
						$('#counter-' + response.data.available).text( parseInt( $('#counter-' + response.data.available).text() ) + 1 );

						// Update user status call.
						$('#join').hide( 'slow' );

						sHtml = '';
						if ( $(".close-call-button") )
						{
							sHtml += '<input type="checkbox" name="player['+response.data.id_player+']" value="true" ';
							if ("called" == response.data.id_player)
							{
								sHtml += '"checked="checked"';
							}
							sHtml += '/>';
						}
						sHtml += '<img title="'+response.data.name+'" src="'+response.data.image_url+'" width="50px" height="50px" />';
						sHtml += '<a title="'+response.data.name+'" href="'+response.data.player_url+'">'+response.data.name+'</a>';
						if ( 'available' != sAvailability )
						{
							sHtml += '<input class="join-button" type="button" value="available">';
						}
						if ( 'unavailable' != sAvailability )
						{
							sHtml += '<input class="join-button" type="button" value="unavailable">';
						}
						if ( 'injuried' != sAvailability )
						{
							sHtml += '<input class="join-button" type="button" value="injuried">';
						}
						if ( 'if_necessary' != sAvailability )
						{
							sHtml += '<input class="join-button" type="button" value="if_necessary">';
						}

						if ( 0 < $('#player_'+response.data.id_player).length )
						{
							$('#player_'+response.data.id_player).hide('slow').html( sHtml ).show('slow');
							$('#player_'+response.data.id_player).attr('class',response.data.available);
						}
						else
						{
							sHtml = '<li id="player_'+response.data.id_player+'" class="'+response.data.available+'">' + sHtml + '</li>'
							$('#call-state').append( sHtml );
						}
					}
					else
					{
						alert( response.msg_error );
					}
				}, "json");
		});
	}
}

$(document).ready(function() {
	footballTeam.init();

	var called_players = $('.called').length;
	var called_players_container = $( document.getElementById('called_players') ).find('strong').text( called_players );
	var available_players = $('.available').length;
	var if_necessary_players = $('.if_necessary').length;
	var unavailable_players = $('.unavailable').length + $('.injuried').length;

	$( document.getElementById('called_players') ).find('strong').text( called_players );
	$( document.getElementById('available_players') ).find('strong').text( available_players );
	$( document.getElementById('if_necessary_players') ).find('strong').text( if_necessary_players );
	$( document.getElementById('unavailable_players') ).find('strong').text( unavailable_players );

	jQuery( document.getElementById('call-state') ).delegate('input', 'change', function(evt) {
		called_players_container.text();
		if( $(this).attr('checked') )
		{
			called_players++;
		}
		else
		{
			called_players--;
		}
		called_players_container.text(called_players);
	});
});
