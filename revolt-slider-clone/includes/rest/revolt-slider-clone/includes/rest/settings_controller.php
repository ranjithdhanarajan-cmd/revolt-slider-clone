<?php
/**
 * REST controller for plugin settings.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Rest;

use Revolt\Settings\Options;

/**
 * Handles CRUD operations for global plugin settings.
 */
class Settings_Controller {
    /**
     * REST namespace used by the plugin.
     */
    const NAMESPACE = 'revolt/v1';

    /**
     * Route base.
     */
    const REST_BASE = '/settings';

    /**
     * Register REST routes.
     *
     * @return void
     */
    public static function register_routes() {
        register_rest_route(
            self::NAMESPACE,
            self::REST_BASE,
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ __CLASS__, 'get_settings' ],
                    'permission_callback' => [ __CLASS__, 'permissions_check' ],
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [ __CLASS__, 'update_settings' ],
                    'permission_callback' => [ __CLASS__, 'permissions_check' ],
                    'args'                => [
                        'performance' => [
                            'type'     => 'object',
                            'required' => false,
                        ],
                        'defaults'    => [
                            'type'     => 'object',
                            'required' => false,
                        ],
                        'permissions' => [
                            'type'     => 'object',
                            'required' => false,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Ensure the current user can manage settings.
     *
     * @return bool
     */
    public static function permissions_check() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Return sanitized settings for the UI.
     *
     * @return \WP_REST_Response
     */
    public static function get_settings() {
        return rest_ensure_response( Options::get_settings() );
    }

    /**
     * Update the stored settings.
     *
     * @param \WP_REST_Request $request Request instance.
     *
     * @return \WP_REST_Response
     */
    public static function update_settings( $request ) {
        $params   = $request->get_json_params();
        $settings = Options::update_settings( $params );

        return rest_ensure_response( $settings );
    }
}
