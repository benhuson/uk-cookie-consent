<?php
/*
Plugin Name: UK Cookie Consent
Plugin URI: http://catapultdesign.co.uk/plugin/uk-cookie-consent/
Description: Simple plug-in to help compliance with the UK interpretation of the EU regulations regarding usage of website cookies. A user to your site is presented with a clear yet unobtrusive notification that the site is using cookies and may then acknowledge and dismiss the notification or click to find out more. The plug-in does not disable cookies on your site or prevent the user from continuing to browse the site - it comes with standard wording on what cookies are and advice on how to disable them in the browser. The plug-in follows the notion of "implied consent" as described by the UK's Information Commissioner and makes the assumption that most users who choose not to accept cookies will do so for all websites.
Author: Catapult
Version: 1.31
Author URI: http://catapultdesign.co.uk/
*/

// Language
load_plugin_textdomain( 'uk-cookie-consent', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

//Add an option page for the settings
add_action('admin_menu', 'catapult_cookie_plugin_menu');
function catapult_cookie_plugin_menu() {
	add_options_page( __( 'Cookie Consent', 'uk-cookie-consent' ), __( 'Cookie Consent', 'uk-cookie-consent' ), 'manage_options', 'catapult_cookie_consent', 'catapult_cookie_options_page' );
}

function catapult_cookie_options_page() { ?>
	<div class="wrap">
		<h2><?php _e( 'UK Cookie Consent', 'uk-cookie-consent' ); ?></h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div class="meta-box-sortabless">
				<div class="postbox">
					<h3 class="hndle"><?php _e( 'Your settings', 'uk-cookie-consent' ); ?></h3>
					<div class="inside">
						<?php //Check to see if the info page has been created
						$options = get_option('catapult_cookie_options');
						$pagename = __( 'Cookie Policy', 'uk-cookie-consent' );
						$cpage = get_page_by_title ( $pagename );
						if ( !$cpage ) {
							global $user_ID;
							$page['post_type']    = 'page';
							$page['post_content'] = '<p>' . __( 'This site uses cookies - small text files that are placed on your machine to help the site provide a better user experience. In general, cookies are used to retain user preferences, store information for things like shopping carts, and provide anonymised tracking data to third party applications like Google Analytics.', 'uk-cookie-consent' ) . '</p>
								<p>' . __( 'As a rule, cookies will make your browsing experience better. However, you may prefer to disable cookies on this site and on others. The most effective way to do this is to disable cookies in your browser. We suggest consulting the Help section of your browser or taking a look at <a href="http://www.aboutcookies.org">the About Cookies website</a> which offers guidance for all modern browsers.', 'uk-cookie-consent' ) . '</p>';
							$page['post_parent']  = 0;
							$page['post_author']  = $user_ID;
							$page['post_status']  = 'publish';
							$page['post_title']   = $pagename;
							$pageid = wp_insert_post ( $page );
							if ( $pageid == 0 ) {
								echo '<div class="updated settings-error">' . __( 'Failed to create page.', 'uk-cookie-consent' ) . '</div>';
							} else {
								echo '<div class="updated">' . __( 'Cookie Policy page successfully created.', 'uk-cookie-consent' ) . '</div>';
							}
						} ?>
						<form action="options.php" method="post">				
							<?php settings_fields('catapult_cookie_options'); ?>
							<?php do_settings_sections('catapult_cookie'); ?>
							<input name="cat_submit" type="submit" id="submit" class="button-primary" style="margin-top:30px;" value="<?php esc_attr_e( __( 'Save Changes', 'uk-cookie-consent' ) ); ?>" />
							<?php $options = get_option('catapult_cookie_options');
							$value = htmlentities ( $options['catapult_cookie_link_settings'], ENT_QUOTES );
							if ( !$value ) {
								$value = 'cookie-policy';
							} ?>
							<p><?php echo sprintf( __( 'Your Cookies Policy page is <a href="%s">here</a>. You may wish to create a menu item or other link on your site to this page.', 'uk-cookie-consent' ), home_url( $value ) ); ?></p>
						</form>
					</div>
				</div>
			</div>
			<div class="meta-box-sortabless ui-sortable" style="position:relative;">
					<div class="postbox">
						<h3 class="hndle"><?php _e( 'Resources', 'uk-cookie-consent' ); ?></h3>
						<div class="inside">
							<p><a href="http://www.ico.gov.uk/for_organisations/privacy_and_electronic_communications/the_guide/cookies.aspx"><?php _e( 'Information Commissioner\'s Office Guidance on Cookies', 'uk-cookie-consent' ); ?></a></p>
							<p><a href="http://www.aboutcookies.org/default.aspx">AboutCookies.org</a></p>
							<p><a href="http://catapultdesign.co.uk/uk-cookie-consent/"><?php _e( 'Our interpretation of the guidance', 'uk-cookie-consent' ); ?></a></p>
						</div>
					</div>
				</div>
		</div><!-- poststuff -->
	</div>
<?php }

add_action('admin_init', 'catapult_cookie_admin_init');
function catapult_cookie_admin_init(){
	register_setting( 'catapult_cookie_options', 'catapult_cookie_options', 'catapult_cookie_options_validate' );
	add_settings_section('catapult_cookie_main', '', 'catapult_cookie_section_text', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_text', __( 'Notification text', 'uk-cookie-consent' ), 'catapult_cookie_text_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_accept', __( 'Accept text', 'uk-cookie-consent' ), 'catapult_cookie_accept_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_more', __( 'More info text', 'uk-cookie-consent' ), 'catapult_cookie_more_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_link', __( 'Info page permalink', 'uk-cookie-consent' ), 'catapult_cookie_link_settings', 'catapult_cookie', 'catapult_cookie_main' );
}

function catapult_cookie_section_text() {
	echo '<p>' . __( 'You can just use these settings as they are or update the text as you wish. We recommend keeping it brief.', 'uk-cookie-consent' ) . '</p>
		<p>' . __( 'The plug-in automatically creates a page called "Cookie Policy" and sets the default More Info link to yoursitename.com/cookie-policy.', 'uk-cookie-consent' ) . '</p>
		<p>' . __( 'If you find the page hasn\'t been created, hit the Save Changes button on this page.', 'uk-cookie-consent' ) . '</p>
		<p>' . __( 'If you would like to change the permalink, just update the Info page permalink setting, e.g. enter "?page_id=4" if you are using the default permalink settings (and 4 is the id of your new Cookie Policy page).', 'uk-cookie-consent' ) . '</p>
		<p>' . sprintf( __( 'For any support queries, please post on the <a href="%s">WordPress forum</a>.', 'uk-cookie-consent' ), 'http://wordpress.org/extend/plugins/uk-cookie-consent/' ) . '</p>
		<p><strong>' . sprintf( __( 'And if this plug-in has been helpful to you, then <a href="%s">please rate it</a>.', 'uk-cookie-consent' ), 'http://wordpress.org/extend/plugins/uk-cookie-consent/' ) . '</strong></p>';
}

function catapult_cookie_text_settings() {
	$options = get_option( 'catapult_cookie_options' );
	$value = htmlentities ( $options['catapult_cookie_text_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = __( 'This site uses cookies', 'uk-cookie-consent' );
	}
	echo "<input id='catapult_cookie_text_settings' name='catapult_cookie_options[catapult_cookie_text_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_accept_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_accept_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = __( 'No problem', 'uk-cookie-consent' );
	}
	echo "<input id='catapult_cookie_accept_settings' name='catapult_cookie_options[catapult_cookie_accept_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_more_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_more_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = __( 'More info', 'uk-cookie-consent' );
	}
	echo "<input id='catapult_cookie_more_settings' name='catapult_cookie_options[catapult_cookie_more_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_link_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_link_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = __( 'cookie-policy', 'uk-cookie-consent' );
	}
	echo "<input id='catapult_cookie_link_settings' name='catapult_cookie_options[catapult_cookie_link_settings]' size='50' type='text' value='{$value}' />";
}

