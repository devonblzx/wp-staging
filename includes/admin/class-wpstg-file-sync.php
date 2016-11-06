<?php

/**
 * Copy files from staging to live site
 *
 * @author Rene Hermenau
 */
class WPSTG_FileSync {
    
    public function __construct(){
        add_filter('wpstg_before_stage_buttons', array($this, 'create_sync_button'), 10, 2);
        add_action('wp_ajax_render_settings_page', array($this, 'render_settings_page'));
    }
    
    /*
     * Create a sync data button on the list of available staging sites
     */
    public function create_sync_button($content, $clone){
        $content = '<a href="#" class="wpstg-sync-settings wpstg-clone-action" data-clone="' . $clone . '">' . __('Sync Data', 'wpstg') . '</a>';
        return $content;
    }
    
    /**
     * Renders the Sync Data settings page
     * 
     * @return string and die();
     */
    public function render_settings_page(){
        	check_ajax_referer('wpstg_ajax_nonce', 'nonce');
		ob_start();
		include ( WPSTG_PLUGIN_DIR ) . '/views/view-sync-settings.php';
		$html = ob_get_clean();
		echo $html;
                die();
    }
}

$wpstg_file_sync = new WPSTG_FileSync();

