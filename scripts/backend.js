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
			    patternCSS = /.*(bootstrap-like|darky-miky)\.css.*/,
			    patternJS = /.*(https:\/\/diegolamonica\.info\/tools\/eucookielaw3\.min\.js).*/,
			changed = false;

			if( patternCSS.test(buffer) ){
				changed = true;
				buffer = buffer.replace(patternCSS, '');
			}
			console.log(patternJS, buffer);
			if( patternJS.test(buffer) ){
				console.log('Changing JS');
				changed = true;
				buffer = buffer.replace(patternJS, '<script src="' + EUCookieLawScriptURL +'"></script>');
			}

			if(changed) {
				codeEditor.codemirror.setValue( buffer );
			}
		});


	}
});
