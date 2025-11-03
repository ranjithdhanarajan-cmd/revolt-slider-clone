<?php
/**
 * Front-end renderer for modules.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Frontend;

use Revolt\Schema\Module_Schema;

/**
 * Renders module markup.
 */
class Renderer {
    /**
     * Enqueue front-end assets when needed.
     *
     * @return void
     */
    public static function enqueue_assets() {
        if ( ! self::should_enqueue() ) {
            return;
        }

        wp_enqueue_style(
            'revolt-frontend',
            REVOLT_SLIDER_CLONE_URL . 'assets/css/frontend.css',
            [],
            REVOLT_SLIDER_CLONE_VERSION
        );

        wp_enqueue_script(
            'revolt-frontend',
            REVOLT_SLIDER_CLONE_URL . 'assets/dist/frontend.bundle.js',
            [ 'wp-element' ],
            REVOLT_SLIDER_CLONE_VERSION,
            true
        );
    }

    /**
     * Determine if assets should be enqueued on current request.
     *
     * @return bool
     */
    protected static function should_enqueue() {
        global $post;

        if ( has_shortcode( $post->post_content ?? '', 'revolt_slider' ) ) {
            return true;
        }

        return did_action( 'render_block' ) && ! empty( $GLOBALS['revolt_enqueue_assets'] );
    }

    /**
     * Render module via shortcode.
     *
     * @param array<string,string> $atts Shortcode attributes.
     *
     * @return string
     */
    public static function render_shortcode( $atts ) {
        $atts = shortcode_atts(
            [
                'id'    => '',
                'alias' => '',
            ],
            $atts,
            'revolt_slider'
        );

        $module = self::get_module_by_atts( $atts );

        if ( ! $module ) {
            return '';
        }

        $GLOBALS['revolt_enqueue_assets'] = true;

        return self::render_module( $module );
    }

    /**
     * Render block callback.
     *
     * @param array<string,mixed> $attributes Block attributes.
     *
     * @return string
     */
    public static function render_block( $attributes ) {
        $module_id = isset( $attributes['moduleId'] ) ? absint( $attributes['moduleId'] ) : 0;

        if ( ! $module_id ) {
            return '';
        }

        $post = get_post( $module_id );
        if ( ! $post || 'revolt_module' !== $post->post_type ) {
            return '';
        }

        $GLOBALS['revolt_enqueue_assets'] = true;

        return self::render_module(
            [
                'id'     => $post->ID,
                'title'  => $post->post_title,
                'alias'  => get_post_meta( $post->ID, '_revolt_module_alias', true ),
                'type'   => get_post_meta( $post->ID, '_revolt_module_type', true ),
                'config' => json_decode( get_post_meta( $post->ID, '_revolt_module_config', true ), true ),
            ]
        );
    }

