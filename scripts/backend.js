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

		codeEditor.codemirror.setSize( size.width, size.height );

		codeEditor.codemirror.on('change', function(){
			var buffer =  codeEditor.codemirror.getValue(),
			    pattern = /.*(bootstrap-like|darky-miky)\.css.*/;

			if( pattern.test(buffer) ){

				codeEditor.codemirror.setValue( buffer.replace(pattern, '') )

			}
		});


	}
});
