<?php
/**
 * This is just a demonstration of how theme licensing works with
 * Easy Digital Downloads.
 *
 * @package EDD Sample Theme
 */

/**
 * Load theme updater functions.
 * Action is used so that child themes can easily disable.
 */

function prefix_plugin_updater() {
	require_once TR_GRABBER_PLUGIN_DIR . 'helpers/theme-updater/updater/theme-updater.php';
}
add_action( 'after_setup_theme', 'prefix_plugin_updater' );