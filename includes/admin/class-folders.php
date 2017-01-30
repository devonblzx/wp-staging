<?php

/**
 * Render List of Folders for Staging Process
 *
 * @author Rene Hermenau
 * 
 */
class wpstgFolders {

    public function __construct() {
        //add_action( 'wp_ajax_wpstg_scanning', array($this, 'wpstg_scan_files') );
    }

    public static function wpstg_scan_files() {
        global $all_files, $wpstg_clone_details, $wpstg_options;

        @set_time_limit( 300 );
        $abs_folder_up = false;
        $abs_path = wpstg_get_clone_root_path();
        $main = 1;

        $folder = $abs_path;

        if( $abs_folder_up ) {
            $abs_path = dirname( $abs_path );
        }
        ?>
        <h3 class="title"><?php esc_html_e( 'Folders to Copy', 'wpstg' ) ?></h3>
        <p></p>
        <table class="wpstg-form-table form-table">
            <tr>
                <th scope="row"><label for="idcopyroot"><?php esc_html_e( 'Main WordPress folder', 'wpstg' ); ?></label></th>
                <td class="wpstg-dir">
                    <input data-wpstg-path="<?php echo $folder; ?>" class="checkbox wpstg-dir wpstg-check-dir" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyroot' ), TRUE, TRUE ); ?> name="copyroot" id="idcopyroot" value="1" /> 
                    <code class="wpstg-expand-dirs" title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), $folder ) ); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo esc_html( self::render_folder_size( $folder, FALSE ) ); ?>
                    <fieldset class="wpstg-dir wpstg-subdir" id="rootexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo self::render_subfolder( $main, $folder, 'copyroot' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopycontent"><?php esc_html_e( 'Content folder', 'wpstg' ); ?></label></th>
                <td class="wpstg-dir">
                    <input data-wpstg-path="<?php echo WP_CONTENT_DIR; ?>" class="checkbox wpstg-dir wpstg-check-dir" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copycontent' ), TRUE, TRUE ); ?> name="copycontent" id="idcopycontent" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), WP_CONTENT_DIR ) ); ?>"><?php echo esc_attr( WP_CONTENT_DIR ); ?></code><?php echo esc_html( self::render_folder_size( WP_CONTENT_DIR, FALSE ) ); ?>
                    <fieldset class="wpstg-dir wpstg-subdir" id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo self::render_subfolder( $main, WP_CONTENT_DIR, 'copycontent' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopyplugins"><?php esc_html_e( 'Plugins Folder', 'wpstg' ); ?></label></th>
                <td class="wpstg-dir">
                    <input data-wpstg-path="<?php echo WP_PLUGIN_DIR; ?>" class="checkbox wpstg-dir wpstg-check-dir" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyplugins' ), TRUE, TRUE ); ?> name="copyplugins" id="idcopyplugins" value="1" /> 
                    <code class="wpstg-expand-dirs" title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), WP_PLUGIN_DIR ) ); ?>"><?php echo esc_attr( WP_PLUGIN_DIR ); ?></code><?php echo esc_html( self::render_folder_size( WP_PLUGIN_DIR, FALSE ) ); ?>
                    <fieldset class="wpstg-dir wpstg-subdir" id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo self::render_subfolder( $main, WP_PLUGIN_DIR, 'copyplugins' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopythemes"><?php esc_html_e( 'Themes folder', 'wpstg' ); ?></label></th>
                <td class="wpstg-dir">
                    <input data-wpstg-path="<?php echo get_theme_root(); ?>" class="checkbox wpstg-dir wpstg-check-dir" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copythemes' ), TRUE, TRUE ); ?> name="copythemes" id="idcopythemes" value="1" /> 
                    <code class="wpstg-expand-dirs" title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), get_theme_root() ) ); ?>"><?php echo esc_attr( get_theme_root() ); ?></code><?php echo esc_html( self::render_folder_size( get_theme_root(), FALSE ) ); ?>
                    <fieldset class="wpstg-dir wpstg-subdir" id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo self::render_subfolder( $main, get_theme_root(), 'copythemes' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopyuploads"><?php esc_html_e( 'Uploads folder', 'wpstg' ); ?></label></th>
                <td class="wpstg-dir">
                    <input data-wpstg-path="<?php echo wpstgFile::get_upload_dir(); ?>" class="checkbox wpstg-dir wpstg-check-dir" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyuploads' ), TRUE, TRUE ); ?> name="copyuploads" id="idcopyuploads" value="1" /> 
                    <code class="wpstg-expand-dirs" title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), wpstgFile::get_upload_dir() ) ); ?>"><?php echo esc_attr( wpstgFile::get_upload_dir() ); ?></code><?php echo esc_html( self::render_folder_size( wpstgFile::get_upload_dir(), FALSE ) ); ?>
                    <fieldset class="wpstg-dir wpstg-subdir" id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo self::render_subfolder( $main, wpstgFile::get_upload_dir(), 'copyuploads' ); ?>
                    </fieldset>
                </td>
            </tr>
        <!--            <tr>
                <th scope="row"><label for="dirinclude"><?php //esc_html_e( 'Extra folders', 'wpstg' );  ?></label></th>
                <td>
                    <textarea name="dirinclude" id="dirinclude" class="text code" rows="7" cols="50"><?php //echo esc_attr( wpstgJobOptions::get( $main, 'dirinclude' ) );  ?></textarea>
                    <p class="description"><?php //esc_attr_e( 'Separate folder names with a line-break or a comma. Folders must be set with their absolute path!', 'backwpup' ) ?></p>
                </td>
            </tr>-->
        </table>
        <?php
    }

    /**
     * Render Subfolder for a specific parent folder. Deepth 1
     * @param int $id job id
     * @param string $folder path
     * @param string $name name of the parent folder
     * @param bool count size of subfolders 
     */
    private static function render_subfolder( $id, $folder, $name ) {
        $folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
        $html = '';
        if( $folder && $dir = opendir( $folder ) ) {
            while ( ( $file = readdir( $dir ) ) !== FALSE ) {
                $excludes = wpstgJobOptions::get( $id, $name . 'excludedirs' );
                if( !in_array( $file, array('.', '..'), true ) && is_dir( $folder . '/' . $file ) && !in_array( trailingslashit( $folder . '/' . $file ), self::get_exclude_dirs( $folder ), true ) ) {
                    $title = '';
                    $html .= '<label for="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '"><input data-wpstg-path="' . $folder . '/' . $file . '" class="checkbox wpstg-dir wpstg-subdir" type="checkbox"' . checked( in_array( $file, $excludes, true ), FALSE, FALSE ) . ' name="' . $name . 'excludedirs[]" id="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '" /> ' . esc_html( $file ) . esc_html( self::render_folder_size( $folder . '/' . $file ) ) . '</label><br />';
                }
            }
            return $html;
            closedir( $dir );
        }
    }

    /**
     * Render Folder Size
     * 
     * @global array $wpstg_options
     * @param string $folder path
     * @return string
     */
    private static function render_folder_size( $folder, $deep = TRUE ) {
        global $wpstg_options;

        $wpstg_options['folder_size'] = false;

        if( empty( $folder ) ) {
            return '';
        }

        $folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
        $folder_size = (!empty( $wpstg_options['folder_size'] )) ? ' (' . size_format( wpstgFile::get_folder_size( $folder, $deep ), 2 ) . ')' : '';
        return $folder_size;
    }

    /**
     *
     * Get excluded specific folder from a given parent folder
     *
     * @param $folder string folder to check for excludes
     *
     * @param $excludedir array $excludedir
     *
     * @return array of folder to exclude
     */
    public static function get_exclude_dirs( $folder, $excludedir = array() ) {
        $folder = trailingslashit( str_replace( '\\', '/', realpath( $folder ) ) );
        if( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) ) != $folder ) {
            $excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( WP_CONTENT_DIR ) ) );
        }
        if( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) ) != $folder ) {
            $excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( WP_PLUGIN_DIR ) ) );
        }
        if( false !== strpos( trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) ), $folder ) && trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) ) != $folder ) {
            $excludedir[] = trailingslashit( str_replace( '\\', '/', realpath( get_theme_root() ) ) );
        }
        return array_unique( $excludedir );
    }
    
    /**
     * Get all excluded folders of a parent folder
     * 
     * @param string $folder
     * @param array $excludedir
     * @return array
     */
    public static function get_all_excluded_folders($folder, $excludedir = array()){
        $folders = self::get_exclude_dirs($folder, $excludedir);
        $folders_created = self::get_folders_from_file();
        return array_merge($folders, $folders_created);
        return $folders;
    }

    /**
     * Add a folder path to wp-content/uploads/wp-staging/temp-folders.php that should be copied
     *
     * @param array $folders folder to add
     * @param bool $new overwrite existing file
     */
    public static function add_folders_to_backup( $folders = array(), $new = false ) {
        if( !is_array( $folders ) ) {
            $folders = ( array ) $folders;
        }
        $file = wpstg_get_upload_dir() . '/included-folders.php';
        if( !file_exists( $file ) || $new ) {
            file_put_contents( $file, '<?php' . PHP_EOL );
        }
        $content = '';
        foreach ( $folders AS $folder ) {
            $content .= '//' . $folder . PHP_EOL;
        }
        if( $content ) {
            file_put_contents( $file, $content, FILE_APPEND );
        }
    }

    /**
     * Write sanitized folder to temp-sanitized-folders.php that should be copied
     *
     * @param array $folders folder to add
     * @param bool $new overwrite existing file
     */
    public static function add_sanitized_folders_to_backup( $folders = array(), $new = false ) {
        if( !is_array( $folders ) ) {
            $folders = ( array ) $folders;
        }
        $file = wpstg_get_upload_dir() . '/included-folders.php';
        if( !file_exists( $file ) || $new ) {
            file_put_contents( $file, '<?php' . PHP_EOL );
        }
        $content = '';
        foreach ( $folders AS $folder ) {
            $content .= '//' . $folder . PHP_EOL;
        }
        if( $content ) {
            file_put_contents( $file, $content, FILE_APPEND );
        }
    }

    /**
     * Get list of Folder for copy
     *
     * @return array folder list
     */
    public static function get_folders_from_file() {
        $file = wpstg_get_upload_dir() . '/included-folders.php';
        if( !file_exists( $file ) ) {
            return array();
        }
        $folders = array();
        $file_data = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        foreach ( $file_data as $folder ) {
            $folder = trim( str_replace( array('<?php', '//'), '', $folder ) );
            if( !empty( $folder ) && is_dir( $folder ) ) {
                $folders[] = $folder;
            }
        }
        $folders = array_unique( $folders );
        sort( $folders );
        //$this->count_folder = count( $folders );
        return $folders;
    }
    /**
     * Get list of all excluded folders from job queue
     *
     * @return array folder list
     */
    public static function get_excluded_folders_from_queue() {
        $file = wpstg_get_upload_dir() . '/excluded-folders.php';
        if( !file_exists( $file ) ) {
            return array();
        }
        $folders = array();
        $file_data = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        foreach ( $file_data as $folder ) {
            $folder = trim( str_replace( array('<?php', '//'), '', $folder ) );
            if( !empty( $folder ) && is_dir( $folder ) ) {
                $folders[] = $folder;
            }
        }
        $folders = array_unique( $folders );
        sort( $folders );

        return $folders;
    }
    
    /**
     * Write all excluded folders to queue-folders.php
     *
     * @param array $folders folders to add
     * @param bool $new overwrite existing file
     */
    public static function add_excluded_folders_to_queue( $folders = array(), $new = false ) {
        if( !is_array( $folders ) ) {
            $folders = ( array ) $folders;
        }
        $file = wpstg_get_upload_dir() . '/excluded-folders.php';
        if( !file_exists( $file ) || $new ) {
            file_put_contents( $file, '<?php' . PHP_EOL );
            $content = '';
            foreach ( $folders AS $folder ) {
                $content .= '//' . $folder . PHP_EOL;
            }
            if( $content ) {
                file_put_contents( $file, $content, FILE_APPEND );
            }
        } else {
            wpstg_log('File excluded-folders.php already exists. Restart the job.');
        }
    }

    /**
     *
     * Helper function for folder_list()
     *
     * @param $job_object wpstgJob
     * @param string $folder
     * @param array $excludedirs
     * @param bool $first
     *
     * @return bool
     *
     */
    public function get_folder_list($id, $folder, $excludedirs = array(), $first = true ) {
        $folder = trailingslashit( $folder );
        $excluded_files = array();
        $excludedObj = new wpstgFile;
        $excluded_files = $excludedObj->get_excluded_files($id);
                
        
        if( $dir = opendir( $folder ) ) {
            //add current folder to folder list
            //$this->add_folders_to_backup( $folder );
            //scan folder
            while ( false !== ( $file = readdir( $dir ) ) ) {
                if( in_array( $file, array('.', '..'), true ) ) {
                    continue;
                }
                //var_dump($excluded_files);
                foreach ( $excluded_files as $exclusion ) { //exclude files
                    $exclusion = trim( $exclusion );
                    if( false !== stripos( $folder . $file, trim( $exclusion ) ) && !empty( $exclusion ) ) {
                        continue 2;
                    }
                }
                if( is_dir( $folder . $file ) ) {
                    //var_dump($excludedirs);
                    if( in_array( trailingslashit( $folder . $file ), $excludedirs ) ) {
                        continue;
                    }
                    if( !is_readable( $folder . $file ) ) {
                        wpstg_log( sprintf( __( 'Folder "%s" is not readable!', 'wpstg' ), $folder . $file ) );
                        continue;
                    }
                    $this->get_folder_list( $id, trailingslashit( $folder . $file ), $excludedirs, false );
                }
               
            }
             $this->add_folders_to_backup( $folder );
            closedir( $dir );
        }
        return true;
    }

}
