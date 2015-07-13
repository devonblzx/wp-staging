<?php
/**
 * Staging functions
 *
 * @package     WPSTG
 * @subpackage  includes/staging-functions
 * @copyright   Copyright (c) 2015, Rene Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if website is a clone or not. 
 * If it' s a clone website we allow access to the frontend only for administrators.
 * Most of WP is loaded at this stage, and the user is authenticated. 
 * 
 * @return string wp_die()
 */
function wpstg_staging_permissions(){
    if ( !wpstg_is_staging_site() ){
        if ( !current_user_can( 'administrator' ) && !wpstg_is_login_page() && !is_admin() )
         wp_die( sprintf ( __('Access denied. <a href="%1$s" target="_blank">Login</a> first','wpstg'), './wp-admin/' ) );
    }
}
add_action( 'init', 'wpstg_staging_permissions' );

/**
 * Inject custom header for staging website
 * 
 * @deprecated since version 0.2
 */
/*function wpstg_inject_header(){
    if ( !wpstg_is_staging_site() ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
            var struct='<div id="wpstg_staging_header" style="display:block;position:fixed;background-color:#c12161;color:#fff;height:32px;top:0;left:0;width:100%;">Staging website!</div>';
            function 
            jQuery('body').append(struct);
        })
        </script>
            <?php
    }
}*/
//add_action('wp_head','wpstg_inject_header');

/**
 * Change admin_bar site_name
 * 
 * @global object $wp_admin_bar
 * @return void
 */
function wpstg_change_adminbar_name() {
    global $wp_admin_bar;
    if (!wpstg_is_staging_site()) {
        // Main Title
        $wp_admin_bar->add_menu(array(
            'id' => 'site-name',
            'title' => is_admin() ? ('STAGING - ' . get_bloginfo( 'name' ) ) : ( 'STAGING ' . get_bloginfo( 'name' ) . ' Dashboard' ),
            'href' => is_admin() ? home_url('/') : admin_url(),
        ));
        //Add a link called 'My Link'...
	/*$wp_admin_bar->add_node(array(
		'id'    => 'my-link',
		'title' => 'My Link',
		'href'  => admin_url()
	));*/
    }
}
add_filter('wp_before_admin_bar_render', 'wpstg_dashboard_sitename');

/**
 * Check if current wordpress instance is the main site or a clone
 * 
 * @global array $wpstg_options options
 * @return bool true if current website is a staging website
 */
function wpstg_is_staging_site(){
    global $wpstg_options;
    $is_staging_site = isset($wpstg_options['wpstg_is_staging_site']) ? $wpstg_options['wpstg_is_staging_site'] : 'false';
    
    if ($is_staging_site === 'true'){
        return;
    }
}

/**
 * Change permalink structure of the clone
 * 
 * @global array $wpstg_options options
 */
function wpstg_change_permalinks(){
    global $wpstg_options;
}

/**
 * Check if current page is a login page
 * 
 * @return bool true if page is login page
 */
function wpstg_is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}