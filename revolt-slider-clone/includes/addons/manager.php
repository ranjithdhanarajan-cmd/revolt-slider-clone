<?php
/**
 * Add-on manager for Revolt Slider Clone.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Addons;

/**
 * Handles add-on registration.
 */
class Manager {
    /**
     * Registered add-ons.
     *
     * @var array<string,array<string,mixed>>
     */
    protected static $addons = [];

    /**
     * Bootstrap default add-ons and fire hooks.
     *
     * @return void
     */
    public static function bootstrap() {
        foreach ( glob( REVOLT_SLIDER_CLONE_PATH . 'includes/addons/*.php' ) as $file ) {
            if ( basename( $file ) === 'manager.php' ) {
                continue;
            }

            require_once $file;
        }

        do_action( 'revolt/register_addons' );
    }

    /**
     * Register a new add-on definition.
     *
     * @param string               $slug Add-on slug.
     * @param array<string,mixed>  $args Arguments.
     *
     * @return void
     */
    public static function register_addon( $slug, $args ) {
        $defaults = [
            'name'        => $slug,
            'description' => '',
            'version'     => '0.1.0',
            'author'      => '',
            'layers'      => [],
            'animations'  => [],
            'effects'     => [],
        ];

        self::$addons[ sanitize_key( $slug ) ] = wp_parse_args( $args, $defaults );
    }

    /**
     * Get registered add-ons.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function get_registered_addons() {
        return self::$addons;
    }
}
