<?php
if (!defined('ABSPATH')) exit;
// Exit if accessed directly

/*
* REST SETUP
*/
/*
* Rename the wp-json API
*/
add_filter( 'rest_url_prefix', 'NGRIFFIN_PLUGIN_RENAME_WP_JSON');
 
function NGRIFFIN_PLUGIN_RENAME_WP_JSON( $slug ) {
    return 'api';
}

/* remove default json apis */
/* add_filter( 'rest_endpoints', 'NGRIFFIN_PLUGIN_REMOVE_DEFAULT_ENDPOINTS' );
  
function NGRIFFIN_PLUGIN_REMOVE_DEFAULT_ENDPOINTS( $endpoints ) {
  $prefix = 'vfplugin';
 
  foreach ( $endpoints as $endpoint => $details ) {
    if ( !fnmatch( '/' . $prefix . '/*', $endpoint, FNM_CASEFOLD ) ) {
      unset( $endpoints[$endpoint] );
    }
  }
 
  return $endpoints;
} */

function internal_api_secure_access() {
    return current_user_can( 'use_internal_apis' );
}
function external_api_secure_access_sso() {
    return current_user_can( 'use_external_apis_sso' );
}
function external_api_secure_access_jobposting() {
    return current_user_can( 'use_external_apis_jobposting' );
}
function external_api_secure_access() {
    return current_user_can( 'use_external_apis' );
}
function external_api_insecure_access() {
    return true;
}

// Enable the option to show ACF in rest
add_filter( 'acf/rest_api/field_settings/show_in_rest', '__return_true' );

// Enable the option to edit ACF in rest
add_filter( 'acf/rest_api/field_settings/edit_in_rest', '__return_true' );

add_action( 'rest_api_init', function () {
    'wp_rest_api_alter';
    // health check API
    register_rest_route( 'vfplugin/v2', '/base/healthcheck/', array(
        'methods' => 'GET',
        'callback' => 'NGRIFFIN_PLUGIN_REST_BASE::NGRIFFIN_PLUGIN_REST_BASE_HEALTH',
        'permission_callback' => 'external_api_insecure_access'
    ) );
    // Hubspot Get Contact API
    register_rest_route( 'vfplugin/v2', '/hubspot/getcontactdetails/', array(
        'methods' => 'GET',
        'callback' => 'NGRIFFIN_PLUGIN_HUBSPOT_API::GET_CONTACT_DETAILS',
        'permission_callback' => 'internal_api_secure_access'
    ) );
});
