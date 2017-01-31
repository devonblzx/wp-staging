<?php

/**
 * Upgrade Functions
 *
 * @package     WPSTG
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, Ren´é Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Perform automatic upgrades when necessary
 *
 * @since 1.2.3
 * @return void
 */
function wpstg_do_automatic_upgrades() {

    $did_upgrade = false;
    $wpstg_version = preg_replace( '/[^0-9.].*/', '', get_option( 'wpstg_version' ) );

    if( version_compare( $wpstg_version, '1.1.2', '<' ) ) {
        wpstg_update_v1();
    }

    // Check if version number in DB is lower than version number in current plugin
    if( version_compare( $wpstg_version, WPSTG_VERSION, '<' ) ) {
        // Let us know that an upgrade has happened
        $did_upgrade = true;
    }

    // Update Version number
    if( $did_upgrade ) {
        update_option( 'wpstg_version', preg_replace( '/[^0-9.].*/', '', WPSTG_VERSION ) );

    }
}

add_action( 'admin_init', 'wpstg_do_automatic_upgrades' );

/**
 * Store default settings which were not stored properly in version earlier than 1.1.2 
 */
function wpstg_update_v1() {
    // Add plugin installation date and variable for rating div
    add_option( 'wpstg_installDate', date( 'Y-m-d h:i:s' ) );
    add_option( 'wpstg_RatingDiv', 'no' );

    // Add First-time variables
    add_option( 'wpstg_firsttime', 'true' );
    add_option( 'wpstg_is_staging_site', 'false' );

    // Show beta notice
    add_option( 'wpstg_hide_beta', 'no' );

    // Create empty config files in /wp-content/uploads/wp-staging
    wpstg_create_remaining_files();
    wpstg_create_clonedetails_files();
}
