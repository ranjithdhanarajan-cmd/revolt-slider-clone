<?php
/**
 * Defines module schema and defaults.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Schema;

/**
 * Module schema helper.
 */
class Module_Schema {
    /**
     * Return schema definition for editor.
     *
     * @return array<string,mixed>
     */
    public static function get_schema() {
        return [
            'module' => [
                'id'       => 'number',
                'title'    => 'string',
                'alias'    => 'string',
                'type'     => 'string',
                'settings' => [
                    'width'        => 'number',
                    'height'       => 'number',
                    'autoplay'     => 'boolean',
                    'delay'        => 'number',
                    'loop'         => 'boolean',
                    'navigation'   => 'object',
                    'responsive'   => 'object',
                ],
                'slides'   => 'array',
            ],
            'slide'  => [
                'id'         => 'string',
                'background' => 'object',
                'layers'     => 'array',
                'settings'   => 'object',
            ],
            'layer'  => [
                'id'         => 'string',
                'type'       => 'string',
                'content'    => 'object',
                'style'      => 'object',
                'animations' => 'array',
                'timeline'   => 'object',
                'responsive' => 'object',
            ],
        ];
    }

    /**
     * Get default module structure.
     *
     * @param string $type Module type.
     *
     * @return array<string,mixed>
     */
    public static function get_default_module( $type = 'slider' ) {
        return [
            'type'     => $type,
            'settings' => [
                'width'      => 1200,
                'height'     => 600,
                'autoplay'   => true,
                'delay'      => 7000,
                'loop'       => true,
                'navigation' => [
                    'arrows'   => true,
                    'bullets'  => true,
                    'thumbnails' => false,
                ],
                'responsive' => [
                    'desktop' => [ 'width' => 1200, 'height' => 600 ],
                    'tablet'  => [ 'width' => 992, 'height' => 500 ],
                    'mobile'  => [ 'width' => 768, 'height' => 420 ],
                ],
            ],
            'slides'   => [
                self::get_default_slide(),
            ],
        ];
    }

    /**
     * Default slide definition.
     *
     * @return array<string,mixed>
     */
    public static function get_default_slide() {
        return [
            'id'         => wp_generate_uuid4(),
            'background' => [
                'color' => '#000000',
            ],
            'layers'     => [
                self::get_default_layer(),
            ],
            'settings'   => [
                'duration' => 7000,
                'transition' => 'fade',
            ],
        ];
    }

    /**
     * Default layer definition.
     *
     * @return array<string,mixed>
     */
    public static function get_default_layer() {
        return [
            'id'         => wp_generate_uuid4(),
            'type'       => 'text',
            'content'    => [
                'text' => __( 'Your layer text', 'revolt-slider-clone' ),
            ],
            'style'      => [
                'top'      => 100,
                'left'     => 100,
                'width'    => 400,
                'height'   => 100,
                'color'    => '#ffffff',
                'fontSize' => '32px',
            ],
            'animations' => [
                [
                    'id'        => wp_generate_uuid4(),
                    'type'      => 'fadeIn',
                    'start'     => 0,
                    'duration'  => 600,
                    'easing'    => 'easeOutQuad',
                    'from'      => [ 'opacity' => 0, 'y' => 40 ],
                    'to'        => [ 'opacity' => 1, 'y' => 0 ],
                ],
            ],
            'timeline'   => [
                'start'    => 0,
                'duration' => 6000,
            ],
            'responsive' => [
                'tablet' => [ 'top' => 80, 'left' => 60, 'fontSize' => '28px' ],
                'mobile' => [ 'top' => 60, 'left' => 40, 'fontSize' => '24px' ],
            ],
        ];
    }
}
