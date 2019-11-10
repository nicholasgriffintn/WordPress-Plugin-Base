<?php

if ( !class_exists( 'NGRIFFIN_PLUGIN_Integration_Install' ) )
{

    /**
     *
     */
    class NGRIFFIN_PLUGIN_Integration_Install
    {

        function create_NGRIFFIN_PLUGIN_tables()
        {
            global $table_prefix, $wpdb;
            $collate = '';

            if ( $wpdb->has_cap( 'collation' ) ) {
                if ( !empty($wpdb->charset) ) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if ( !empty($wpdb->collate) ) $collate .= " COLLATE $wpdb->collate";
            }

            $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_daily" ." (
             `id` mediumint(9) NOT NULL AUTO_INCREMENT,
             `time` date DEFAULT '0000-00-00' NOT NULL,
             `postnum` varchar(255) NOT NULL,
             `postcount` int DEFAULT '0' NOT NULL,
             UNIQUE KEY id (id)) $collate;";

            $wpdb->query($sql);

            $sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_total" ." (
                 `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                 `postnum` varchar(255) NOT NULL,
                 `postcount` int DEFAULT '0' NOT NULL,
                 UNIQUE KEY id (id)) $collate;";

			$wpdb->query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "NGRIFFIN_PLUGIN_tracking_user_category" ." (
				`id` mediumint(9) NOT NULL AUTO_INCREMENT,
				`postnum` varchar(255) NOT NULL,
				`postcount` int DEFAULT '0' NOT NULL,
				`userid` varchar(255) NOT NULL,
				`category` varchar(255) NOT NULL,
				UNIQUE KEY id (id)) $collate;";

			$wpdb->query($sql);

            $tblname = 'oauth_clients';

            $oauth_clients_table = $table_prefix . "$tblname ";

            $charset_collate = $wpdb->get_charset_collate();

  	        if( $wpdb->get_var("show tables like '$oauth_clients_table'") != $oauth_clients_table ) {

  	            $sql = "CREATE TABLE $oauth_clients_table (
  	                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
  	                `consumer_key` varchar(80) NOT NULL,
  	                `secret_key` varchar(80) NOT NULL,
  	                `redirect_uri` varchar(2000) NOT NULL,
                    `deny_uri` varchar(2000) NOT NULL,
                    `user_id` bigint(20) NOT NULL,
  	                `status` varchar(20) NOT NULL DEFAULT 'publish',
  	                PRIMARY KEY (id)
  	            ) $charset_collate;";

  	            if ( !function_exists( 'dbDelta' ) ) {
  	                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  	            }

  	            dbDelta( $sql );

  	        }

            $tblname1 = 'oauth_codes';

            $oauth_code_table = $table_prefix . "$tblname1";

            if( $wpdb->get_var("show tables like '$oauth_code_table'") != $oauth_code_table ) {

  	            $sql = "CREATE TABLE $oauth_code_table (
  	                `authcode_id` bigint(20) NOT NULL AUTO_INCREMENT,
  	                `auth_code` varchar(80) NOT NULL,
                    `oauth_consumer_key` varchar(80) NOT NULL,
  	                `user_id` varchar(80) NOT NULL,
  	                `redirect_uri` varchar(2000) NOT NULL,
  	                `expire` bigint(20) NOT NULL,
  	                PRIMARY KEY (authcode_id)
  	            ) $charset_collate;";
                if ( !function_exists( 'dbDelta' ) ) {
  	                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  	            }
  	            dbDelta( $sql );

  	        }

            $tblname2 = 'oauth_jwt_tokens';

            $oauth_jwt_table = $table_prefix . "$tblname2";

            if( $wpdb->get_var("show tables like '$oauth_jwt_table'") != $oauth_jwt_table ) {

  	            $sql = "CREATE TABLE $oauth_jwt_table (
  	                `token_id` bigint(20) NOT NULL AUTO_INCREMENT,
  	                `access_token` varchar(2000) NOT NULL,
                    `oauth_consumer_key` varchar(80) NOT NULL,
                    `oauth_code` varchar(80) NOT NULL,
  	                `user_id` varchar(80) NOT NULL,
  	                `redirect_uri` varchar(2000) NOT NULL,
  	                `expire` bigint(20) NOT NULL,
  	                PRIMARY KEY (token_id)
  	            ) $charset_collate;";
                if ( !function_exists( 'dbDelta' ) ) {
  	                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  	            }
  	            dbDelta( $sql );
            }

        }

    }

    $ob = new NGRIFFIN_PLUGIN_Integration_Install();

    $ob->create_NGRIFFIN_PLUGIN_tables();
}
