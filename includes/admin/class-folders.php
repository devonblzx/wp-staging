<?php

/**
 * Render List of Folders for Staging Process
 *
 * @author Rene Hermenau
 * 
 */
class WPSTG_Folders {

    public function __construct() {
        add_action( 'wp_ajax_wpstg_scanning', array($this, 'wpstg_scan_files') );
    }

    public function wpstg_scan_files() {
        global $all_files, $wpstg_clone_details, $wpstg_options;

        @set_time_limit( 300 );
        $abs_folder_up = false;
        $show_size = 'off';
        $abs_path = wpstg_get_clone_root_path();
        $main = 1;

        $folder = $abs_path;

        if( $abs_folder_up ) {
            $abs_path = dirname( $abs_path );
        }
        ?>
        <h3 class="title"><?php esc_html_e( 'Folders to Copy', 'wpstg' ) ?></h3>
        <p></p>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="idbackuproot"><?php esc_html_e( 'Copy WordPress folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox"
                           type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'backuproot' ), TRUE, TRUE ); ?>
                           name="backuproot" id="idbackuproot" value="1" /> <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), $abs_path ) ); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo esc_html( $this->render_folder_size( $folder ) ); ?>

                    <fieldset id="backuprootexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php
                        if( $folder && $dir = opendir( $folder ) ) {
                            while ( ( $file = readdir( $dir ) ) !== FALSE ) {
                                $excludes = wpstgJobOptions::get( $main, 'backuprootexcludedirs' );

                                if( !in_array( $file, array('.', '..'), true ) && is_dir( $folder . '/' . $file ) && !in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
                                    $folder_size = ($show_size === 'on') ? ' (' . size_format( $this->get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';

                                    echo '<nobr><label for="idrootexcludedirs-' . sanitize_file_name( $file ) . '"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), FALSE, FALSE ) . ' name="backuprootexcludedirs[]" id="idrootexcludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '" /> ' . esc_html( $file ) . esc_html( $this->render_folder_size( $folder ) ) . '</label><br /></nobr>';
                                }
                            }
                            closedir( $dir );
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idbackuproot"><?php esc_html_e( 'Copy Content folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'backupcontent' ), TRUE, TRUE ); ?> name="backupcontent" id="idbackupcontent" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), WP_CONTENT_DIR ) ); ?>"><?php echo esc_attr( WP_CONTENT_DIR ); ?></code><?php echo esc_html( $this->render_folder_size( WP_CONTENT_DIR ) ); ?>
                    <fieldset id="backupcontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, WP_CONTENT_DIR, 'backupcontent' ); ?>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Subfolder for a specific parent folder. Deepth 1
     * @param int $id job id
     * @param string $folder path
     * @param string $name name of the parent folder
     */
    private function render_subfolder( $id, $folder, $name ) {
        $folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
        $html = '';
        if( $folder && $dir = opendir( $folder ) ) {
            while ( ( $file = readdir( $dir ) ) !== FALSE ) {
                $excludes = wpstgJobOptions::get( $id, $name . 'excludedirs' );

                if( !in_array( $file, array('.', '..'), true ) && is_dir( $folder . '/' . $file ) && !in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
                    $title = '';

                    $html .= '<nobr><label for="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), FALSE, FALSE ) . ' name="' . $name . 'excludedirs[]" id="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '"' . $title . ' /> ' . esc_html( $file ) . esc_html( $this->render_folder_size( $folder ) ) . '</label><br /></nobr>';
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
    private function render_folder_size( $folder ) {
        global $wpstg_options;

        $wpstg_options['folder_size'] = true;

        if( empty( $folder ) ) {
            return '';
        }

        $folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
        $folder_size = (!empty( $wpstg_options['folder_size'] )) ? ' (' . size_format( $this->get_folder_size( $folder, FALSE ), 2 ) . ')' : '';
        return $folder_size;
    }

    /**
     *
     * Get sub folder to exclude from a given folder
     *
     * @param $folder string folder to check for excludes
     *
     * @param array $excludedir
     *
     * @return array of folder to exclude
     */
    private function get_exclude_dirs( $folder, $excludedir = array() ) {
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
     *
     * get size of files in folder
     *
     * @param string $folder the folder to calculate
     * @param bool $deep went thrue suborders
     * @return int folder size in byte
     */
    public static function get_folder_size( $folder, $deep = TRUE ) {
        $files_size = 0;
        if( !is_readable( $folder ) )
            return $files_size;
        if( $dir = opendir( $folder ) ) {
            while ( FALSE !== ( $file = readdir( $dir ) ) ) {
                if( in_array( $file, array('.', '..'), true ) || is_link( $folder . '/' . $file ) ) {
                    continue;
                }
                if( $deep && is_dir( $folder . '/' . $file ) ) {
                    $files_size = $files_size + self::get_folder_size( $folder . '/' . $file, TRUE );
                } elseif( is_link( $folder . '/' . $file ) ) {
                    continue;
                } elseif( is_readable( $folder . '/' . $file ) ) {
                    $file_size = filesize( $folder . '/' . $file );
                    if( empty( $file_size ) || !is_int( $file_size ) ) {
                        continue;
                    }
                    $files_size = $files_size + $file_size;
                }
            }
            closedir( $dir );
        }
        return $files_size;
    }

}

$wpstg_file_sync = new WPSTG_Folders();

