<?php
/*
Plugin Name: UK Cookie Consent
Plugin URI: http://catapultdesign.co.uk/plugin/uk-cookie-consent/
Description: Simple plug-in to help compliance with the UK interpretation of the EU regulations regarding usage of website cookies. A user to your site is presented with a clear yet unobtrusive notification that the site is using cookies and may then acknowledge and dismiss the notification or click to find out more. The plug-in does not disable cookies on your site or prevent the user from continuing to browse the site - it comes with standard wording on what cookies are and advice on how to disable them in the browser. The plug-in follows the notion of "implied consent" as described by the UK's Information Commissioner and makes the assumption that most users who choose not to accept cookies will do so for all websites.
Author: Catapult
Version: 1.31
Author URI: http://catapultdesign.co.uk/
*/

$options = get_option('catapult_cookie_options');

$wp_content_url = get_option( 'siteurl' ) . '/wp-content';
$wp_plugin_url = plugins_url() . '/catapult-cookie-consent';

//Add an option page for the settings
add_action('admin_menu', 'catapult_cookie_plugin_menu');
function catapult_cookie_plugin_menu() {
	add_options_page('Cookie Consent', 'Cookie Consent', 'manage_options', 'catapult_cookie_consent', 'catapult_cookie_options_page');
}

function catapult_cookie_options_page() { ?>
	<div class="wrap">
		<h2>UK Cookie Consent</h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div class="meta-box-sortabless">
				<div class="postbox">
					<h3 class="hndle">Your settings</h3>
					<div class="inside">
						<?php //Check to see if the info page has been created
						$options = get_option('catapult_cookie_options');
						$pagename = 'Cookie Policy';
						$cpage = get_page_by_title ( $pagename );
						if ( !$cpage ) {
							global $user_ID;
							$page['post_type']    = 'page';
							$page['post_content'] = '<p>This site uses cookies - small text files that are placed on your machine to help the site provide a better user experience. In general, cookies are used to retain user preferences, store information for things like shopping carts, and provide anonymised tracking data to third party applications like Google Analytics.</p>
							<p>As a rule, cookies will make your browsing experience better. However, you may prefer to disable cookies on this site and on others. The most effective way to do this is to disable cookies in your browser. We suggest consulting the Help section of your browser or taking a look at <a href="http://www.aboutcookies.org">the About Cookies website</a> which offers guidance for all modern browsers.</p>';
							$page['post_parent']  = 0;
							$page['post_author']  = $user_ID;
							$page['post_status']  = 'publish';
							$page['post_title']   = $pagename;
							$pageid = wp_insert_post ( $page );
							if ( $pageid == 0 ) {
								echo '<div class="updated settings-error">Failed to create page.</div>';
							} else {
								echo '<div class="updated">Cookie Policy page successfully created.</div>';
							}
						} ?>
						<form action="options.php" method="post">				
							<?php settings_fields('catapult_cookie_options'); ?>
							<?php do_settings_sections('catapult_cookie'); ?>
							<input name="cat_submit" type="submit" id="submit" class="button-primary" style="margin-top:30px;" value="<?php esc_attr_e('Save Changes'); ?>" />
							<?php $options = get_option('catapult_cookie_options');
							$value = htmlentities ( $options['catapult_cookie_link_settings'], ENT_QUOTES );
							if ( !$value ) {
								$value = 'cookie-policy';
							} ?>
							<p>Your Cookies Policy page is <a href="<?php bloginfo ( 'url' ); ?>/<?php echo $value; ?>/">here</a>. You may wish to create a menu item or other link on your site to this page.</p>
						</form>
					</div>
				</div>
			</div>
			<div class="meta-box-sortabless ui-sortable" style="position:relative;">
					<div class="postbox">
						<h3 class="hndle">Resources</h3>
						<div class="inside">
							<p><a href="http://www.ico.gov.uk/for_organisations/privacy_and_electronic_communications/the_guide/cookies.aspx">Information Commissioner's Office Guidance on Cookies</a></p>
							<p><a href="http://www.aboutcookies.org/default.aspx">AboutCookies.org</a></p>
							<p><a href="http://catapultdesign.co.uk/uk-cookie-consent/">Our interpretation of the guidance</a></p>
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
	add_settings_field('catapult_cookie_text', 'Notification text', 'catapult_cookie_text_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_accept', 'Accept text', 'catapult_cookie_accept_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_more', 'More info text', 'catapult_cookie_more_settings', 'catapult_cookie', 'catapult_cookie_main' );
	add_settings_field('catapult_cookie_link', 'Info page permalink', 'catapult_cookie_link_settings', 'catapult_cookie', 'catapult_cookie_main' );
}

