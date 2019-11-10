<?php
if (!defined('ABSPATH')) exit;
// Exit if accessed directly

use TheIconic\Tracking\GoogleAnalytics\Analytics;

$analytics = new Analytics();

if ( !class_exists( 'NGRIFFIN_PLUGIN_REST_ANALYTICS' ) )
{
	class NGRIFFIN_PLUGIN_REST_ANALYTICS
	{
        public static function NGRIFFIN_PLUGIN_REST_ANALYTICS_PUSH_EVENT_LISTENER() {
            if (isset($_GET['trackingID'])) {
                $trackingID_param = $_GET['trackingID'];
            } else {
                $userID_param = false;
            }

            if (isset($_GET['userID'])) {
                $userID_param = $_GET['userID'];
            } else {
                $userID_param = false;
            }

            $trackingID = $trackingID_param;
            $clientID = $userID_param;
            $eventCategory = '';
            $eventAction = '';

            self::NGRIFFIN_PLUGIN_REST_ANALYTICS_PUSH_EVENT($trackingID, $clientID, $eventCategory, $eventAction);
        }

		private static function NGRIFFIN_PLUGIN_REST_ANALYTICS_PUSH_EVENT($trackingID, $clientID, $eventCategory, $eventAction) {
            $analytics->setProtocolVersion('1')
                ->setTrackingId($trackingID)
                ->setClientId($clientID);
            
            $analytics->setEventCategory($eventCategory)
                ->setEventAction($eventAction)
                ->sendEvent();

			$success = 'true';
			$message = 'This API appears to be working well.';
			$json_return = compact( 'success', 'message' );
			return json_encode( array( $json_return ) );
        }
        
        public static function NGRIFFIN_PLUGIN_tracking_init_pageview( $page_id ) {
            if ($page_id) {
                $ping_post_id = $page_id;
            } else {
                $ping_post_id = false;
            }
        
            if ($ping_post_id) {
                global $wpdb;
    
                // get the local time based off WordPress setting
                $nowisnow = date('Y-m-d');
    
                // first try and update the existing total post counter
                $results = $wpdb->query( $wpdb->prepare( "UPDATE ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_total SET postcount = postcount+1 WHERE postnum = '%s' LIMIT 1", $ping_post_id ) );
    
                // Get the user's cookie
                $ping_user_id = User_Data::user_id_handeler();
    
                // if it doesn't exist, then insert two new records
                // one in the total views, another in today's views
                if ($results == 0) {
                    $wpdb->query( 
                        $wpdb->prepare( 
                            "INSERT INTO ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_total (postnum, postcount) VALUES ('%s', 1)", $ping_post_id 
                        )
                    );
                    $wpdb->query(
                        $wpdb->prepare ( 
                            "INSERT INTO ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $ping_post_id 
                            ) 
                        );
    
                    $success = 'true';
                    $message = 'New page tracking started';
                    $error = compact( 'success', 'message' );
                    return json_encode( array( $error ) );
                } else {
                    $results2 = $wpdb->query( 
                        $wpdb->prepare ( 
                            "UPDATE ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_daily SET postcount = postcount+1 WHERE time = '%s' AND postnum = '%s' LIMIT 1", $nowisnow, $ping_post_id 
                        ) 
                    );
                    // insert a new record since one hasn't been created for current day
                    if ($results2 == 0)
                        $wpdb->query( 
                            $wpdb->prepare( 
                                "INSERT INTO ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_daily (time, postnum, postcount) VALUES ('%s', '%s', 1)", $nowisnow, $ping_post_id 
                            ) 
                        );
    
                    $success = 'true';
                    $message = 'Page view tracked';
                    $error = compact( 'success', 'message' );
                    return json_encode( array( $error ) );
                }
            } else {
                $success = 'false';
                $message = 'Please provide the correct information to register a ping!';
                $error = compact( 'success', 'message' );
                return json_encode( array( $error ) );
            }
        }
        public static function NGRIFFIN_PLUGIN_tracking_init_pageview_rest() {
            if (isset($_GET['p'])) {
                $ping_post_id = $_GET['p'];
            } else {
                $ping_post_id = false;
            }
    
            if ($ping_post_id) {
                return self::NGRIFFIN_PLUGIN_tracking_init_pageview($ping_post_id);
            } else {
                $success = 'false';
                $message = 'Please provide the correct information';
                $error = compact( 'success', 'message' );
                return json_encode( array( $error ) );
            }
        }
    
        public static function NGRIFFIN_PLUGIN_tracking_get_init_pageview( $page_id ) {
            if ($page_id) {
                $ping_post_id = $page_id;
            } else {
                $ping_post_id = false;
            }
        
            if ($ping_post_id) {
                // get all the post view info to display
                $today = self::NGRIFFIN_PLUGIN_tracking_fetch_post_today( $ping_post_id );
                $total = self::NGRIFFIN_PLUGIN_tracking_fetch_post_total( $ping_post_id );
    
                $post_counts        = new stdClass();
                $post_counts->total = 0;
                $post_counts->today = 0;
        
                if ( ! empty( $total ) ) {
                    $post_counts->total = $total;
                }
        
                if ( ! empty( $today ) ) {
                    $post_counts->today = $today;
                }
        
                return $post_counts;
            } else {
                $success = 'false';
                $message = 'Please provide the correct information to register a ping!';
                $error = compact( 'success', 'message' );
                return json_encode( array( $error ) );
            }
        }

        public static function NGRIFFIN_PLUGIN_tracking_get_init_pageview_rest() {
            if (isset($_GET['p'])) {
                $ping_post_id = $_GET['p'];
            } else {
                $ping_post_id = false;
            }
    
            if ($ping_post_id) {
                return self::NGRIFFIN_PLUGIN_tracking_get_init_pageview($ping_post_id);
            } else {
                $success = 'false';
                $message = 'Please provide the correct information';
                $error = compact( 'success', 'message' );
                return json_encode( array( $error ) );
            }
        }
    
        public static function NGRIFFIN_PLUGIN_tracking_fetch_post_today( $page_ids ) {
            global $wpdb;
            $nowisnow = date('Y-m-d');
    
            if ( !is_array( $page_ids ) ) $page_ids = array( $page_ids );
    
            $sql = $wpdb->prepare( "SELECT t.postnum AS page_id, t.postcount AS total, d.postcount AS today FROM ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_total AS t
                LEFT JOIN ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_daily AS d ON t.postnum = d.postnum
                WHERE t.postnum IN ( ".implode( ',', $page_ids )." ) AND d.time = %s", $nowisnow );
            return $wpdb->get_results($sql);
        }
    
        public static function NGRIFFIN_PLUGIN_tracking_fetch_post_total( $page_id ) {
            global $wpdb;
    
            $sql = $wpdb->prepare( "SELECT postcount AS total FROM ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_total
                WHERE postnum = %s", $page_id );
            return $wpdb->get_var($sql);
        }
	}
}
