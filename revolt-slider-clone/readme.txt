=== Revolt Slider Clone ===
Contributors: your-name
Requires at least: 6.3
Tested up to: 6.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modular, extensible slider builder with a visual React editor inspired by Slider Revolution.

== Description ==

Revolt Slider Clone provides a drag-and-drop editor for building sliders, carousels, hero sections, and one-page modules. It features layers, timeline-based animations, responsive previews, template library, and an add-on system for extending layer types and effects.

== Installation ==

1. Upload the `revolt-slider-clone` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` screen in WordPress.
3. Navigate to `Revolt Slider` in the admin menu to launch the editor.
4. Create a module, customize slides and layers, and embed the module using the `[revolt_slider]` shortcode or Gutenberg block.

== Frequently Asked Questions ==

= Does the plugin load assets on every page? =

No, the front-end assets are only loaded when a module shortcode or block is present on the page.

= Can developers extend the plugin? =

Yes, developers can hook into `revolt/register_addons` to register new layer types, animations, and effects.

== Changelog ==

= 0.1.0 =
* Initial scaffold with custom post type, REST endpoints, React editor, templates, shortcode, block, and add-on system.

== Upgrade Notice ==

= 0.1.0 =
Initial release.