function catapult_cookie_section_text() {
	echo '<p>You can just use these settings as they are or update the text as you wish. We recommend keeping it brief.</p><p>The plug-in automatically creates a page called "Cookie Policy" and sets the default More Info link to yoursitename.com/cookie-policy.</p><p>If you find the page hasn\'t been created, hit the Save Changes button on this page.</p><p>If you would like to change the permalink, just update the Info page permalink setting, e.g. enter "?page_id=4" if you are using the default permalink settings (and 4 is the id of your new Cookie Policy page).</p><p>For any support queries, please post on the <a href="http://wordpress.org/extend/plugins/uk-cookie-consent/">WordPress forum</a>.</p><p><strong>And if this plug-in has been helpful to you, then <a href="http://wordpress.org/extend/plugins/uk-cookie-consent/">please rate it</a>.</strong></p>';
}

function catapult_cookie_text_settings() {
	$options = get_option( 'catapult_cookie_options' );
	$value = htmlentities ( $options['catapult_cookie_text_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = 'This site uses cookies';
	}
	echo "<input id='catapult_cookie_text_settings' name='catapult_cookie_options[catapult_cookie_text_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_accept_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_accept_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = 'No problem';
	}
	echo "<input id='catapult_cookie_accept_settings' name='catapult_cookie_options[catapult_cookie_accept_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_more_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_more_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = 'More info';
	}
	echo "<input id='catapult_cookie_more_settings' name='catapult_cookie_options[catapult_cookie_more_settings]' size='50' type='text' value='{$value}' />";
}
function catapult_cookie_link_settings() {
	$options = get_option('catapult_cookie_options');
	$value = htmlentities ( $options['catapult_cookie_link_settings'], ENT_QUOTES );
	if ( !$value ) {
		$value = 'cookie-policy';
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

// Enqueue jquery and styles if the cookie is not set
function catapult_cookie_jquery() {
    wp_enqueue_script( 'jquery' );
	$url_path = plugins_url( str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) ) );
	if ( ! isset( $_COOKIE['catAccCookies'] ) ) {
		wp_enqueue_style( 'catapult_cookie_css', $url_path . 'css/uk-cookie-consent.css', '', '', 'screen' );
	}
}     
add_action('wp_enqueue_scripts', 'catapult_cookie_jquery');

//Add CSS and JS if the cookie is not set
function catapult_add_cookie_css() {
	if ( !isset ( $_COOKIE["catAccCookies"] ) ) {
		echo '<script type="text/javascript">
			function catapultAcceptCookies() {
				days = 30;
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
				document.cookie = "catAccCookies=true"+expires+"; path=/";
				jQuery("#catapult-cookie-bar").hide();
				jQuery("html").css("margin-top","0");
			}		
		</script>';
	}
}
add_action ( 'wp_head', 'catapult_add_cookie_css' );

//Add the notification bar if the cookie is not set
function catapult_add_cookie_bar() {
	if ( !isset ( $_COOKIE["catAccCookies"] ) ) {
		$options = get_option('catapult_cookie_options');
		if ( $options['catapult_cookie_text_settings'] ) {
			$current_text = $options['catapult_cookie_text_settings'];
		} else {
			$current_text = "This site uses cookies";
		}
		if ( $options['catapult_cookie_accept_settings'] ) {
			$accept_text = $options['catapult_cookie_accept_settings'];
		} else {
			$accept_text = "Okay, thanks";
		}
		if ( $options['catapult_cookie_more_settings'] ) {
			$more_text = $options['catapult_cookie_more_settings'];
		} else {
			$more_text = "Find out more";
		}
		if ( $options['catapult_cookie_link_settings'] ) {
			$link_text = strtolower ( $options['catapult_cookie_link_settings'] );
		} else {
			$link_text = "cookie-policy";
		}
		echo '<div id="catapult-cookie-bar">' . htmlspecialchars ( $current_text ) . '<button id="catapultCookie" onclick="catapultAcceptCookies()">' . htmlspecialchars ( $accept_text ) . '</button><a href="' . get_bloginfo ( 'url' ) . '/' . $link_text . '/">' . htmlspecialchars ( $more_text ) . '</a></div>';
	}
}
add_action ( 'wp_footer', 'catapult_add_cookie_bar', 1000 );


