<?php

namespace AppSero;

/**
 * AppSero License Checker
 *
 * This class will check, active and deactive license
 */
class License {

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
    }

    /**
     * Check license
     *
     * @return boolean
     */
    public function check( $license_key ) {
        $route    = 'v1/license-api/' . $this->client->hash . '/check';

        return $this->send_request( $license_key, $route );
    }

    /**
     * Active a license
     *
     * @return boolean
     */
    public function activate( $license_key ) {
        $route    = 'v1/license-api/' . $this->client->hash . '/activate';

        return $this->send_request( $license_key, $route );
    }

    /**
     * Deactivate a license
     *
     * @return boolean
     */
    public function deactivate( $license_key ) {
        $route    = 'v1/license-api/' . $this->client->hash . '/deactivate';

        return $this->send_request( $license_key, $route );
    }

    /**
     * Send common request
     *
     * @param $license_key
     * @param $route
     *
     * @return array
     */
    protected function send_request( $license_key, $route ) {
        $params = array(
            'license_key' => $license_key,
            'url'         => esc_url( home_url() ),
        );

        $response = $this->client->send_request( $params, $route, true );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $response = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $response['errors'] ) && isset( $response['errors']['license_key'] ) ) {
            $response = array(
                'success' => false,
                'error'   => $response['errors']['license_key'][0]
            );
        }

        return $response;
    }

}
