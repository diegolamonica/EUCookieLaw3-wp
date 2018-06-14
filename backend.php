<?php

class EUCookieLawBackend extends toSendItCustomPost {

	const MENU_SLUG = 'EUCookieLaw3';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
		/*
				 * Setting page management
				 */
		add_filter( 'admin_menu', [ $this, 'admin' ] );
	}

	public function init() {

		/*
		 * Consent list configuration
		*/
		add_filter( 'manage_' . EUCookieLaw3::POST_SLUG . '_posts_custom_column', [ $this, "showAdminColumns" ] );
		add_filter( 'manage_' . EUCookieLaw3::POST_SLUG . '_posts_columns', [ $this, "setAdminColumns" ] );
		add_filter( 'manage_' . EUCookieLaw3::POST_SLUG . '_sortable_columns', [ $this, 'setSortableColumns' ] );

		/*
		 * Configurazione dell'interfaccia (salvataggio e messaggi)
		*/

		add_action( 'add_meta_boxes', [ $this, 'manageMetaboxes' ] );
		add_action( 'save_post', [ $this, 'saveEUCookieLawStatus' ] );
		add_meta_box( __CLASS__ . '_is_enabled', __( 'EUCookieLaw status', EUCookieLaw3::LANG_DOMAIN ), [
			$this,
			"showEnablingMetabox",
		] );
		// add_filter( 'post_updated_messages', [ $this, 'updateMessages' ] );
		// add_action( 'save_post', [ $this, 'saveMetadata' ] );

		/*
		 * Accoda gli script necessari e i fogli di stile
		*/
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStartupScripts' ] );


	}

	public function about() {
		?>
		<h1><?= __( "About EUCookieLaw", EUCookieLaw3::LANG_DOMAIN ); ?></h1>

		<p>
			<?= __( "EUCookieLaw3 is a revamped version of the plugin EUCookieLaw.", EUCookieLaw3::LANG_DOMAIN ) ?>
		</p>
		<p>
			<?= sprintf(
				__( "Read the <a href=\"%s\">on-line documentation</a>", EUCookieLaw3::LANG_DOMAIN ),
				'https://www.github.com/diegolamonica/EUCookieLaw3/'
			); ?>
		</p>
		<p>
			<?= __( "The plugin is totally free and its source code is open source in all its parts.", EUCookieLaw3::LANG_DOMAIN ) ?><br />
		</p>
		<p>
			<?= __( "If you found it useful, please consider for:", EUCookieLaw3::LANG_DOMAIN ) ?>
		</p>
		<table class="form-table">
			<tr>
				<td class="text-center">
					<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40diegolamonica%2einfo&lc=IT&item_name=EU%20Cookie%20Law%203&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest">

						<!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tbody><tr><td align="center"></td></tr><tr><td align="center"><a href="https://www.paypal.com/it/webapps/mpp/paypal-popup" title="Come funziona PayPal" onclick="javascript:window.open('https://www.paypal.com/it/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_74x46.jpg" border="0" alt="PayPal Logo" /></a></td></tr></tbody></table><!-- PayPal Logo -->
						<?= __( "A donation via PayPal", EUCookieLaw3::LANG_DOMAIN ); ?>
					</a>
				</td>
				<td class="text-center">
					<a href="http://amzn.eu/h0ngjnC">
						<img src="https://images-na.ssl-images-amazon.com/images/G/01/x-locale/communities/wishlist/uwl/UWL_SWF_shims._CB368675346_.png" /><br />
						<?= __( "A gift from my Amazon Wishlist", EUCookieLaw3::LANG_DOMAIN ); ?>
					</a>
				</td>
			</tr>
		</table>
		<?php
	}

	private function buildScreen( $screen ) {
		add_screen_option( 'layout_columns', [ 'max' => 2, 'default' => 2 ] );
		?>
		<div class="wrap">
			<h2>EUCookieLaw</h2>

			<div id="poststuff">
				<form name="post" method="post" novalidate="novalidate">
					<div id="post-body"
					     class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( $screen, 'side', $screen ); ?>
						</div>
						<div id="postbox-container-2" class="postbox-container">
							<?php do_meta_boxes( $screen, 'normal', $screen ); ?>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	public function settings() {
		$screen = WP_Screen::get();
		add_screen_option( 'layout_columns', [ 'max' => 2, 'default' => 2 ] );
		add_meta_box(
			'eucookielaw-banner-code' . $screen->id,
			__( 'Banner', EUCookieLaw3::LANG_DOMAIN ),
			[ $this, 'bannerMetabox' ],
			$screen, 'normal', 'high'
		);

		add_meta_box(
			'eucookielaw-message-support' . $screen->id,
			__( 'Support', EUCookieLaw3::LANG_DOMAIN ),
			[ $this, 'outputMessagesSupport' ],
			$screen, 'side', 'high'
		);

		add_meta_box(
			'eucookielaw-donation' . $screen->id,
			__( 'Donation', EUCookieLaw3::LANG_DOMAIN ),
			[ $this, 'donationsMetabox' ],
			$screen, 'side', 'high'
		);
		$this->buildScreen( $screen );
	}

	private function displayFBLike() {

		?>
		<div id="fb-root"></div>
		<script>(function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[ 0 ];
				if ( d.getElementById( id ) ) return;
				js = d.createElement( s );
				js.id = id;
				js.src = "//connect.facebook.net/it_IT/sdk.js#xfbml=1&version=v2.3&appId=451493874905248";
				fjs.parentNode.insertBefore( js, fjs );
			}( document, 'script', 'facebook-jssdk' ));</script>
		<div class="fb-page" data-href="https://www.facebook.com/UsaEUCookieLaw" data-hide-cover="true"
		     data-show-facepile="true" data-show-posts="true">
			<div class="fb-xfbml-parse-ignore">
				<blockquote cite="https://www.facebook.com/UsaEUCookieLaw">
					<a href="https://www.facebook.com/UsaEUCookieLaw">EUCookieLaw</a>
				</blockquote>
			</div>
		</div>
		<?php
	}

	public function donationsMetabox() {
		?>
		<p>
			<?php echo sprintf(
				__( "If you find this plugin useful, and since I've noticed that nobody did this script (as is) before of me, " .
				    "I'd like to receive <a href=\"%s\">a donation</a> or a gift from <a href=\"%s\">my Amazon Wishlist</a> as thankful for the time You've earned for you, your " .
				    "family and your hobbies! :)", EUCookieLaw3::LANG_DOMAIN ),
				"https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40diegolamonica%2einfo&lc=IT&item_name=EU%20Cookie%20Law%203&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest",
				"http://amzn.eu/h0ngjnC"
			); ?>
		</p
		<p>
			<?= sprintf(
				__( 'You can find further informations about this plugin on <a href="%s">GitHub</a>', EUCookieLaw3::LANG_DOMAIN ), 'https://github.com/diegolamonica/EUCookieLaw3/' ); ?>
		</p>
		<?php
		$this->displayFBLike();
	}

	public function outputMessagesSupport() {
		?>
		<h3><?php _e( "Save settings", EUCookieLaw3::LANG_DOMAIN ); ?></h3>


		<p>
		<p class="eucookielaw-info-submit"><strong>EUCookieLaw version <?php echo EUCookieLaw3::VERSION ?></strong></p>
		<p>
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( __CLASS__ ); ?>"/>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( "Save" ); ?>">
		</p>
		<?php
		if ( defined( 'WP_CACHE' ) && WP_CACHE === true ) {
			?>
			<p class="help">
				<?php _e( "Note that, to ensure the cached contents uses the right settings from EUCookieLaw you need to empty your cache (according to specific cache plugin settings) once you have saved the configuration", EUCookieLaw3::LANG_DOMAIN ); ?>
			</p>
			<?php
		}
	}

	public function bannerMetabox() {
		if ( isset( $_POST[ 'nonce' ] ) && wp_verify_nonce( $_POST[ 'nonce' ], __CLASS__ ) ) {

			$_POST = stripslashes_deep( $_POST );

			update_option( EUCookieLaw3::OPT_SCRIPT, $_POST[ 'banner' ] );
			update_option( EUCookieLaw3::OPT_STYLE, $_POST[ 'style' ] );
			update_option( EUCookieLaw3::OPT_REGISTER_CONSENT, isset( $_POST[ 'consent' ] ) );
			update_option( EUCookieLaw3::OPT_ROLE, isset( $_POST[ 'role' ] ) );
		}
		$script  = get_option( EUCookieLaw3::OPT_SCRIPT, '' );
		$consent = get_option( EUCookieLaw3::OPT_REGISTER_CONSENT, false );
		$style   = get_option( EUCookieLaw3::OPT_STYLE, false );
		$theRole = get_option( EUCookieLaw3::OPT_ROLE, false );;
		$settings = [
			'codeEditor' => wp_enqueue_code_editor( [ 'mode' => 'text/html' ] ),
		];
		wp_enqueue_script( 'wp-theme-plugin-editor' );

		?>
		<h3><?= __( "What the hell?!?!", EUCookieLaw3::LANG_DOMAIN ) ?></h3>
		<p>
			<strong><?= __( "Where are all EUCookieLaw settings???", EUCookieLaw3::LANG_DOMAIN ) ?></strong><br/>
			<?= __( "Don't worry, nowdays all is more simple than in the past! Now you can use the <a href=\"https://diegolamonica.info/tools/eucookielaw/builder/\">online Configuration Builder</a> to produce the right configuration for your site!", EUCookieLaw3::LANG_DOMAIN ) ?>
		</p>
		<p></p>
		<script type="text/javascript">
			var EUCookieLawScriptURL = '<?=plugins_url( '/scripts/eucookielaw3.min.js', __FILE__ )?>';
		</script>
		<label
			for="code-editor"><?php _e( "EUCookieLaw Banner source code", EUCookieLaw3::LANG_DOMAIN ); ?></label></th>

		<textarea id="code-editor" name="banner" cols="30" rows="18"
		          class="large-text"><?= htmlspecialchars( $script ) ?></textarea>
		<p class="help">
			<?= __( "Paste the code obtained from the builder", EUCookieLaw3::LANG_DOMAIN ); ?>
		</p>
		<div>
			<label>
				<input type="checkbox" value="1" name="consent" <?php checked( $consent ) ?>>
				<strong><?= __( "Register user consent", EUCookieLaw3::LANG_DOMAIN ) ?></strong>
			</label>
			<p class="help">
				<?= __( 'Enable this flag if you want that all consents and rejections should be tracked', EUCookieLaw3::LANG_DOMAIN ); ?>
			</p>
		</div>
		<div>
			<label>
				<?= __( "Select banner aspect:", EUCookieLaw3::LANG_DOMAIN ); ?>
				<select name="style">
					<option
						value="" <?php selected( $style == '' ) ?>><?= __( "No decoration", EUCookieLaw3::LANG_DOMAIN ); ?></option>
					<option value="bootstrap-like.css" <?php selected( $style == 'bootstrap-like.css' ) ?>>Bootstrap
						Like
					</option>
					<option value="darky-miky.css" <?php selected( $style == 'darky-miky.css' ) ?>>Dark Style</option>
				</select>
				<p class="help">
					<?= sprintf(
						__( "You can set a default style or you should define the rules for the Banner following the <a href=\"%s\">design guideguide documentation</a>", EUCookieLaw3::LANG_DOMAIN ), "https://github.com/diegolamonica/EUCookieLaw3-themes" ); ?>
				</p>
			</label>
		</div>
		<div>
			<label>
				<?= __( "Who can manage EUCookieLaw3 settings?", EUCookieLaw3::LANG_DOMAIN ); ?>
				<?php
				$roles = wp_roles()->roles;
				$rolesKeys = array_keys($roles);
				# echo '<pre>', print_r($roles,1), '</pre>';

				?>
				<select name="role">
					<?php
					foreach ( $rolesKeys as $role ) {
						?>
						<option value="<?=$role?>" <?php selected( $role == $theRole ) ?>><?= __( $roles[$role]['name'] ); ?></option>
						<?php
					}
					?>
				</select>
				<p class="help">
					<?= __( "Define the minimum access level allowed", EUCookieLaw3::LANG_DOMAIN ); ?>
				</p>
			</label>
		</div>
		<?php
	}

	public function tools() {
		?>
		<h1><?= __( "Useful resource for good people!", EUCookieLaw3::LANG_DOMAIN ); ?></h1>
		<?php

		$response = wp_remote_get( "https://diegolamonica.info/tools/eucookielaw/tools.json" );

		if ( ! is_wp_error( $response ) ) {

			$toolsList = json_decode( $response[ 'body' ] );

		} else {
			$toolsList = false;
		}

		if ( ! $toolsList ) {
			_e( "I am not able to retrieve useful resource list at the moment... please try later", EUCookieLaw3::LANG_DOMAIN );
		} else {

			?>

			<p>
				<?= __( "The following resources are not directly related to this plugin.", EUCookieLaw3::LANG_DOMAIN ) ?>
			</p>
			<p>
				<?= sprintf(

					__( "If you discover some useful resources online related to CookieLaw and GDPR and you want to share with all, " .
					    "let me knwo at <a href=\"mailto:%s\">%1\$s</a>.", EUCookieLaw3::LANG_DOMAIN ),
					'diego.lamonica@gmail.com' );
				?>
			</p>

			<ul class="tool-list">
				<?php
				foreach ( $toolsList as $item ) {
					if ( empty( $item->icon ) ) {
						$item->icon = plugins_url( '/img/logo.png', __FILE__ );
					}
					?>
					<li class="tool-item">
						<img class="tool-icon" src="<?= $item->icon ?>" alt=""/>
						<div class="tool-info">
							<h2 class="tool-title"><?= $item->title ?></h2>
							<p class="tool-description"><?= $item->description ?></p>
							<p class="tool-more">
								<a target="_blank" href="<?= $item->link ?>"><?= $item->link ?></a>
							</p>
						</div>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
	}

	public function admin() {

		add_menu_page(
			"EU Cookie Law 3", "EU Cookie Law 3",
			'activate_plugins',
			self::MENU_SLUG,
			[ $this, 'about' ],
			'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAELElEQVQ4jY3UT0ibdxzH8feTJ08e85fUJyYxsWal082trsWDMhOcrjCZ2DWH0ZYd2sNOxdMOhcIQBsXbYAhedll3XmG9CQUVVqSpTGjHaLLQBTpnTJ8GNf//Pf920WLdOvq9/eD3e/1+/L58P/Dm5QYC4+Pj0fn5ecfrNgn/J9jt9qFkMjk3MzPzcU9PT7/D4RA6nY4tlUrVKpXK6aWlpcobgaFQ6NTFixd/PH/+/MTIyAiKoiCKIuVyGZvNRi6XY3V1dX10dPTT2dnZ2iuPOH5Bf39/+Nq1a39OT0/bPB4Psizj9Xqx2+34fD6azSaFQgFFURL5fP5LYPEoIB4Do5cvX/55cnLypM/nIxgMsru7i2VZuFwubDYbkiQRiUSIxWKsrKzEx8bGHA8ePPjlELAdwWJnzpwZV1V1zOl00mw2qdfrzM3NIUkS1WqVra0tMpkMAOFwmGQy6U2n018vLi6GjoMy8LaiKN9GIhGcTiemaeL1erl9+zaWZfH8+XMePXrE0tIS9XodgIGBASYmJuzPnj3rPQ6GXC5X1OFw9IdCIURRxO/3k8lkKBaLqKqKaZoMDw9z48YNLMsCwOfzMTU1JdTr9c+O/qEAfBAKhd4JBAKfDA4OMjg4yPDwMLqu02q1kGWZ3d1dZFlGEAT8fj92ux1N04hEIrRaram+vr5vNE0zbQedDsmyHDUMA1mWabfbXLhwgY2NDfx+P5IksbGxwd27dzEMg3w+T7FY5OrVq3Q6HS5dusT169cZGhr6wn7wSpdpmn9ZlkWlUkEURW7evAlAtVpFFEXi8TixWIxyuYwkSezv71OpVLh37x7xeJx2u82LFy9cIiAB70uSVFYU5XOPx4MkSQQCAWRZZnt7m3a7TXd3N61Wi729PSRJwufzMTMzgyiKaJpGPp/n/v37yzbAANrFYrGuaRqFQgFVVSkUCjgcDgzD4M6dO+i6TqPRwDAMnE4nT548YX9//+UEbW5u4na7vxMBCwgD7yqKkul0Oh8eQpZl4XQ6SSQSZLNZqtUqgUCAEydOsLCwwLlz5yiXy+zt7bG8vPzr2trarUMQ4L1qtfowGAxeKZVKtp6eHkRRRNd1dnZ20HWdbDZLs9mkq6uLRCKBIAiUy2XW1tZIpVJJVVXzh6PXAMK6rg90d3f/IYri2M7OjuDxeABwu93Iskw+n2d0dJRcLsfTp0+p1Wqsr6+TSqXm0+n0T4B1NG36gWnAF41G1b6+vu/tdrurt7eXs2fPvoQty6Krq4t0Ok02m6VUKi1sbm7eAtrw7/g6CXwE9MqyvDIyMvIVcMU0TcnhcBAMBhEEwVBV1Wi1Wg81Tfvh8ePHq8D2IfBfeagAk8BbQAf4PRAIWIIghEVRVCqVyt+NRqMGmMBvQOno4dcltgs4DZwCPAeNMwENqAE5YOtg/Ur9A4rvtmO4NgDnAAAAAElFTkSuQmCC' );

		$theRole = get_option( EUCookieLaw3::OPT_ROLE, 'administrator');

		add_submenu_page( self::MENU_SLUG,
		                  __( "All you need to know about EUCookieLaw", EUCookieLaw3::LANG_DOMAIN ),
		                  __( "About", EUCookieLaw3::LANG_DOMAIN ),
		                  $theRole,
		                  self::MENU_SLUG, [
			                  $this,
			                  'about',
		                  ] );
		add_submenu_page( self::MENU_SLUG,
		                  __( "EUCookieLaw Settings", EUCookieLaw3::LANG_DOMAIN ),
		                  __( "Settings", EUCookieLaw3::LANG_DOMAIN ),
		                  $theRole,
		                  self::MENU_SLUG . '-settings', [
			                  $this,
			                  'settings',
		                  ] );
		add_submenu_page( self::MENU_SLUG, __( "EUCookieLaw Tools", EUCookieLaw3::LANG_DOMAIN ),
		                  __( "Tools", EUCookieLaw3::LANG_DOMAIN ),
		                  $theRole,
		                  self::MENU_SLUG . '-tools', [
			                  $this,
			                  'tools',
		                  ] );
	}

	/**
	 * Imposta le colonne da presentare nell'elenco dei consensi in area amministrativa
	 * Questo metodo è agganciato all'action "manage_<post-name>_post_columns"
	 *
	 * @param array $cols
	 *
	 * @return array
	 */
	public function setAdminColumns( $cols ) {
		$cols = [
			'cb'                          => '<input type="checkbox" />',
			EUCookieLaw3::MD_CONSENTID    => __( 'Consent Identifier', EUCookieLaw3::LANG_DOMAIN ),
			EUCookieLaw3::MD_SERVICE      => __( 'Service', EUCookieLaw3::LANG_DOMAIN ),
			EUCookieLaw3::MD_STATUS       => __( 'Consent status', EUCookieLaw3::LANG_DOMAIN ),
			EUCookieLaw3::MD_WHEN         => __( 'Since', EUCookieLaw3::LANG_DOMAIN ),
			EUCookieLaw3::MD_USER_ADDRESS => __( 'IP Address', EUCookieLaw3::LANG_DOMAIN ),
		];

		return $cols;

	}

	public function setSortableColumns( $cols ) {

		return $cols;
	}

	public function showAdminColumns( $col ) {
		global $post;
		$value = get_post_meta( $post->ID, $col, true );
		switch ( $col ) {

			case EUCookieLaw3::MD_WHEN:
				$value = mysql2date( 'D d M, Y', $value );
				break;
			case EUCookieLaw3::MD_STATUS:
				$value = __( ( $value === 'ok' ) ? 'Approved' : 'Rejected', EUCookieLaw3::LANG_DOMAIN );
				break;
			case EUCookieLaw3::MD_USER_ADDRESS:
				/*
				 * Anonymizing IP in display mode
				 */
				$value = preg_replace('#\d+$#', '***', $value);
				break;
		}
		echo( $value );
	}


	public function registerStartupScripts() {
		wp_enqueue_script( 'cm_xml' );
		wp_enqueue_script( 'cm_javascript' );
		wp_enqueue_script( 'cm_css' );
		wp_enqueue_script( __CLASS__, plugins_url( '/scripts/backend.min.js', __FILE__ ), [
			'jquery',
			'wp-codemirror',
		], EUCookieLaw3::VERSION, false );
		wp_enqueue_style( __CLASS__, plugins_url( '/css/backend.css', __FILE__ ), [], EUCookieLaw3::VERSION, false );
		wp_enqueue_style( 'codemirror' );
		wp_enqueue_style( 'cm_blackboard' );
	}

	public function saveEUCookieLawStatus( $postId ) {

		if ( isset( $_POST ) && isset( $_POST[ __CLASS__ ] ) ) {

			// verify this came from the our screen and with proper authorization.
			if ( wp_verify_nonce( $_POST[ __CLASS__ ], __CLASS__ . $postId ) ) {

				// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

				} else {

					if ( current_user_can( 'edit_post', $postId ) ) {

						if ( get_post_type( $postId ) !== EUCookieLaw3::POST_SLUG ) {

							self::set( EUCookieLaw3::MD_DISABLED, isset( $_POST[ EUCookieLaw3::MD_DISABLED ] ) );
						}
					}

				}
			}
		}


	}

	/**
	 * è il metodo di callback agganciato alla creazione del custom post
	 */
	public function manageMetaboxes( $post ) {

		add_meta_box( 'div-consent-data-' . EUCookieLaw3::POST_SLUG, __( 'Consent Data', EUCookieLaw3::LANG_DOMAIN ), [
			$this,
			'addConsentMetabox',
		], EUCookieLaw3::POST_NAME, 'normal', 'high' );


		if ( $post->post_type !== 'eucookielaw' ) {
			add_meta_box( 'div-status-' . EUCookieLaw3::POST_SLUG, __( 'EUCookieLaw status', EUCookieLaw3::LANG_DOMAIN ), [
				$this,
				'enablingMetabox',
			], null, 'side', 'high' );
		}

	}


	public function enablingMetabox( $post ) {
		$this->buildFormMetaBox( $post, [
			[
				'name' => __( 'Disable' ),
				'id'   => EUCookieLaw3::MD_DISABLED,
				'desc' => __( "Checking the box above, you can disable EUCookieLaw to work on this specific content", EUCookieLaw3::LANG_DOMAIN ),
				'type' => 'checkbox',
			],
		], true, false );

	}

	/**
	 * Aggiunge il metabox per il consenso
	 *
	 * @param $post
	 */
	public function addConsentMetabox( $post ) {
		$this->buildFormMetaBox( $post, [
			[
				'name' => 'Global Unique Identifier',
				'id'   => EUCookieLaw3::MD_CONSENTID,
				'type' => 'text',
			],
			[
				'name' => 'Service type',
				'id'   => EUCookieLaw3::MD_SERVICE,
				'type' => 'text',
				'desc' => 'This information is related to services your site is serving to the users',
			],
			[
				'name' => 'Service type',
				'id'   => EUCookieLaw3::MD_STATUS,
				'type' => 'text',
				'desc' => 'Is the record for consent (<code>OK</code>) or rejection (<code>KO</code>)',
			],
			[
				'name' => 'Action date',
				'id'   => EUCookieLaw3::MD_WHEN,
				'type' => 'text',
				'desc' => 'This information is related to services your site is serving to the users',
			],
		], true, true );

	}

}
