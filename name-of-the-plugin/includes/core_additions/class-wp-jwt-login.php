<?php if(!defined('ABSPATH')) { die(); }
// Exit if accessed directly

use \Firebase\JWT\JWT;

if ( !class_exists( 'NGRIFFIN_PLUGIN_JWTLOGIN' ) )
{
    class NGRIFFIN_PLUGIN_JWTLOGIN
    {
        /**
         * Function handles extracting the Authorization headers from the Request Headers. This
         * is used to the json web token authorization process.
         * */
        private static function NGRIFFIN_PLUGIN_GETAUTH() {
            $headers = null;
            if ( isset( $_SERVER['Authorization'] ) ) {
                $headers = trim( $_SERVER['Authorization'] );
            } elseif ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
                $headers = trim( $_SERVER['HTTP_AUTHORIZATION'] );
            } elseif ( function_exists( 'apache_request_headers' ) ) {
                $request_headers = apache_request_headers();
                $request_headers = array_combine( array_map( 'ucwords', array_keys( $request_headers ) ), array_values( $request_headers ) );
                if ( isset( $request_headers['Authorization'] ) ) {
                    $headers = trim( $request_headers['Authorization'] );
                }
            }
            return $headers;
        }

        /**
         * Function attempls to find the Bearer Token in the Authorization header. If found,
         * this function will return the JWT it finds or null if no JWT is found.
         *
         * @param [string] $headers Authorization Header.
         * @return void|string
         */
        private static function NGRIFFIN_PLUGIN_GETBEARERTOKEN( $headers ) {
            // HEADER: Get the access token from the header.
            if ( ! empty( $headers ) ) {
                if ( preg_match( '/Bearer\s((.*)\.(.*)\.(.*))/', $headers, $matches ) ) {
                    return $matches[1];
                }
            }
            return null;
        }

        /**
         * Function is used during authenticated requests to validate a json web token.
         *
         * @param [string] $header Authorization Header string.
         * @return WP_Error|boolean
         */
        private static function NGRIFFIN_PLUGIN_VALIDATETOKEN( $header ) {
            $key = NGRIFFIN_PLUGIN_GETBEARERTOKEN( $header );
            if ( $key ) {
                try {
                    /*
                    * split the key
                    */
                    list($jwt, $consumerKey) = explode(":", $key);
                    global $wpdb;
                    $now = strtotime("now");
                    $secret_data = $wpdb->get_row( $wpdb->prepare( "SELECT secret_key	FROM {$wpdb->prefix}oauth_clients WHERE consumer_key = %s", $consumerKey ) );
                    if ($secret_data) {
                        $secret = $secret_data->secret_key;
                        $token = JWT::decode( $jwt, $secret, array( 'HS256' ) );
                        if ($token->exp < $now) {
                            return 'NGRIFFIN_PLUGIN_VALIDATETOKEN: JWT token has expired';
                        }
                    } else {
                        return 'invalid';
                    }
                } catch ( \Exception $e ) {
                    /*
                    * the token was not able to be decoded.
                    * this is likely because the signature was not able to be verified (tampered token)
                    */
                    return 'NGRIFFIN_PLUGIN_VALIDATETOKEN: Unable to validate JWT Token';
                }
            } else {
                /*
                * The request lacks the authorization token
                */
                return 'NGRIFFIN_PLUGIN_VALIDATETOKEN: No JWT Token';
            }
            return true;
        }

        /**
         * Function hooks into the get current users process used by the Rest API and validates the
         * current users by extracting the user_id from a json web token. This code was primarily lifted from the
         * basic auth plugin. So Props to them for the over process.
         *
         * @param [object] $user WP User Object
         * @return void|int
         */
        public static function NGRIFFIN_PLUGIN_JWTAUTHHANDLER(  ) {
            $headers = NGRIFFIN_PLUGIN_GETAUTH();
            if ( ! $headers && ! isset($_COOKIE['JWT_key'])) {
                if (is_user_logged_in()) {
                    wp_redirect( NGRIFFIN_PLUGIN_PLUGIN_DASHBOARD_PATH );
                } else {
                    return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: No cookie or header was supplied';
                }
            }

            if ( $headers && ! isset($_COOKIE['JWT_key']) ) {
                $token = NGRIFFIN_PLUGIN_VALIDATETOKEN( $headers );
            } else {
                if (($pos = strpos($key, ":")) !== FALSE) {
                    list($jwt, $consumerKey) = explode(":", $key);
                    global $wpdb;
                    $now = strtotime("now");
                    $secret_data = $wpdb->get_row( $wpdb->prepare( "SELECT secret_key	FROM {$wpdb->prefix}oauth_clients WHERE consumer_key = %s", $consumerKey ) );
                    if ($secret_data) {
                        $secret = $secret_data->secret_key;
                        $token = JWT::decode( $jwt, $secret, array( 'HS256' ) );
                        if ($token->exp < $now) {
                            return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: JWT Token has expired';
                        }
                    } else {
                        return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: Invalid JWT Token supplied';
                    }
                } else {
                    return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: No secret key was provided';
                }
            }
            if  ($token) {
                if ( is_wp_error( $token ) ) {
                    return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: Error with JWT token';
                }
                wp_clear_auth_cookie();

                wp_set_current_user($token->userId, $token->name);
                wp_set_auth_cookie($token->userId);
                do_action('wp_login', $token->name);
                wp_redirect( NGRIFFIN_PLUGIN_PLUGIN_DASHBOARD_PATH );

                return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: Completed';
            } else {
                return 'NGRIFFIN_PLUGIN_JWTAUTHHANDLER: Error with JWT token';
            }
        }
    }
}
