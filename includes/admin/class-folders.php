<?php

/**
 * Render List of Folders for Staging Process
 *
 * @author Rene Hermenau
 * 
 */
class WPSTG_Folders {
    
    public function __construct(){
        add_action('wp_ajax_wpstg_scanning', array($this,'wpstg_scan_files'));
    }
    
    public function wpstg_scan_files($path, &$folders = array()) {
	global $all_files, $wpstg_clone_details, $wpstg_options;
    
        wp_die('test2');
    }
    
}

$wpstg_file_sync = new WPSTG_Folders();

