<?php
/**
 * REST controller for Revolt modules.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Rest;

use Revolt\Plugin;
use Revolt\Schema\Module_Schema;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Handles module CRUD via REST.
 */
class Modules_Controller {
    const NAMESPACE = 'revolt/v1';

    /**
     * Register REST routes.
     *
     * @return void
     */
    public static function register_routes() {
        register_rest_route(
            self::NAMESPACE,
            '/modules/(?P<id>\\d+)',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ __CLASS__, 'get_item' ],
                    'permission_callback' => [ __CLASS__, 'can_view_modules' ],
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [ __CLASS__, 'update_item' ],
                    'permission_callback' => [ __CLASS__, 'can_edit_modules' ],
                ],
            ]
        );

        register_rest_route(
            self::NAMESPACE,
            '/modules',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [ __CLASS__, 'get_items' ],
                    'permission_callback' => [ __CLASS__, 'can_view_modules' ],
                ],
                [
                    'methods'             => 'POST',
                    'callback'            => [ __CLASS__, 'create_item' ],
                    'permission_callback' => [ __CLASS__, 'can_edit_modules' ],
                ],
            ]
        );
    }

    /**
     * Check permissions for editing modules.
     *
     * @return bool
     */
    public static function can_edit_modules() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check permissions for viewing modules.
     *
     * @return bool
     */
    public static function can_view_modules() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Sanitize module JSON input.
     *
     * @param string $value Raw JSON string.
     *
     * @return string
     */
    public static function sanitize_module_json( $value ) {
        if ( empty( $value ) ) {
            return wp_json_encode( Module_Schema::get_default_module() );
        }

        // Ensure valid JSON.
        json_decode( $value, true );

        return json_last_error() ? wp_json_encode( Module_Schema::get_default_module() ) : $value;
    }

    /**
     * Get module item.
     *
     * @param WP_REST_Request $request Request.
     *
     * @return WP_REST_Response|WP_Error
     */
    public static function get_item( WP_REST_Request $request ) {
        $id = absint( $request['id'] );
        $post = get_post( $id );

        if ( ! $post || 'revolt_module' !== $post->post_type ) {
            return new WP_Error( 'revolt_module_not_found', __( 'Module not found.', 'revolt-slider-clone' ), [ 'status' => 404 ] );
        }

        return rest_ensure_response( self::prepare_module_response( $post ) );
    }

    /**
     * Get all modules.
     *
     * @return WP_REST_Response
     */
    public static function get_items() {
        $posts = get_posts(
            [
                'post_type'      => 'revolt_module',
                'posts_per_page' => 100,
                'post_status'    => 'any',
            ]
        );

        $modules = array_map( [ __CLASS__, 'prepare_module_response' ], $posts );

        return rest_ensure_response( $modules );
    }

    /**
     * Create a new module.
     *
     * @param WP_REST_Request $request Request.
     *
     * @return WP_REST_Response|WP_Error
     */
    public static function create_item( WP_REST_Request $request ) {
        $title = sanitize_text_field( $request['title'] ?? __( 'New Module', 'revolt-slider-clone' ) );
        $type  = Plugin::sanitize_module_type( $request['type'] ?? 'slider' );

        $id = wp_insert_post(
            [
                'post_type'   => 'revolt_module',
                'post_title'  => $title,
                'post_status' => 'draft',
            ]
        );

        if ( is_wp_error( $id ) ) {
            return $id;
        }

        update_post_meta( $id, '_revolt_module_type', $type );
        update_post_meta( $id, '_revolt_module_config', wp_json_encode( Module_Schema::get_default_module( $type ) ) );

        return rest_ensure_response( self::prepare_module_response( get_post( $id ) ) );
    }

    /**
     * Update module.
     *
     * @param WP_REST_Request $request Request.
     *
     * @return WP_REST_Response|WP_Error
     */
    public static function update_item( WP_REST_Request $request ) {
        $id = absint( $request['id'] );

        $post = get_post( $id );
        if ( ! $post || 'revolt_module' !== $post->post_type ) {
            return new WP_Error( 'revolt_module_not_found', __( 'Module not found.', 'revolt-slider-clone' ), [ 'status' => 404 ] );
        }

        $title = sanitize_text_field( $request['title'] ?? $post->post_title );
        $type  = Plugin::sanitize_module_type( $request['type'] ?? get_post_meta( $id, '_revolt_module_type', true ) );
        $alias = sanitize_key( $request['alias'] ?? get_post_meta( $id, '_revolt_module_alias', true ) );
        $config = self::sanitize_module_json( wp_json_encode( $request['config'] ?? [] ) );

        wp_update_post(
            [
                'ID'         => $id,
                'post_title' => $title,
            ]
        );

        update_post_meta( $id, '_revolt_module_type', $type );
        update_post_meta( $id, '_revolt_module_alias', $alias );
        update_post_meta( $id, '_revolt_module_config', $config );

        return rest_ensure_response( self::prepare_module_response( get_post( $id ) ) );
    }

    /**
     * Prepare module response.
     *
     * @param \WP_Post $post Post.
     *
     * @return array<string,mixed>
     */
    protected static function prepare_module_response( $post ) {
        return [
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'type'    => get_post_meta( $post->ID, '_revolt_module_type', true ),
            'alias'   => get_post_meta( $post->ID, '_revolt_module_alias', true ),
            'config'  => json_decode( get_post_meta( $post->ID, '_revolt_module_config', true ), true ),
            'status'  => $post->post_status,
            'modified'=> $post->post_modified_gmt,
        ];
    }
}
