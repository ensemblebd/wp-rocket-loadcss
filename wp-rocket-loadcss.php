<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ensemblegroup.net
 * @package           ehnance_wp_rocket_loadCss
 *
 * @wordpress-plugin
 * Plugin Name:       WP Rocket LoadCSS
 * Plugin URI:        https://github.com/ensemblebd/wp-rocket-loadcss
 * Description:       WordPress plugin to quickly modify php output with appropriate loadCSS syntax.
 * Version:           1.5
 * Author:            Ensemble Group
 * Author URI:        https://ensemblegroup.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ehnance-wp-rocket-loadcss
 */
 
defined( 'ABSPATH' ) or die();

if( is_admin() ) {
    require_once('settings.php'); // grab our settings page.
	$my_settings_page = new WPRLCSettingsPage(); // class constructor will run necessary wp actions. We simply construct it.
}


$WPROCKET_ACTIVE_OR_EXISTANT = false;

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
if (is_plugin_active("wp-rocket/wp-rocket.php")) {
	$WPROCKET_ACTIVE_OR_EXISTANT = true;
} else if (defined('WP_ROCKET_VERSION')) {
	$WPROCKET_ACTIVE_OR_EXISTANT = true;
}

$P_OPTIONS = get_option('wprlc_settings');
$OPT_FORCE_EXECUTE = false;
if ($P_OPTIONS !== FALSE && isset($P_OPTIONS['buffer_override']) && 1 == $P_OPTIONS['buffer_override']) {
	$OPT_FORCE_EXECUTE = true;
}


if (!is_admin() && isset($P_OPTIONS) && ($WPROCKET_ACTIVE_OR_EXISTANT || $OPT_FORCE_EXECUTE)) {
	wprlc_dowork($P_OPTIONS, $WPROCKET_ACTIVE_OR_EXISTANT);
}

function wprlc_dowork($P_OPTIONS, $WPR_EXISTS) {
	try {
		$opt_ShouldInjectLoadCSS = ((isset($P_OPTIONS['inject_loadcss'])?$P_OPTIONS['inject_loadcss']:0) == 1);
		$opt_ShouldModifyOutputBuffer = ((isset($P_OPTIONS['modify_output_buffer'])?$P_OPTIONS['modify_output_buffer']:0) == 1);
		$opt_ForceBufferProcessor = ((isset($P_OPTIONS['buffer_override'])?$P_OPTIONS['buffer_override']:0) == 1);
		
		if ($opt_ShouldInjectLoadCSS) {
			add_action('wp_head', 'wprlc_wp_head_inject');
		}
		if ($opt_ShouldModifyOutputBuffer) {
			if (!$WPR_EXISTS && $opt_ForceBufferProcessor) {
				if (substr( $_SERVER['REQUEST_URI'], 0, 4 ) === "/amp") return; // to support amp pages. We should not make any changes in such a case.
				$path = home_url(add_query_arg(null, null));
				$parts = explode(".", $path);
				if (stristr($parts[count($parts) - 1], "xml") === false && stristr($parts[count($parts) - 1], "xsl") === false) { // to support yoast SEO xml sitemap. We should not make any changes, in such a case.
					// if we arrived here, then we are a.) Not an admin page, b.) not an amp page, and c.) not yoast's xml sitemap. Excellent.. lets do it..
					add_action('after_setup_theme', 'wprlc_forcemode_buffer_start', 1);
					add_action('shutdown', 'wprlc_forcemode_buffer_end', 999999999); // large priority in case someone else is doing fancy stuff too.
				}
			} else {
				add_filter('rocket_buffer','wprlc_buffer_post_process', 999999999, 1); // large priority in case someone else is doing fancy stuff too.
			}
		}
	}
	catch(\Exception|\Throwable $e) {
		file_put_contents(dirname(__FILE__).'/error.log', $e->getMessage()."\r\n", FILE_APPEND);
	}
}



function wprlc_wp_head_inject() {
	// we inject with an inline script to ensure that the loadCSS polyfill is IMMEDIATELY available, despite WP Rocket's js combine feature.
	// The impact is significantly minimal, and ensures proper loading of css resources.
	$inline_script = plugin_dir_path(__FILE__).'assets/js/filamentgroup.loadcss.min.js'; 
	if (file_exists($inline_script)) {
		echo '<script type="text/javascript">'.file_get_contents($inline_script).'</script>';
	}
}


function wprlc_buffer_post_process($buffer) {
	// trigger asynchronous css using loadCSS lib. 
	// The loadCSS lib was previously injected into the header, we assume. 
	// Admin of site could have their own version of the lib instead. 
	// If it's not present, then obviously css will never load. We shall assume the admin is a competent admin. 
	try {
		$matches=[];
		$find="(<link\\s+[^>]*rel\\s*=\\s*(['\"])stylesheet\\2.*?>)(?:(?=.*<\\/head))"; // the non capture lookahead is technically non-performant. But performance as measured in ms has been determinedly inconsequential (based on content load of several very large raw html loads), therefore I'm personally quite happy with it.
		preg_match_all("/".$find."/smix",$buffer,$matches,PREG_SET_ORDER);
		foreach($matches as $link) {
			$noscript='<noscript>'.$link[1].'</noscript>';
			$noscript='<noscript>'.$link[1].'</noscript>';
			if (stristr($link[1],'="stylesheet')!==false) $new_link=str_ireplace('="stylesheet','="preload', $link[1]);
			else if (stristr($link[1],"='stylesheet")!==false) $new_link=str_ireplace("='stylesheet","='preload", $link[1]);
			else {
				$new_link=str_ireplace("link ","link rel=\"preload\" ", $link[1]);
			}
			$new_link=str_replace(" rel",' as="style" onload="this.onload=null;this.rel=\'stylesheet\'" rel',$new_link);
			$new_link.=$noscript;
			$buffer=str_replace($link[1],$new_link,$buffer);
		}
	}
	catch(\Exception|\Throwable $e) {
		file_put_contents(dirname(__FILE__).'/error.log', $e->getMessage()."\r\n", FILE_APPEND);
	}
    return $buffer;
}


function wprlc_forcemode_buffer_start() {
    ob_start("wprlc_buffer_post_process");
}
function wprlc_forcemode_buffer_end() {
    ob_end_flush();
}





