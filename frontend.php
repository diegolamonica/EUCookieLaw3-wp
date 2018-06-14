<?php

class EUCookieLawFrontend {

	public function alterNode($matches){
		if( preg_match( '#eucookielaw3\.min\.js#', $matches[4] ) ){
			return $matches[0];
		}else{
			return sprintf('<%s%sdata-eucookielaw-manage="true" data-eucookielaw-attr="%s" data-eucookielaw-src="%s"', $matches[1], $matches[2], $matches[3], $matches[4]);
		}
		
	}

	public function parseHTML(){
		$buffer = ob_get_clean();

		$buffer = preg_replace_callback('#^<(script)([^>]+)(src)="([^"]+)"#ims',
			                       [$this, 'alterNode'],
			                       $buffer);

		$buffer = preg_replace_callback("#^<(script)([^>]+)(src)='([^']+)'#ims",
			                       [$this, 'alterNode'],
			                       $buffer);

		$buffer = preg_replace_callback('#^<(link)([^>]+)(href)="([^"]+)"#ims',
			                       [$this, 'alterNode'],
			                       $buffer);
		$buffer = preg_replace_callback("#^<(link)([^>]+)(href)='([^']+)'#ims",
			                       [$this, 'alterNode'],
			                       $buffer);

		echo $buffer;
	}

	public function __construct() {

		if ( ! is_admin() ) {
			$this->init();
		}

		add_action( 'wp_ajax_consent', [ $this, 'registerConsent' ] );
		add_action( 'wp_ajax_nopriv_consent', [ $this, 'registerConsent' ] );

		add_action( 'wp_ajax_consent-list', [ $this, 'getConsents' ] );
		add_action( 'wp_ajax_nopriv_consent-list', [ $this, 'getConsents' ] );

	}

	public function reviewConsent( $arguments, $content, $shortCode ) {

		$classes = isset( $arguments[ 'class' ] ) ? $arguments[ 'class' ] : '';
		$label   = isset( $arguments[ 'title' ] ) ? $arguments[ 'title' ] : 'Review consents';

		return sprintf( '<a href="#" class="%s" onclick="cookieLaw.showAlert(); return false;">%s</a>', $classes, $label );
	}


	public function myConsents( $arguments, $content, $shortCode ) {
		ob_start();
		include 'consent-table.php';
		$buffer = ob_get_clean();

		return $buffer;
	}

	public function includeEUCookieLawScript() {

		$id = get_the_ID();

		$status = EUCookieLaw3::get( EUCookieLaw3::MD_DISABLED, '', '', $id );


		if ( $status !== '1' ) {
			$value = get_option( EUCookieLaw3::OPT_SCRIPT, '<!-- not found -->' );
			echo $value;
		}
	}

	public function init() {

		$style = get_option( EUCookieLaw3::OPT_STYLE, '' );
		if ( $style !== '' ) {
			wp_register_style( __CLASS__, plugins_url( 'css/' . $style, __FILE__ ) );
		}

		if ( get_option( EUCookieLaw3::OPT_REGISTER_CONSENT, false ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'registerStartupScripts' ] );
		}

		add_action( 'wp_head', [ $this, 'includeEUCookieLawScript' ], - 9999 );
		add_shortcode( 'review_button', [ $this, 'reviewConsent' ] );
		add_shortcode( 'show_consents', [ $this, 'myConsents' ] );

	}

	public function registerConsent() {

		$firstAdminUser = get_users( [ 'role' => 'Administrator', 'number' => 1 ] );
		$adminUser      = ( $firstAdminUser[ 0 ] );
		$newPostId      = wp_insert_post( [
			                                  'post_type'   => EUCookieLaw3::POST_NAME,
			                                  'title'       => rand(),
			                                  'post_author' => $adminUser->ID,
		                                  ] );

		EUCookieLaw3::set( EUCookieLaw3::MD_CONSENTID, $_POST[ 'guid' ], $newPostId );
		EUCookieLaw3::set( EUCookieLaw3::MD_STATUS, $_POST[ 'status' ], $newPostId );
		EUCookieLaw3::set( EUCookieLaw3::MD_WHEN, $_POST[ 'when' ], $newPostId );
		EUCookieLaw3::set( EUCookieLaw3::MD_SERVICE, $_POST[ 'name' ], $newPostId );
		EUCookieLaw3::set( EUCookieLaw3::MD_USER_ADDRESS, $_SERVER[ 'REMOTE_ADDR' ], $newPostId );

		wp_die( "ok" );

	}

	public function getConsents() {


		$guid = $_POST[ 'guid' ];

		$filter = [
			'post_type'      => EUCookieLaw3::POST_SLUG,
			'post_status'    => 'draft',

			'meta_key'       => EUCookieLaw3::MD_CONSENTID,
			'meta_value'     => $guid,

			'posts_per_page' => - 1,
		] ;

		$posts = get_posts( $filter );

		$exportedKeys = [
			EUCookieLaw3::MD_SERVICE,
			EUCookieLaw3::MD_STATUS,
			EUCookieLaw3::MD_WHEN,
			EUCookieLaw3::MD_SERVICE,
		];

		$records = [];
		foreach ( $posts as $post ) {
			$record = [];
			foreach ( $exportedKeys as $exportedKey ) {

				$record[ $exportedKey ] = EUCookieLaw3::get( $exportedKey, '', '', $post->ID );

			}

			$records[] = $record;
		}

		echo wp_json_encode( $records );
		die();


	}


	public function registerStartupScripts() {

		wp_enqueue_script( __CLASS__, plugins_url( '/scripts/frontend.min.js', __FILE__ ), [ 'jquery' ], EUCookieLaw3::VERSION, false );
		wp_localize_script( __CLASS__, 'eucookielawGlobalData',
		                    [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
		wp_enqueue_style( __CLASS__ );
	}


}