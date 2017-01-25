<?php

/**
 * Class for storing settings
 *
 * @author Rene Hermenau, BackWPup
 * 
 */
final class wpstgJobOptions {

    public function __construct() {
        $this->default_site_options();
    }

    /**
     *
     * add default options
     *
     */
    public static function default_site_options() {
        add_option( 'wpstg_jobs' );
    }

    /**
     *
     * Update a option
     *
     * @param int $jobid the job id
     * @param string $option Option key
     * @param mixed $value the value to store
     *
     * @return bool if option save or not
     */
    public static function update( $jobid, $option, $value ) {
        $jobid = ( int ) $jobid;
        $option = sanitize_key( trim( $option ) );
        if( empty( $jobid ) || empty( $option ) ) {
            return false;
        }
        //Update option
        $jobs_options = self::jobs_options();
        $jobs_options[$jobid][$option] = $value;
        return self::update_jobs_options( $jobs_options );
    }

    /**
     *
     * Load Options
     *
     * @param bool $use_cache
     *
     * @return array of options
     */
    private static function jobs_options() {
        return get_site_option( 'wpstg_jobs', array() );
    }

    /**
     *
     * Update Options
     *
     * @param array $options The options array to save
     *
     * @return bool updated or not
     */
    private static function update_jobs_options( $options ) {
        return update_site_option( 'wpstg_jobs', $options );
    }

    /**
     *
     * Get a Option
     *
     * @param int $jobid Option the job id
     * @param string $option Option key
     * @param mixed $default returned if no value, if null the default option will get
     * @param bool $use_cache USe the cache
     *
     * @return bool|mixed        false if nothing can get else the option value
     */
    public static function get( $jobid, $option, $default = null ) {
        $jobid = ( int ) $jobid;
        $option = sanitize_key( trim( $option ) );
        if( empty( $jobid ) || empty( $option ) ) {
            return false;
        }
        $jobs_options = self::jobs_options();
        if( !isset( $jobs_options[$jobid][$option] ) && isset( $default ) ) {
            return $default;
        } elseif( !isset( $jobs_options[$jobid][$option] ) ) {
            return self::defaults_job( $option );
        } else {
            return $jobs_options[$jobid][$option];
        }
    }

    /**
     *
     * Get default option for wpstg options
     *
     * @param string $key Option key
     *
     * @internal param int $id The job id
     *
     * @return bool|mixed
     * 
     * @todo created for later use
     */
    public static function defaults_job( $key = '' ) {
        $key = sanitize_key( trim( $key ) );
        //set defaults
        $default['copyexcludethumbs'] = FALSE;
        $default['copyspecialfiles'] = TRUE;
        $default['copyroot'] = TRUE;
        $default['copycontent'] = TRUE;
        $default['copyplugins'] = TRUE;
        $default['copythemes'] = TRUE;
        $default['copyuploads'] = TRUE;
        $default['copyrootexcludedirs'] = array('logs', 'usage');
        $default['copycontentexcludedirs'] = array('cache', 'upgrade', 'w3tc');
        $default['copypluginsexcludedirs'] = array('', '');
        $default['copythemesexcludedirs'] = array();
        $default['copyuploadsexcludedirs'] = array();
        $default['fileexclude'] = '.tmp,.svn,.git,desktop.ini,.DS_Store,/node_modules/';
        //$default['dirinclude'] = '';
        $default['copyabsfolderup'] = FALSE;

        //return all
        if( empty( $key ) ) {
            return $default;
        }

        //return one default setting
        if( isset( $default[$key] ) ) {
            return $default[$key];
        } else {
            return false;
        }
    }

    /**
     *
     * Job Options
     *
     * @param int $id The job id
     * @param bool $use_cache
     *
     * @return array of all job options
     */
    public static function get_job( $id ) {
        if( !is_numeric( $id ) ) {
            return false;
        }
        $id = intval( $id );
        $jobs_options = self::jobs_options();
        return wp_parse_args( $jobs_options[$id], self::defaults_job() );
    }

    /**
     *
     * Delete a Option
     *
     * @param int $jobid the job id
     * @param string $option Option key
     *
     * @return bool deleted or not
     */
    public static function delete( $jobid, $option ) {
        $jobid = ( int ) $jobid;
        $option = sanitize_key( trim( $option ) );
        if( empty( $jobid ) || empty( $option ) ) {
            return false;
        }
        //delete option
        $jobs_options = self::jobs_options( false );
        unset( $jobs_options[$jobid][$option] );
        return self::update_jobs_options( $jobs_options );
    }

    /**
     *
     * Delete a Job
     *
     * @param int $id The job id
     *
     * @return bool   deleted or not
     */
    public static function delete_job( $id ) {
        if( !is_numeric( $id ) ) {
            return false;
        }
        $id = intval( $id );
        $jobs_options = self::jobs_options();
        unset( $jobs_options[$id] );
        return self::update_jobs_options( $jobs_options );
    }

    /**
     *
     * get the id's of jobs
     *
     * @param string|null $key Option key or null for getting all id's
     * @param bool $value Value that the option must have to get the id
     *
     * @return array job id's
     */
    public static function get_job_ids( $key = null, $value = false ) {
        $key = sanitize_key( trim( $key ) );
        $jobs_options = self::jobs_options( false );
        if( empty( $jobs_options ) ) {
            return array();
        }
        //get option job ids
        if( empty( $key ) ) {
            return array_keys( $jobs_options );
        }
        //get option ids for option with the defined value
        $new_option_job_ids = array();
        foreach ( $jobs_options as $id => $option ) {
            if( isset( $option[$key] ) && $value == $option[$key] ) {
                $new_option_job_ids[] = ( int ) $id;
            }
        }
        sort( $new_option_job_ids );
        return $new_option_job_ids;
    }

}

$wpstg_job_settings = new wpstgJobOptions();

