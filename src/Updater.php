<?php

namespace AppSero;

/**
 * AppSero Updater
 *
 * This class will show new updates project
 */
class Updater {

    /**
     * AppSero\Client
     *
     * @var object
     */
    protected $client;

    /**
     * Initialize the class
     *
     * @param AppSero\Client
     */
    public function __construct( Client $client ) {

        $this->client = $client;

        $this->cache_key = 'appsero_' . md5( $this->client->slug ) . '_version_info';

        // Set up hooks.
        $this->init();
    }

    /**
     * Set up WordPress filters to hook into WP's update process.
     *
     * @return void
     */
    public function init() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
        // remove_action( 'after_plugin_row_' . $this->client->basename, 'wp_plugin_update_row', 10 );
        // add_action( 'after_plugin_row_' . $this->client->basename, array( $this, 'show_update_notification' ), 10, 2 );
        // add_action( 'admin_init', array( $this, 'show_changelog' ) );
    }

    /**
     * Check for Update for this specific project
     */
    public function check_update( $transient_data ) {

        global $pagenow;

        if ( ! is_object( $transient_data ) ) {
            $transient_data = new stdClass;
        }

        if ( 'plugins.php' == $pagenow && is_multisite() ) {
            return $transient_data;
        }

        if ( ! empty( $transient_data->response ) && ! empty( $transient_data->response[ $this->client->basename ] ) ) {
            return $transient_data;
        }

        $version_info = $this->get_cached_version_info();

        if ( false === $version_info ) {
            $version_info = $this->get_project_latest_version();
            $this->set_cached_version_info( $version_info );
        }

        if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

            if ( version_compare( $this->client->project_version, $version_info->new_version, '<' ) ) {
                unset( $version_info->sections );
                $transient_data->response[ $this->client->basename ] = $version_info;
            }

            $transient_data->last_checked = time();
            $transient_data->checked[ $this->client->basename ] = $this->client->project_version;
        }

        return $transient_data;
    }

    /**
     * Get version info from database
     *
     * @return Object or Boolean
     */
    private function get_cached_version_info() {

        $cache = get_option( $this->cache_key );

        if( ! $cache || empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
            return false; // Cache is expired
        }

        $value = json_decode( $cache['value'] );

        // We need to turn the icons into an array
        if ( isset( $value->icons ) ) {
            $value->icons = (array) $value->icons;
        }

        // We need to turn the banners into an array
        if ( isset( $value->banners ) ) {
            $value->banners = (array) $value->banners;
        }

        if ( isset( $value->sections ) ) {
            $value->sections = (array) $value->sections;
        }

        return $value;
    }

    /**
     * Set version info to database
     */
    private function set_cached_version_info( $value ) {
        if ( ! $value ) {
            return;
        }

        $data = array(
            'timeout' => strtotime( '+3 hours', time() ),
            'value'   => json_encode( $value )
        );

        update_option( $this->cache_key, $data, false );
    }

    /**
     * Get plugin info from Appsero
     */
    private function get_project_latest_version() {

        $params = array(
            'version'  => $this->client->project_version,
            'name'     => $this->client->name,
            'slug'     => $this->client->slug,
            'basename' => $this->client->basename,
        );

        $route = 'update/' . $this->client->hash . '/check';

        $response = $this->client->send_request( $params, $route, true );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $response = json_decode( wp_remote_retrieve_body( $response ) );

        if ( ! isset( $response->id ) ) {
            return false;
        }

        if ( isset( $response->icons ) ) {
            $response->icons = (array) $response->icons;
        }

        if ( isset( $response->banners ) ) {
            $response->banners = (array) $response->banners;
        }

        if ( isset( $response->sections ) ) {
            $response->sections = (array) $response->sections;
        }

        return $response;
    }

    /**
     * Updates information on the "View version x.x details" page with custom data.
     *
     * @param mixed   $data
     * @param string  $action
     * @param object  $args
     *
     * @return object $data
     */
    public function plugins_api_filter( $data, $action = '', $args = null ) {

        if ( $action != 'plugin_information' ) {
            return $data;
        }

        if ( ! isset( $args->slug ) || ( $args->slug != $this->client->slug ) ) {
            return $data;
        }

        $version_info = $this->get_cached_version_info();

        if ( false === $version_info ) {
            $version_info = $this->get_project_latest_version();
            $this->set_cached_version_info( $version_info );
        }

        return $version_info;
    }

}
