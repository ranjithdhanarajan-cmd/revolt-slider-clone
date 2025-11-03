<?php
/**
 * Admin editor bootstrap.
 *
 * @package RevoltSliderClone
 */

namespace Revolt\Admin;

use Revolt\Addons\Manager as AddonsManager;
use Revolt\Schema\Module_Schema;
use Revolt\Settings\Options;

/**
 * Handles rendering and assets for the admin SPA.
 */
class Editor {
    /**
     * Output the admin app container.
     *
     * @return void
     */
    public static function render_app() {
        echo '<div class="revolt-app" id="revolt-slider-app"></div>';
    }

    /**
     * Render templates page placeholder.
     *
     * @return void
     */
    public static function render_templates_page() {
        echo '<div class="revolt-app" id="revolt-templates-app"></div>';
    }

    /**
     * Render add-ons page placeholder.
     *
     * @return void
     */
    public static function render_addons_page() {
        echo '<div class="revolt-app" id="revolt-addons-app"></div>';
    }

    /**
     * Render settings page placeholder.
     *
     * @return void
     */
    public static function render_settings_page() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Revolt Slider Settings', 'revolt-slider-clone' ) . '</h1><div id="revolt-settings-app"></div></div>';
    }

    /**
     * Enqueue admin assets for editor.
     *
     * @return void
     */
    public static function enqueue_assets() {
        $screen = get_current_screen();

        if ( empty( $screen ) || false === strpos( $screen->base, 'revolt-slider' ) ) {
            return;
        }

        wp_enqueue_style(
            'revolt-admin',
            REVOLT_SLIDER_CLONE_URL . 'assets/css/admin.css',
            [],
            REVOLT_SLIDER_CLONE_VERSION
        );

        wp_enqueue_script(
            'revolt-admin',
            REVOLT_SLIDER_CLONE_URL . 'assets/dist/admin.bundle.js',
            [ 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch' ],
            REVOLT_SLIDER_CLONE_VERSION,
            true
        );

        wp_localize_script(
            'revolt-admin',
            'RevoltSliderData',
            [
                'nonce'     => wp_create_nonce( 'wp_rest' ),
                'restUrl'   => esc_url_raw( rest_url( 'revolt/v1' ) ),
                'schema'    => Module_Schema::get_schema(),
                'templates' => self::get_templates(),
                'addons'    => AddonsManager::get_registered_addons(),
                'modules'   => self::get_modules_summary(),
                'settings'  => Options::get_settings(),
                'adminUrls' => self::get_admin_urls(),
                'i18n'      => [
                    'createModule' => __( 'Create Module', 'revolt-slider-clone' ),
                    'noModules'    => __( 'No modules yet. Create your first one!', 'revolt-slider-clone' ),
                ],
            ]
        );
    }

    /**
     * Return registered templates.
     *
     * @return array<int,array<string,mixed>>
     */
    protected static function get_templates() {
        $templates_dir = trailingslashit( REVOLT_SLIDER_CLONE_PATH ) . 'templates/';
        $files         = glob( $templates_dir . '*.json' );
        $templates     = [];

        foreach ( $files as $file ) {
            $content = file_get_contents( $file );
            if ( ! $content ) {
                continue;
            }

            $templates[] = [
                'slug'    => basename( $file, '.json' ),
                'data'    => json_decode( $content, true ),
                'preview' => REVOLT_SLIDER_CLONE_URL . 'assets/img/' . basename( $file, '.json' ) . '.jpg',
            ];
        }

        return $templates;
    }

    /**
     * Retrieve module summaries for the list view.
     *
     * @return array<int,array<string,mixed>>
     */
    protected static function get_modules_summary() {
        $posts = get_posts(
            [
                'post_type'      => 'revolt_module',
                'posts_per_page' => 100,
                'post_status'    => 'any',
                'orderby'        => 'modified',
                'order'          => 'DESC',
            ]
        );

        return array_map(
            static function ( $post ) {
                return [
                    'id'       => $post->ID,
                    'title'    => $post->post_title,
                    'type'     => get_post_meta( $post->ID, '_revolt_module_type', true ),
                    'alias'    => get_post_meta( $post->ID, '_revolt_module_alias', true ),
                    'modified' => $post->post_modified,
                ];
            },
            $posts
        );
    }

    /**
     * Helper for commonly used admin URLs.
     *
     * @return array<string,string>
     */
    protected static function get_admin_urls() {
        return [
            'modulesList' => admin_url( 'admin.php?page=revolt-slider-clone' ),
            'newModule'   => admin_url( 'post-new.php?post_type=revolt_module' ),
            'templates'   => admin_url( 'admin.php?page=revolt-slider-templates' ),
            'addons'      => admin_url( 'admin.php?page=revolt-slider-addons' ),
            'settings'    => admin_url( 'admin.php?page=revolt-slider-settings' ),
            'editModule'  => admin_url( 'post.php' ),
        ];
    }
}
