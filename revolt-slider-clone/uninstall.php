<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package RevoltSliderClone
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$modules = get_posts(
    [
        'post_type'      => 'revolt_module',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ]
);

foreach ( $modules as $module_id ) {
    wp_delete_post( $module_id, true );
}
