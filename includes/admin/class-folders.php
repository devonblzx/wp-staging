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
<!--            <tr>
                <th scope="row"><label for="idcopyroot"><?php esc_html_e( 'WordPress folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox"
                           type="checkbox"<?php //checked( wpstgJobOptions::get( $main, 'copyroot' ), TRUE, TRUE ); ?>
                           name="wpstgroot" id="idcopyroot" value="1" /> <code title="<?php //echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), $abs_path ) ); ?>"><?php //echo esc_attr( $folder ); ?></code><?php //echo esc_html( $this->render_folder_size( $folder ) ); ?>

                    <fieldset id="rootexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php
//                        if( $folder && $dir = opendir( $folder ) ) {
//                            while ( ( $file = readdir( $dir ) ) !== FALSE ) {
//                                $excludes = wpstgJobOptions::get( $main, 'copyrootexcludedirs' );
//
//                                if( !in_array( $file, array('.', '..'), true ) && is_dir( $folder . '/' . $file ) && !in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
//                                    $folder_size = ($show_size === 'on') ? ' (' . size_format( wpstgFile::get_folder_size( $folder . '/' . $file ), 2 ) . ')' : '';
//
//                                    echo '<nobr><label for="idrootexcludedirs-' . sanitize_file_name( $file ) . '"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), FALSE, FALSE ) . ' name="copyrootexcludedirs[]" id="idrootexcludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '" /> ' . esc_html( $file ) . esc_html( $this->render_folder_size( $folder ) ) . '</label><br /></nobr>';
//                                }
//                            }
//                            closedir( $dir );
//                        }
                        ?>
                    </fieldset>
                </td>
            </tr>-->
            <tr>
                <th scope="row"><label for="idcopyroot"><?php esc_html_e( 'Main WordPress folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyroot' ), TRUE, TRUE ); ?> name="copyroot" id="idcopyroot" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), $folder ) ); ?>"><?php echo esc_attr( $folder ); ?></code><?php echo esc_html( $this->render_folder_size( $folder, FALSE ) ); ?>
                    <fieldset id="rootexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, $folder, 'copyroot' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopycontent"><?php esc_html_e( 'Content folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copycontent' ), TRUE, TRUE ); ?> name="copycontent" id="idcopycontent" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), WP_CONTENT_DIR ) ); ?>"><?php echo esc_attr( WP_CONTENT_DIR ); ?></code><?php echo esc_html( $this->render_folder_size( WP_CONTENT_DIR, FALSE ) ); ?>
                    <fieldset id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, WP_CONTENT_DIR, 'copycontent' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopyplugins"><?php esc_html_e( 'Plugins Folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyplugins' ), TRUE, TRUE ); ?> name="copyplugins" id="idcopyplugins" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), WP_PLUGIN_DIR ) ); ?>"><?php echo esc_attr( WP_PLUGIN_DIR ); ?></code><?php echo esc_html( $this->render_folder_size( WP_PLUGIN_DIR, FALSE  ) ); ?>
                    <fieldset id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, WP_PLUGIN_DIR, 'copyplugins' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopythemes"><?php esc_html_e( 'Themes folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copythemes' ), TRUE, TRUE ); ?> name="copythemes" id="idcopythemes" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), get_theme_root() ) ); ?>"><?php echo esc_attr( get_theme_root() ); ?></code><?php echo esc_html( $this->render_folder_size( get_theme_root(), FALSE  ) ); ?>
                    <fieldset id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, get_theme_root(), 'copythemes' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="idcopyuploads"><?php esc_html_e( 'Uploads folder', 'wpstg' ); ?></label></th>
                <td>
                    <input class="checkbox" type="checkbox"<?php checked( wpstgJobOptions::get( $main, 'copyuploads' ), TRUE, TRUE ); ?> name="copyuploads" id="idcopyuploads" value="1" /> 
                    <code title="<?php echo esc_attr( sprintf( __( 'Path as set by user (symlink?): %s', 'wpstg' ), wpstgFile::get_upload_dir() ) ); ?>"><?php echo esc_attr( wpstgFile::get_upload_dir() ); ?></code><?php echo esc_html( $this->render_folder_size( wpstgFile::get_upload_dir(), FALSE  ) ); ?>
                    <fieldset id="copycontentexcludedirs" style="padding-left:15px; margin:2px;">
                        <?php echo $this->render_subfolder( $main, wpstgFile::get_upload_dir(), 'copyuploads' ); ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="dirinclude"><?php esc_html_e( 'Extra folders', 'wpstg' ); ?></label></th>
                <td>
                    <textarea name="dirinclude" id="dirinclude" class="text code" rows="7" cols="50"><?php echo esc_attr( wpstgJobOptions::get( $main, 'dirinclude' ) ); ?></textarea>
	            <p class="description"><?php esc_attr_e( 'Separate folder names with a line-break or a comma. Folders must be set with their absolute path!', 'backwpup' )?></p>
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
     * @param bool count size of subfolders 
     */
    private function render_subfolder( $id, $folder, $name ) {
        $folder = untrailingslashit( str_replace( '\\', '/', $folder ) );
        $html = '';
        if( $folder && $dir = opendir( $folder ) ) {
            while ( ( $file = readdir( $dir ) ) !== FALSE ) {
                $excludes = wpstgJobOptions::get( $id, $name . 'excludedirs' );
                if( !in_array( $file, array('.', '..'), true ) && is_dir( $folder . '/' . $file ) && !in_array( trailingslashit( $folder . '/' . $file ), $this->get_exclude_dirs( $folder ), true ) ) {
                    $title = '';
                    $html .= '<nobr><label for="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '"><input class="checkbox" type="checkbox"' . checked( in_array( $file, $excludes, true ), FALSE, FALSE ) . ' name="' . $name . 'excludedirs[]" id="' . $name . 'excludedirs-' . sanitize_file_name( $file ) . '" value="' . esc_attr( $file ) . '" /> ' . esc_html( $file ) . esc_html( $this->render_folder_size( $folder . '/' . $file ) ) . '</label><br /></nobr>';
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
    private function render_folder_size( $folder, $deep = TRUE ) {
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
     * Get exclude specific folder from a given parent folder
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


}

$wpstg_file_sync = new WPSTG_Folders();

