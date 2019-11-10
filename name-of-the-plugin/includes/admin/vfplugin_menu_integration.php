<?php
if (!defined('ABSPATH')) exit;
// Exit if accessed directly

if ( !class_exists( 'VFPLUGIN_WP_MENU' ) ) {

    /**
     *
     */
    class VFPLUGIN_WP_MENU
    {

        function __construct()
        {
            add_action( 'admin_menu', array( $this, 'vfplugin_integration_menu' ) );
        }

        function vfplugin_integration_menu()
        {

            add_menu_page( 'VF Plugin', 'VF Plugin', 'manage_options', 'vfplugin-integration', array( $this, 'wp_vfplugin_integration' ), '', 55 );

            $hook = add_submenu_page( 'vfplugin-integration', 'vfplugin Integration', 'Apps', 'manage_options', 'vfplugin-integration', array( $this, 'wp_vfplugin_integration' ) );

            add_submenu_page( 'vfplugin-integration', 'Add App', 'Add New App', 'manage_options', 'add-app', array( $this, 'vfplugin_add_integration' ) );

            add_action( "load-$hook", array( $this, 'vfplugin_add_rule_screen_option' ) );

			add_filter( 'set-screen-option', array( $this, 'vfplugin_set_options' ), 10, 3 );

            add_action( 'wksjs_add_edit_app_app_edit', array( $this, 'vfplugin_add_sso_app' ) );

            add_action( 'wksjs_add_edit_app_keys', array( $this, 'vfplugin_app_keys' ) );

        }

        function vfplugin_add_rule_screen_option() {

            $options = 'per_page';

            $args = array(
                'label' => 'Product Per Page',
                'default' => 20,
                'option' => 'product_per_page'
            );

            add_screen_option( $options, $args );

    	}

        function vfplugin_set_options($status, $option, $value) {

            return $value;

        }

        function wp_vfplugin_integration()
        {
            echo '<div class="wrap">';

                echo '<h1 class="wp-heading-inline">Apps</h1>';

                echo '<a href="admin.php?page=add-app" class="page-title-action">Add New</a>';

                require_once(sprintf("%s/vfplugin_app_key_list.php", dirname(__FILE__)));

            echo '</div>';
        }

        function vfplugin_add_integration()
        {
            echo '<div class="wrap">';
            echo '<nav class="nav-tab-wrapper">';
            if ( isset( $_GET['app_id'] ) ) :
                echo '<h1 class="wp-heading-inline">Edit App Details</h1>';
                $wk_tabs = array(

                  'app_edit'	=>	__('Edit'),
                  'keys'	=>	__('Keys')

                );
            else :
                echo '<h1 class="wp-heading-inline">Add New App</h1>';
                $wk_tabs = array(

                  'app_edit'	=>	__('Add'),

                );
            endif;

            echo '<p>App Information</p>';

              $current_tab = empty( $_GET['tab'] ) ? 'app_edit' : sanitize_title( $_GET['tab'] );
              $pid = empty( $_GET['app_id'] ) ? '' : '&app_id='.$_GET['app_id'];

              foreach ( $wk_tabs as $name => $label ) {

                echo '<a href="' . admin_url( 'admin.php?page=add-app'.$pid.'&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

              }
            ?>

            </nav>

            <h1 class="screen-reader-text"><?php echo esc_html( $wk_tabs[ $current_tab ] ); ?></h1>

            <?php

            do_action( 'wksjs_add_edit_app_' . $current_tab );

            echo '</div>';

        }

        function vfplugin_add_sso_app()
        {
            require_once(sprintf("%s/vfplugin_add_app_page.php", dirname(__FILE__)));
            $ob = new vfplugin_App_Init();
            $ob->vfplugin_sso_add_app();
        }

        function vfplugin_app_keys()
        {
            require_once(sprintf("%s/vfplugin_app_keys.php", dirname(__FILE__)));
            $ob = new vfplugin_App_Keys();
            $ob->vfplugin_show_sso_app_keys();
        }

    }

    new VFPLUGIN_WP_MENU();

}
