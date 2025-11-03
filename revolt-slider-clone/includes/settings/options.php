<?php
/**
 * Global settings management.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Settings;

/**
 * Provides helpers to read and write plugin level options.
 */
class Options {
    /**
     * Option key used to persist settings.
     */
    const OPTION_KEY = 'revolt_slider_clone_settings';

    /**
     * Return the default settings structure.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function get_defaults() {
        return [
            'performance' => [
                'lazyLoad'                => true,
                'disableAnimationsMobile' => false,
            ],
            'defaults'    => [
                'width'    => 1200,
                'height'   => 600,
                'autoplay' => true,
                'delay'    => 5000,
                'loop'     => true,
            ],
            'permissions' => [
                'canEditModules' => 'edit_revolt_modules',
            ],
        ];
    }

    /**
     * Retrieve the persisted settings merged with defaults.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function get_settings() {
        $saved     = get_option( self::OPTION_KEY, [] );
        $defaults  = self::get_defaults();
        $sanitized = self::sanitize( $saved );

        return [
            'performance' => array_merge( $defaults['performance'], $sanitized['performance'] ),
            'defaults'    => array_merge( $defaults['defaults'], $sanitized['defaults'] ),
            'permissions' => array_merge( $defaults['permissions'], $sanitized['permissions'] ),
        ];
    }

    /**
     * Persist new settings after sanitization.
     *
     * @param array<string,mixed> $value Raw submitted values.
     *
     * @return array<string,array<string,mixed>> Sanitized settings.
     */
    public static function update_settings( $value ) {
        $sanitized = self::sanitize( $value );
        update_option( self::OPTION_KEY, $sanitized );

        return self::get_settings();
    }

    /**
     * Sanitize incoming settings before persisting.
     *
     * @param array<string,mixed> $value Raw settings.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function sanitize( $value ) {
        $value    = is_array( $value ) ? $value : [];
        $defaults = self::get_defaults();

        $performance    = isset( $value['performance'] ) && is_array( $value['performance'] ) ? $value['performance'] : [];
        $defaults_group = isset( $value['defaults'] ) && is_array( $value['defaults'] ) ? $value['defaults'] : [];
        $permissions    = isset( $value['permissions'] ) && is_array( $value['permissions'] ) ? $value['permissions'] : [];

        return [
            'performance' => [
                'lazyLoad'                => ! empty( $performance['lazyLoad'] ),
                'disableAnimationsMobile' => ! empty( $performance['disableAnimationsMobile'] ),
            ],
            'defaults'    => [
                'width'    => max( 320, absint( $defaults_group['width'] ?? $defaults['defaults']['width'] ) ),
                'height'   => max( 180, absint( $defaults_group['height'] ?? $defaults['defaults']['height'] ) ),
                'autoplay' => ! empty( $defaults_group['autoplay'] ),
                'delay'    => max( 0, absint( $defaults_group['delay'] ?? $defaults['defaults']['delay'] ) ),
                'loop'     => ! empty( $defaults_group['loop'] ),
            ],
            'permissions' => [
                'canEditModules' => sanitize_key( $permissions['canEditModules'] ?? $defaults['permissions']['canEditModules'] ),
            ],
        ];
    }
}
