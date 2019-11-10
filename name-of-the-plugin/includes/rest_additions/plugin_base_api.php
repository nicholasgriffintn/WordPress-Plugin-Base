<?php
if (!defined('ABSPATH')) exit;
// Exit if accessed directly

if ( !class_exists( 'NGRIFFIN_PLUGIN_REST_BASE' ) )
{
	class NGRIFFIN_PLUGIN_REST_BASE
	{
		public static function NGRIFFIN_PLUGIN_REST_BASE_HEALTH() {
			$success = 'true';
			$message = 'This API appears to be working well.';
			$json_return = compact( 'success', 'message' );
			return json_encode( array( $json_return ) );
		}
	}
}
