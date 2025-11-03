<?php
/**
 * Plugin Name:       Revolt Slider Clone
 * Plugin URI:        https://example.com/plugins/revolt-slider-clone
 * Description:       Visual drag-and-drop slider builder inspired by Slider Revolution.
 * Version:           0.1.0
 * Author:            Your Name
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       revolt-slider-clone
 * Domain Path:       /languages
 */

define( 'REVOLT_SLIDER_CLONE_FILE', __FILE__ );
define( 'REVOLT_SLIDER_CLONE_PATH', plugin_dir_path( __FILE__ ) );
define( 'REVOLT_SLIDER_CLONE_URL', plugin_dir_url( __FILE__ ) );
define( 'REVOLT_SLIDER_CLONE_VERSION', '0.1.0' );

autoload_revolt_slider_clone();

/**
 * Register the autoloader for plugin classes.
 *
 * @return void
 */
function autoload_revolt_slider_clone() {
    spl_autoload_register(
        static function ( $class ) {
            if ( 0 !== strpos( $class, 'Revolt\\' ) ) {
                return;
            }

            $relative = strtolower( str_replace( [ 'Revolt\\', '\\' ], [ '', '/' ], $class ) );
            $path     = REVOLT_SLIDER_CLONE_PATH . 'includes/' . $relative . '.php';

            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }
    );
}

// Bootstrap the plugin after all plugins are loaded.
add_action( 'plugins_loaded', [ '\\Revolt\\Plugin', 'init' ] );
