<?php

class EUCookieLaw3 {
	const VERSION     = '20180526.1';
	const POST_NAME   = 'EUCookieLaw';
	const POST_SLUG   = 'eucookielaw';
	const LANG_DOMAIN = 'EUCookieLaw3';

	const MD_CONSENTID    = 'consent_identifier';
	const MD_SERVICE      = 'service';
	const MD_STATUS       = 'status';
	const MD_WHEN         = 'when';
	const MD_USER_ADDRESS = 'ip_address';
	const MD_DISABLED     = 'disabled';

	const OPT_SCRIPT           = 'eucookielaw_setting';
	const OPT_STYLE            = 'eucookielaw_style';
	const OPT_ROLE             = 'eucookielaw_role';
	const OPT_REGISTER_CONSENT = 'eucookielaw_register_consent';
	/**
	 * @var EUCookieLawBackend
	 */
	static $backend;

	/**
	 * @var EUCookieLawFrontend
	 */
	static $frontend;

	static public function get( $metadata, $from = '', $to = '', $postId = null ) {
		return toSendItCustomPost::get( $metadata, $from, $to, $postId );
	}

	static public function set( $metadata, $valore, $postId = null ) {
		toSendItCustomPost::set( $metadata, $valore, $postId );
	}

	static public function loadTranslations() {

		load_plugin_textdomain( self::LANG_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	static public function init() {
		/*
		 * Includo il frontend e il backend.
		 */
		require dirname( __FILE__ ) . '/backend.php';
		require dirname( __FILE__ ) . '/frontend.php';

		self::$backend  = new EUCookieLawBackend();
		self::$frontend = new EUCookieLawFrontend();

		/*
		 * Registro il custom post
		 */
		self::$backend->register(
			self::POST_NAME,
			self::POST_SLUG,
			'i ',
			'Consent', 'Consents',
			[ 'title', 'comments' ],
			is_admin() ? [ self::$backend, 'manageMetaboxes' ] : null,
			self::LANG_DOMAIN,
			false, [
				'capability_type' => 'post',
				'capabilities'    => [
					'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
					'delete_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout


				],
				'map_meta_cap'    => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			]
		);

	}

	public function __construct() {

		add_action( 'init', [ __CLASS__, 'init' ] );
		add_action( 'plugins_loaded', [ __CLASS__, 'loadTranslations' ] );

	}

}