<?php

namespace AppSero;

/**
 * AppSero Client
 *
 * This class is necessary to set project data
 */
class Client {

    /**
     * The client version
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * Hash identifier of the plugin
     *
     * @var string
     */
    public $hash;

    /**
     * Name of the plugin
     *
     * @var string
     */
    public $name;

    /**
     * The plugin/theme file path
     *
     * @var string
     */
    public $file;

    /**
     * URL to the API endpoint
     *
     * @var string
     */

    /**
     * Main plugin file
     *
     * @var string
     */
    public $basename;

    /**
     * Slug of the plugin
     *
     * @var string
     */
    public $slug;

    /**
     * The project version
     *
     * @var string
     */
    public $project_version;

	/**
     * Initialize the class
     *
     * @param string  $hash hash of the plugin
     * @param string  $name readable name of the plugin
     * @param string  $file main plugin file path
     */
    public function __construct( $hash, $name, $file ) {
        $this->hash = $hash;
        $this->name = $name;
        $this->file = $file;

        $this->set_basename_and_slug();
    }

    /**
     * Get Insights class object
     *
     * @return AppSero\Insights
     */
    public function insights() {
        return new Insights( $this );
    }

    /**
     * API Endpoint
     *
     * @return string
     */
    public function endpoint() {
        $endpoint = apply_filters( 'appsero_endpoint', 'https://api.appsero.com' );
        return trailingslashit( $endpoint );
    }

    /**
     * Set project basename, slug and version
     */
    public function set_basename_and_slug() {
        if ( strpos( $this->file, '/wp-content/themes/' ) === false ) {

            $this->basename = plugin_basename( $this->file );

            list( $this->slug, $mainfile) = explode( '/', $this->basename );

            require_once ABSPATH . 'wp-admin/includes/plugin.php';

            $plugin_data = get_plugin_data( $this->file );

            $this->project_version = $plugin_data['Version'];

        } else {
            $this->basename = str_replace( WP_CONTENT_DIR . '/themes/', '', $this->file );
            list( $this->slug, $mainfile) = explode( '/', $this->basename );
        }
    }

    /**
     * Run updater
     */
    public function updater() {
        return new Updater( $this );
    }

    /**
     * Send request to remote endpoint
     *
     * @param  array  $params
     * @param  string $route
     *
     * @return void
     */
    public function send_request( $params, $route, $blocking = false ) {
        $url = $this->endpoint() . $route;

        $response = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => $blocking,
            'headers'     => array( 'user-agent' => 'AppSero/' . md5( esc_url( home_url() ) ) . ';' ),
            'body'        => array_merge( $params, array( 'client' => $this->version ) ),
            'cookies'     => array()
        ) );

        return $response;
    }

}
