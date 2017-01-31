<?php

/**
 * Class for methods for file/folder related operations
 */
class wpstgFile {

    /**
     * @var array of files/folder to exclude from backup
     */
    public $exclude_from_backup = array();

    public function __construct( $jobid = 1 ) {
        $this->get_excluded_files( $jobid );
    }

    /**
     * Get excluded files
     * 
     * @param int $jobid
     * @return array
     */
    public function get_excluded_files( $jobid ) {
        $this->exclude_from_backup = explode( ',', trim( wpstgJobOptions::get( $jobid, 'fileexclude' ) ) );
        $this->exclude_from_backup = array_unique( $this->exclude_from_backup );
        return $this->exclude_from_backup;
    }

    /**
     *
     * Get the folder for blog uploads
     *
     * @return string
     */
    public static function get_upload_dir() {
        if( is_multisite() ) {
            if( defined( 'UPLOADBLOGSDIR' ) )
                return trailingslashit( str_replace( '\\', '/', ABSPATH . UPLOADBLOGSDIR ) );
            elseif( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites' ) )
                return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites/' );
            elseif( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads' ) )
                return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/' );
            else
                return trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
        } else {
            $upload_dir = wp_upload_dir( null, false, true );
            return trailingslashit( str_replace( '\\', '/', $upload_dir['basedir'] ) );
        }
    }

    /**
     *
     * check if path in open basedir
     *
     * @param string $file the file path to check
     *
     * @return bool is it in open basedir
     */
    public static function is_in_open_basedir( $file ) {
        $ini_open_basedir = ini_get( 'open_basedir' );
        if( empty( $ini_open_basedir ) ) {
            return TRUE;
        }
        $open_base_dirs = explode( PATH_SEPARATOR, $ini_open_basedir );
        $file = trailingslashit( strtolower( str_replace( '\\', '/', $file ) ) );
        foreach ( $open_base_dirs as $open_base_dir ) {
            if( empty( $open_base_dir ) ) {
                continue;
            }
            $open_base_dir = realpath( $open_base_dir );
            $open_base_dir = strtolower( str_replace( '\\', '/', $open_base_dir ) );
            $part = substr( $file, 0, strlen( $open_base_dir ) );
            if( $part === $open_base_dir ) {
                return TRUE;
            }
        }
        return FALSE;
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

    /**
     * Get an absolute path if it is relative
     *
     * @param string $path
     *
     * @return string
     */
    public static function get_absolute_path( $path = '/' ) {
        $path = str_replace( '\\', '/', $path );
        $content_path = trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
        //use WP_CONTENT_DIR as root folder
        if( empty( $path ) || $path === '/' ) {
            $path = $content_path;
        }
        //make relative path to absolute
        if( substr( $path, 0, 1 ) !== '/' && !preg_match( '#^[a-zA-Z]:/#', $path ) ) {
            $path = $content_path . $path;
        }
        return $path;
    }

    /**
     *
     * Get an array of files to copy in the selected folder
     *
     * @param string $folder the folder absolute path to get the files from
     *
     * @return array files to copy
     */
    public function get_files_in_folder( $folder ) {
        $files = array();
        $folder = trailingslashit( $folder );
        if( !is_dir( $folder ) ) {
            wpstg_log( sprintf( _x( 'Folder %s not exists', 'Folder name', 'wpstg' ), $folder ) );
            return $files;
        }
        if( !is_readable( $folder ) ) {
            wpstg_log( sprintf( _x( 'Folder %s not readable', 'Folder name', 'wpstg' ), $folder ) );
            return $files;
        }
        if( $dir = opendir( $folder ) ) {
            while ( false !== ( $file = readdir( $dir ) ) ) {
                if( in_array( $file, array('.', '..'), true ) || is_dir( $folder . $file ) ) {
                    continue;
                }
                foreach ( $this->exclude_from_backup as $exclusion ) { //exclude files
                    $exclusion = trim( $exclusion );
                    if( false !== stripos( $folder . $file, trim( $exclusion ) ) && !empty( $exclusion ) ) {
                        continue 2;
                    }
                }
//				if ( $this->job['backupexcludethumbs'] && strpos( $folder, self::get_upload_dir() ) !== false && preg_match( "/\-[0-9]{1,4}x[0-9]{1,4}.+\.(jpg|png|gif)$/i", $file ) ) {
//					continue;
//				}
                if( is_link( $folder . $file ) ) {
                    wpstg_log( sprintf( __( 'Link "%s" not following.', 'wpstg' ), $folder . $file ) );
                } elseif( !is_readable( $folder . $file ) ) {
                    wpstg_log( sprintf( __( 'File "%s" is not readable!', 'wpstg' ), $folder . $file ) );
                } else {
                    $file_size = filesize( $folder . $file );
                    if( !is_int( $file_size ) || $file_size < 0 || $file_size > 2147483647 ) {
                        wpstg_log( sprintf( __( 'File size of “%s” cannot be retrieved. File might be too large and will not be added to queue.', 'wpstg' ), $folder . $file . ' ' . $file_size ) );
                        continue;
                    }
                    $files[] = $folder . $file;
                }
            }
            closedir( $dir );
        }
        return $files;
    }



}

$wpstg_file = new wpstgFile();