    /**
     * Fetch module by shortcode attributes.
     *
     * @param array<string,string> $atts Attributes.
     *
     * @return array<string,mixed>|null
     */
    protected static function get_module_by_atts( $atts ) {
        if ( ! empty( $atts['id'] ) ) {
            $post = get_post( absint( $atts['id'] ) );
            if ( $post && 'revolt_module' === $post->post_type ) {
                return [
                    'id'     => $post->ID,
                    'title'  => $post->post_title,
                    'alias'  => get_post_meta( $post->ID, '_revolt_module_alias', true ),
                    'type'   => get_post_meta( $post->ID, '_revolt_module_type', true ),
                    'config' => json_decode( get_post_meta( $post->ID, '_revolt_module_config', true ), true ),
                ];
            }
        }

        if ( ! empty( $atts['alias'] ) ) {
            $query = new \WP_Query(
                [
                    'post_type'      => 'revolt_module',
                    'meta_key'       => '_revolt_module_alias',
                    'meta_value'     => sanitize_key( $atts['alias'] ),
                    'post_status'    => 'publish',
                    'fields'         => 'ids',
                    'posts_per_page' => 1,
                ]
            );

            if ( $query->have_posts() ) {
                $post = get_post( $query->posts[0] );
                if ( $post ) {
                    return [
                        'id'     => $post->ID,
                        'title'  => $post->post_title,
                        'alias'  => get_post_meta( $post->ID, '_revolt_module_alias', true ),
                        'type'   => get_post_meta( $post->ID, '_revolt_module_type', true ),
                        'config' => json_decode( get_post_meta( $post->ID, '_revolt_module_config', true ), true ),
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Render module markup from config array.
     *
     * @param array<string,mixed> $module Module data.
     *
     * @return string
     */
    protected static function render_module( $module ) {
        $config = $module['config'] ?: Module_Schema::get_default_module( $module['type'] ?? 'slider' );
        $slides = $config['slides'] ?? [];

        ob_start();
        ?>
        <div class="revolt-module revolt-module--<?php echo esc_attr( $module['type'] ); ?>" data-module-id="<?php echo esc_attr( $module['id'] ); ?>" data-module-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
            <div class="revolt-module__viewport">
                <?php foreach ( $slides as $index => $slide ) : ?>
                    <div class="revolt-slide" data-slide-index="<?php echo esc_attr( $index ); ?>">
                        <?php echo self::render_slide_background( $slide ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <div class="revolt-slide__layers">
                            <?php foreach ( $slide['layers'] ?? [] as $layer ) : ?>
                                <?php echo self::render_layer( $layer ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Render slide background markup.
     *
     * @param array<string,mixed> $slide Slide config.
     *
     * @return string
     */
    protected static function render_slide_background( $slide ) {
        $background = $slide['background'] ?? [];
        $style      = [];

        if ( ! empty( $background['color'] ) ) {
            $style[] = 'background-color:' . sanitize_hex_color( $background['color'] );
        }

        if ( ! empty( $background['image'] ) ) {
            $style[] = 'background-image:url(' . esc_url( $background['image'] ) . ')';
        }

        $style_attr = empty( $style ) ? '' : ' style="' . esc_attr( implode( ';', $style ) ) . '"';

        $html  = '<div class="revolt-slide__background"' . $style_attr . '>';
        if ( ! empty( $background['video'] ) ) {
            $html .= sprintf( '<video class="revolt-slide__video" autoplay muted loop playsinline><source src="%s" type="video/mp4"></video>', esc_url( $background['video'] ) );
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a layer.
     *
     * @param array<string,mixed> $layer Layer config.
     *
     * @return string
     */
    protected static function render_layer( $layer ) {
        $type       = sanitize_key( $layer['type'] ?? 'text' );
        $content    = $layer['content'] ?? [];
        $style      = $layer['style'] ?? [];
        $animations = $layer['animations'] ?? [];
        $classes    = [ 'revolt-layer', 'revolt-layer--' . $type ];

        if ( ! empty( $style['className'] ) ) {
            $classes[] = sanitize_html_class( $style['className'] );
        }

        $style_attr = self::inline_style( $style );

        $data_attr = ' data-animations="' . esc_attr( wp_json_encode( $animations ) ) . '"';

        switch ( $type ) {
            case 'image':
                $html = sprintf( '<img src="%s" alt="%s" class="%s"%s%s loading="lazy"/>', esc_url( $content['src'] ?? '' ), esc_attr( $content['alt'] ?? '' ), esc_attr( implode( ' ', $classes ) ), $style_attr, $data_attr );
                break;
            case 'button':
                $html = sprintf( '<a href="%s" class="%s"%s%s>%s</a>', esc_url( $content['url'] ?? '#' ), esc_attr( implode( ' ', $classes ) ), $style_attr, $data_attr, wp_kses_post( $content['text'] ?? '' ) );
                break;
            case 'video':
                $html = sprintf( '<div class="%s"%s%s><video controls preload="metadata"><source src="%s" type="video/mp4"></video></div>', esc_attr( implode( ' ', $classes ) ), $style_attr, $data_attr, esc_url( $content['src'] ?? '' ) );
                break;
            default:
                $html = sprintf( '<div class="%s"%s%s>%s</div>', esc_attr( implode( ' ', $classes ) ), $style_attr, $data_attr, wp_kses_post( $content['text'] ?? '' ) );
                break;
        }

        return $html;
    }

    /**
     * Convert style array to inline CSS.
     *
     * @param array<string,mixed> $style Style array.
     *
     * @return string
     */
    protected static function inline_style( $style ) {
        $map = [
            'top'      => 'top',
            'left'     => 'left',
            'width'    => 'width',
            'height'   => 'height',
            'zIndex'   => 'z-index',
            'opacity'  => 'opacity',
            'color'    => 'color',
            'fontSize' => 'font-size',
        ];

        $styles = [];
        foreach ( $map as $key => $css ) {
            if ( isset( $style[ $key ] ) ) {
                $value    = is_numeric( $style[ $key ] ) ? $style[ $key ] . 'px' : $style[ $key ];
                $styles[] = $css . ':' . sanitize_text_field( $value );
            }
        }

        return empty( $styles ) ? '' : ' style="' . esc_attr( implode( ';', $styles ) ) . '"';
    }
}
