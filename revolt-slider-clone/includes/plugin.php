<?php
/**
 * Main plugin bootstrapper.
 *
 * @package RevoltSliderClone
 */

namespace Revolt;

use Revolt\Admin\Editor;
use Revolt\Addons\Manager as AddonsManager;
use Revolt\Frontend\Renderer;
use Revolt\Rest\Modules_Controller;
use Revolt\Rest\Settings_Controller;

/**
 * Class Plugin
 */
class Plugin {
    /**
     * Initialize plugin hooks.
     *
     * @return void
     */
    public static function init() {
        $instance = new self();
        $instance->hooks();
    }

    /**
     * Register plugin hooks.
     *
     * @return void
     */
    public function hooks() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'init', [ $this, 'register_meta' ] );
        add_action( 'init', [ $this, 'register_block' ] );
        add_action( 'init', [ $this, 'load_textdomain' ] );

        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

        add_action( 'admin_enqueue_scripts', [ Editor::class, 'enqueue_assets' ] );
        add_action( 'wp_enqueue_scripts', [ Renderer::class, 'enqueue_assets' ] );

        add_shortcode( 'revolt_slider', [ Renderer::class, 'render_shortcode' ] );

        // Register REST routes on rest_api_init.
        add_action( 'rest_api_init', [ Modules_Controller::class, 'register_routes' ] );
        add_action( 'rest_api_init', [ Settings_Controller::class, 'register_routes' ] );

        // Register default add-ons immediately to expose add-on data.
        AddonsManager::bootstrap();
    }

    /**
     * Load plugin text domain.
     *
     * @return void
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'revolt-slider-clone', false, dirname( plugin_basename( REVOLT_SLIDER_CLONE_FILE ) ) . '/languages/' );
    }

    /**
     * Register custom post type for modules.
     *
     * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'               => __( 'Modules', 'revolt-slider-clone' ),
            'singular_name'      => __( 'Module', 'revolt-slider-clone' ),
            'add_new'            => __( 'Add New Module', 'revolt-slider-clone' ),
            'add_new_item'       => __( 'Add New Module', 'revolt-slider-clone' ),
            'edit_item'          => __( 'Edit Module', 'revolt-slider-clone' ),
            'new_item'           => __( 'New Module', 'revolt-slider-clone' ),
            'view_item'          => __( 'View Module', 'revolt-slider-clone' ),
            'search_items'       => __( 'Search Modules', 'revolt-slider-clone' ),
            'not_found'          => __( 'No modules found', 'revolt-slider-clone' ),
            'not_found_in_trash' => __( 'No modules found in Trash', 'revolt-slider-clone' ),
            'menu_name'          => __( 'Revolt Modules', 'revolt-slider-clone' ),
        ];

        register_post_type(
            'revolt_module',
            [
                'labels'        => $labels,
                'public'        => false,
                'show_ui'       => true,
                'show_in_menu'  => false,
                'show_in_rest'  => true,
                'supports'      => [ 'title', 'editor', 'revisions' ],
                'menu_position' => 25,
                'menu_icon'     => 'dashicons-images-alt2',
                'rewrite'       => false,
            ]
        );
    }

    /**
     * Register CPT meta for module JSON and settings.
     *
     * @return void
     */
    public function register_meta() {
        register_post_meta(
            'revolt_module',
            '_revolt_module_config',
            [
                'type'              => 'string',
                'show_in_rest'      => true,
                'single'            => true,
                'sanitize_callback' => [ Modules_Controller::class, 'sanitize_module_json' ],
                'auth_callback'     => [ Modules_Controller::class, 'can_edit_modules' ],
            ]
        );

        register_post_meta(
            'revolt_module',
            '_revolt_module_alias',
            [
                'type'              => 'string',
                'show_in_rest'      => true,
                'single'            => true,
                'sanitize_callback' => 'sanitize_key',
                'auth_callback'     => [ Modules_Controller::class, 'can_edit_modules' ],
            ]
        );

        register_post_meta(
            'revolt_module',
            '_revolt_module_type',
            [
                'type'              => 'string',
                'show_in_rest'      => true,
                'single'            => true,
                'sanitize_callback' => [ __CLASS__, 'sanitize_module_type' ],
                'auth_callback'     => [ Modules_Controller::class, 'can_edit_modules' ],
            ]
        );
    }

    /**
     * Sanitize module type.
     *
     * @param string $value Raw type.
     *
     * @return string
     */
    public static function sanitize_module_type( $value ) {
        $allowed = [ 'slider', 'carousel', 'hero', 'page' ];
        $value   = sanitize_key( $value );

        return in_array( $value, $allowed, true ) ? $value : 'slider';
    }

    /**
     * Register admin menu.
     *
     * @return void
     */
    public function register_admin_menu() {
        add_menu_page(
            __( 'Revolt Slider', 'revolt-slider-clone' ),
            __( 'Revolt Slider', 'revolt-slider-clone' ),
            'edit_revolt_modules',
            'revolt-slider-clone',
            [ Editor::class, 'render_app' ],
            'dashicons-images-alt2',
            25
        );

        add_submenu_page(
            'revolt-slider-clone',
            __( 'Modules', 'revolt-slider-clone' ),
            __( 'Modules', 'revolt-slider-clone' ),
            'edit_revolt_modules',
            'revolt-slider-clone',
            [ Editor::class, 'render_app' ]
        );

        add_submenu_page(
            'revolt-slider-clone',
            __( 'Templates', 'revolt-slider-clone' ),
            __( 'Templates', 'revolt-slider-clone' ),
            'edit_revolt_modules',
            'revolt-slider-templates',
            [ Editor::class, 'render_templates_page' ]
        );

        add_submenu_page(
            'revolt-slider-clone',
            __( 'Add-ons', 'revolt-slider-clone' ),
            __( 'Add-ons', 'revolt-slider-clone' ),
            'edit_revolt_modules',
            'revolt-slider-addons',
            [ Editor::class, 'render_addons_page' ]
        );

        add_submenu_page(
            'revolt-slider-clone',
            __( 'Settings', 'revolt-slider-clone' ),
            __( 'Settings', 'revolt-slider-clone' ),
            'manage_options',
            'revolt-slider-settings',
            [ Editor::class, 'render_settings_page' ]
        );
    }

    /**
     * Register custom block type placeholder.
     *
     * @return void
     */
    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type(
            'revolt/module',
            [
                'api_version'     => 2,
                'editor_script'   => 'revolt-block-editor',
                'render_callback' => [ Renderer::class, 'render_block' ],
                'attributes'      => [
                    'moduleId' => [
                        'type' => 'integer',
                    ],
                ],
            ]
        );
    }

    /**
     * Enqueue block editor assets.
     *
     * @return void
     */
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'revolt-block-editor',
            REVOLT_SLIDER_CLONE_URL . 'assets/dist/block.bundle.js',
            [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-data' ],
            REVOLT_SLIDER_CLONE_VERSION,
            true
        );

        wp_enqueue_style(
            'revolt-block-editor',
            REVOLT_SLIDER_CLONE_URL . 'assets/css/block-editor.css',
            [ 'wp-edit-blocks' ],
            REVOLT_SLIDER_CLONE_VERSION
        );
    }
}
