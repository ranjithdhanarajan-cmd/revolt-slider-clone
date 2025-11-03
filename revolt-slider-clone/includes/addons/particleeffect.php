<?php
/**
 * Sample Particle Effect add-on.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Addons;

/**
 * Registers the particle layer type.
 */
class ParticleEffect {
    /**
     * Hook into add-on registration.
     */
    public static function register() {
        Manager::register_addon(
            'particle-effect',
            [
                'name'        => __( 'Particle Effect', 'revolt-slider-clone' ),
                'description' => __( 'Adds particle overlay layers and emitters.', 'revolt-slider-clone' ),
                'version'     => '0.1.0',
                'author'      => 'Revolt Slider Team',
                'layers'      => [
                    [
                        'type'        => 'particle',
                        'label'       => __( 'Particle Layer', 'revolt-slider-clone' ),
                        'description' => __( 'Animated particle system overlay.', 'revolt-slider-clone' ),
                        'defaults'    => [
                            'density' => 80,
                            'speed'   => 1.5,
                            'shape'   => 'circle',
                        ],
                    ],
                ],
                'effects'     => [
                    [
                        'type'        => 'particleEmitter',
                        'label'       => __( 'Particle Emitter', 'revolt-slider-clone' ),
                        'description' => __( 'Emit particles along a path or area.', 'revolt-slider-clone' ),
                    ],
                ],
            ]
        );
    }
}

// Register the add-on on the custom hook.
add_action( 'revolt/register_addons', [ ParticleEffect::class, 'register' ] );
