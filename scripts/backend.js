jQuery(document).ready(function( $ ) {
	if( window.location.search.indexOf('EUCookieLaw3-settings') !== -1 ) {
		var textarea   = $( 'textarea' ),
		    size       = {
			    width:  textarea.width(),
			    height: textarea.height()
		    },
		    codeEditor = wp.codeEditor.initialize( jQuery( 'textarea' ), {
			    codemirror: {
				    lineNumbers: true,
				    mode:        "text/html",
			    }
		    } );

		window.theCodeEditor = codeEditor;
		codeEditor.codemirror.setSize( size.width, size.height );
	}
});