function catapult_cookie_options_validate($input) {
	$options = get_option( 'catapult_cookie_options' );
	$options['catapult_cookie_text_settings'] = trim($input['catapult_cookie_text_settings']);
	$options['catapult_cookie_accept_settings'] = trim($input['catapult_cookie_accept_settings']);
	$options['catapult_cookie_more_settings'] = trim($input['catapult_cookie_more_settings']);
	$options['catapult_cookie_link_settings'] = trim($input['catapult_cookie_link_settings']);
	return $options;
}

// Enqueue scripts and styles if the cookie is not set
function catapult_cookie_jquery() {
	$url_path = plugins_url( str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) );
	if ( ! isset( $_COOKIE['catAccCookies'] ) ) {
		wp_enqueue_style( 'catapult_cookie_css', $url_path . 'css/uk-cookie-consent.css', '', '', 'screen' );
		wp_enqueue_script( 'catapult_cookie_js', $url_path . 'js/uk-cookie-consent.js', array( 'jquery' ) );
	}
}     
add_action('wp_enqueue_scripts', 'catapult_cookie_jquery');

//Add the notification bar if the cookie is not set
function catapult_add_cookie_bar() {
	if ( !isset ( $_COOKIE["catAccCookies"] ) ) {
		$options = get_option('catapult_cookie_options');
		if ( $options['catapult_cookie_text_settings'] ) {
			$current_text = $options['catapult_cookie_text_settings'];
		} else {
			$current_text = __( 'This site uses cookies', 'uk-cookie-consent' );
		}
		if ( $options['catapult_cookie_accept_settings'] ) {
			$accept_text = $options['catapult_cookie_accept_settings'];
		} else {
			$accept_text = __( 'Okay, thanks', 'uk-cookie-consent' );
		}
		if ( $options['catapult_cookie_more_settings'] ) {
			$more_text = $options['catapult_cookie_more_settings'];
		} else {
			$more_text = __( 'Find out more', 'uk-cookie-consent' );
		}
		if ( $options['catapult_cookie_link_settings'] ) {
			$link_text = strtolower ( $options['catapult_cookie_link_settings'] );
		} else {
			$link_text = "cookie-policy";
		}
		$content = sprintf( '<div id="catapult-cookie-bar">%s<button id="catapultCookie" onclick="catapultAcceptCookies();">%s</button><a href="%s">%s</a></div>', htmlspecialchars( $current_text ), htmlspecialchars( $accept_text ), home_url( $link_text ), htmlspecialchars( $more_text ) );
		echo apply_filters( 'catapult_cookie_content', $content, $options );
	}
}
add_action ( 'wp_footer', 'catapult_add_cookie_bar', 1000 );


