<?php if(!defined('ABSPATH')) { die(); }
// Exit if accessed directly

if ( !class_exists( 'NGRIFFIN_PLUGIN_WPREST_CACHE' ) )
{
    class NGRIFFIN_PLUGIN_WPREST_CACHE
    {
        /**
         * Function handles extracting the Authorization headers from the Request Headers. This
         * is used to the json web token authorization process.
         * */
        public static function NGRIFFIN_PLUGIN_CACHED_FILE_API_RESULTS( $cache_file = NULL, $expires = NULL, $callback ) {
            global $request_type, $purge_cache, $limit_reached, $request_limit;

            if( !$cache_file ) {
                $cache_file = dirname(__FILE__) . '/../../cachedAPIS/api-cache-' . NGRIFFIN_PLUGIN_TokenGenerator::NGRIFFIN_PLUGIN_GENERATETOKEN() . '.json';
            } else {
                $cache_file = dirname(__FILE__) . '/../../cachedAPIS/api-cache-' . $cache_file . '.json';
            }
            if( !$expires) $expires = time() - 2*60*60;

            // Check that the file is older than the expire time and that it's not empty
            if (file_exists($cache_file)) {
                if ( filectime($cache_file) < $expires || file_get_contents($cache_file)  == '' || $purge_cache && intval($_SESSION['views']) <= $request_limit ) {
                    // File is too old, refresh cache
                    $api_results = $callback;
                    $json_results = json_encode($api_results);

                    // Remove cache file on error to avoid writing wrong xml
                    if ( $api_results && $json_results )
                        file_put_contents($cache_file, $json_results);
                    else
                        unlink($cache_file);
                } else {
                    // Check for the number of purge cache requests to avoid abuse
                    if( intval($_SESSION['views']) >= $request_limit ) 
                        $limit_reached = " <span class='error'>Request limit reached ($request_limit). Please try purging the cache later.</span>";
                    // Fetch cache
                    $json_results = file_get_contents($cache_file);
                    $request_type = 'JSON';
                }
            } else {
                // File is too new, refresh cache
                $api_results = $callback;
                $json_results = json_encode($api_results);

                // Remove cache file on error to avoid writing wrong xml
                if ( $api_results && $json_results )
                    file_put_contents($cache_file, $json_results);
                else
                    unlink($cache_file);
            }

            return json_decode($json_results);
        }

        public static function NGRIFFIN_PLUGIN_CACHED_TRANSIENT_API_RESULTS( $transient_name = NULL, $expires = NULL, $callback ) {

            // Do we have this information in our transients already?
            $transient = get_transient( $transient_name );
            
            // Yep!  Just return it and we're done.
            if( !empty( $transient ) ) {
              
              // The function will return here every time after the first time it is run, until the transient expires.
              return $transient;
          
            // Nope!  We gotta make a call.
            } else {
            
                $api_results = $callback;
                $api_results = json_decode($api_results);

                if ($api_results) {
                    // Call the API.
                    $json_store = array( 
                        "cachedStatus" => 'Cached in transient',
                        "returnedData" => $api_results
                    );
                    
                    // Save the API response so we don't have to call again until tomorrow.
                    set_transient( $transient_name, json_encode($json_store), 60*60*4 );
                    
                    // Return the list of subscribers.  The function will return here the first time it is run, and then once again, each time the transient expires.
                    $json_results_response = array( 
                        "cachedStatus" => 'Not Cached',
                        "returnedData" => $api_results
                    );
                    return json_encode($json_results_response);
                } else {
                    $json_results_response = array( 
                        "cachedStatus" => 'No Data',
                        "returnedData" => 'NULL'
                    );
                    return json_encode($json_results_response);
                }
              
            }
            
        }
    }
}