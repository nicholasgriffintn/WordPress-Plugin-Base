<?php
/*
Plugin Name:        Vacancy Filler Plugin
Plugin URI:         https://vacancy-filler.co.uk
Description:        Plugin for Vacancy Filler's site
Version:            3.0
Author:             Accrosoft
Author URI:         https://accrosoft.com
*/

/* Set plugin variables */
define('NGRIFFIN_PLUGIN_PLUGIN_FRAMEWORK_PATH', plugin_dir_path(__FILE__));
define('NGRIFFIN_PLUGIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('NGRIFFIN_PLUGIN_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NGRIFFIN_PLUGIN_PLUGINURL_PLUGIN', get_template_directory_uri() . '/libs/');
define('NGRIFFIN_PLUGIN_IMAGES_PLUGIN', NGRIFFIN_PLUGIN_PLUGINURL_PLUGIN . 'images/');
define('NGRIFFIN_PLUGIN_JS_PLUGIN', NGRIFFIN_PLUGIN_PLUGINURL_PLUGIN . 'js/');
define('NGRIFFIN_PLUGIN_CSS_PLUGIN', NGRIFFIN_PLUGIN_PLUGINURL_PLUGIN . 'css/');

define('NGRIFFIN_PLUGIN_PLUGIN_DASHBOARD_PATH', '/wp-admin');
define('NGRIFFIN_PLUGIN_PLUGIN_LOGIN_PATH', '/wp-login.php');

/* Require Composer Autoloader */
require_once (__DIR__.'/vendor/autoload.php');

/**
 * Create tables and flush the rewrite rules on activation.
 */
function NGRIFFIN_PLUGIN_activation()
{
    flush_rewrite_rules();
    require_once(sprintf("%s/db_install.php", dirname(__FILE__)));
}
register_activation_hook(__FILE__, 'NGRIFFIN_PLUGIN_activation');

function init_NGRIFFIN_PLUGIN_files() {
	/**
	 * Include admin page additions
	 */
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/admin/plugin_menu_integration.php';
	
	/**
	 * Include tracker code
	 */
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/tracker_code/plugin_tracking_funcs.php';

	/**
	 * Include rest additions
	 */
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/rest_additions/plugin_base_api.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/rest_additions/plugin_analytics_api.php';
	
	/**
	 * Include core additions
	 */
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-general-functions.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-generate-token.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-jwt-login.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-rest.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-rest-cache.php';
	require NGRIFFIN_PLUGIN_PLUGIN_PATH . '/includes/core_additions/class-wp-sso.php';
	
}

init_NGRIFFIN_PLUGIN_files();

?>