<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Theme Updater
 */

// Includes the files needed for the theme updater
if ( !class_exists( 'EDD_Plugin_Updater_Admin' ) ) {
	require_once TR_GRABBER_PLUGIN_DIR . 'helpers/theme-updater/updater/theme-updater-admin.php';
}

// Loads the updater classes
$updater = new EDD_Plugin_Updater_Admin(

	// Config settings
	$config = array(
		'remote_api_url' => 'https://torothemes.com', // Site where EDD is hosted
		'item_name'      => 'toroflix', // Name of theme
		'theme_slug'     => get_template(), // Theme slug
		'version'        => '1.0.0', // The current version of this theme
		'author'         => 'Toro'
	),

	// Strings
	$strings = array(
		'theme-license' => __( 'Theme License', 'toroflix' ),
		'enter-key' => __( 'Enter your theme license key.', 'toroflix' ),
		'license-key' => __( 'License Key', 'toroflix' ),
		'license-action' => __( 'License Action', 'toroflix' ),
		'deactivate-license' => __( 'Deactivate License', 'toroflix' ),
		'activate-license' => __( 'Activate License', 'toroflix' ),
		'status-unknown' => __( 'License status is unknown.', 'toroflix' ),
		'renew' => __( 'Renew?', 'toroflix' ),
		'unlimited' => __( 'unlimited', 'toroflix' ),
		'license-key-is-active' => __( 'License key is active.', 'toroflix' ),
		'expires%s' => __( 'Expires %s.', 'toroflix' ),
		'%1$s/%2$-sites' => __( 'You have %1$s / %2$s sites activated.', 'toroflix' ),
		'license-key-expired-%s' => __( 'License key expired %s.', 'toroflix' ),
		'license-key-expired' => __( 'License key has expired.', 'toroflix' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'toroflix' ),
		'license-is-inactive' => __( 'License is inactive.', 'toroflix' ),
		'license-key-is-disabled' => __( 'License key is disabled.', 'toroflix' ),
		'site-is-inactive' => __( 'Site is inactive.', 'toroflix' ),
		'license-status-unknown' => __( 'License status is unknown.', 'toroflix' ),
		'update-notice' => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'toroflix' ),
		'update-available' => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'toroflix' )
	)

);