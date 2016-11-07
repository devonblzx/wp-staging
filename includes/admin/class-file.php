<?php
/**
 * Class for methods for file/folder related things
 */
class wpstgFile {
	/**
	 *
	 * Get the folder for blog uploads
	 *
	 * @return string
	 */
	public static function get_upload_dir() {
		if ( is_multisite() ) {
			if ( defined( 'UPLOADBLOGSDIR' ) )
				return trailingslashit( str_replace( '\\', '/',ABSPATH . UPLOADBLOGSDIR ) );
			elseif ( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites') )
				return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/sites/' );
			elseif ( is_dir( trailingslashit( WP_CONTENT_DIR ) . 'uploads' ) )
				return str_replace( '\\', '/', trailingslashit( WP_CONTENT_DIR ) . 'uploads/' );
			else
				return trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) );
		} else {
			$upload_dir = wp_upload_dir( null, false, true );
			return trailingslashit( str_replace( '\\', '/', $upload_dir[ 'basedir' ] ) );
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
		if ( empty( $ini_open_basedir ) ) {
			return TRUE;
		}
		$open_base_dirs = explode( PATH_SEPARATOR, $ini_open_basedir );
		$file           = trailingslashit( strtolower( str_replace( '\\', '/', $file ) ) );
		foreach ( $open_base_dirs as $open_base_dir ) {
			if ( empty( $open_base_dir ) ) {
				continue;
			}
			$open_base_dir = realpath( $open_base_dir );
			$open_base_dir = strtolower( str_replace( '\\', '/', $open_base_dir ) );
			$part = substr( $file, 0, strlen( $open_base_dir ) );
			if ( $part === $open_base_dir ) {
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
		if ( ! is_readable( $folder ) )
			return $files_size;
		if ( $dir = opendir( $folder ) ) {
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ), true ) || is_link( $folder . '/' . $file ) ) {
					continue;
				}
				if ( $deep && is_dir( $folder . '/' . $file ) ) {
					$files_size = $files_size + self::get_folder_size( $folder . '/' . $file, TRUE );
				}
				elseif ( is_link( $folder . '/' . $file ) ) {
					continue;
				}
				elseif ( is_readable( $folder . '/' . $file ) ) {
					$file_size = filesize( $folder . '/' . $file );
					if ( empty( $file_size ) || ! is_int( $file_size ) ) {
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
		if ( empty( $path ) || $path === '/' ) {
			$path = $content_path;
		}
		//make relative path to absolute
		if ( substr( $path, 0, 1 ) !== '/' && ! preg_match( '#^[a-zA-Z]:/#', $path ) ) {
			$path =  $content_path . $path;
		}
		return $path;
	}
}

$wpstg_file = new wpstgFile();