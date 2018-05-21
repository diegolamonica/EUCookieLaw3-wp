(function ( $ ) {

	/**
	 * Send the consent/reject choice of the user to the server
	 *
	 * @var groupName string
	 * @var agreement string
	 * @var when string
	 */
	window.userConsentCallback = function ( groupName, agreement, when ) {
		var sign = localStorage.getItem( 'eucookielaw-client-signature' ) || false;
		if ( sign ) {
			$.post( eucookielawGlobalData.ajax_url, {
				action: 'consent',
				name:   groupName,
				status: agreement,
				when:   when,
				guid:   sign
			} );
		}
	};


	$(document).ready( function ( ) {

		/*
		 * Get the list of consents from the user
		 */
		if( $('#list-of-consents').length > 0){

			var sign = localStorage.getItem( 'eucookielaw-client-signature' ) || false;

			if(sign){

				$.ajax({
					url: eucookielawGlobalData.ajax_url,
					data: {
						action: 'consent-list',
						guid:   sign,
					},
					dataType: 'json',
				} ).done( function ( response ) {


					if(response.length > 0){
						$(response).each( function(){
							var newRow = $('<tr><td /><td /><td /></tr>').appendTo('#list-of-consents');
							newRow.find('td:nth-child(1)').text ( this.service );
							newRow.find('td:nth-child(2)').text ( this.status == 'ok' ? 'Consented' : 'Rejected' );
							newRow.find('td:nth-child(3)').text ( this.when );

						})

					}else{



					}

				});

			}

		}

	});

})( jQuery );