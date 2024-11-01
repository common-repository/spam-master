<?php
/**
 * Load spam master update.
 *
 * @package Spam Master
 */

/**
 * Function to update spam master.
 *
 * @param update $update for update.
 * @param item   $item for update.
 *
 * @return true|update
 */
function spam_master_auto_update( $update, $item ) {
	global $wpdb, $blog_id;

	// Add Table & Load Spam Master Options.
	if ( is_multisite() ) {
		$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
	} else {
		$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_auto_update = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" );

	$plugins = array(
		'spam-master',
	);
	if ( 'true' === $spam_master_auto_update && in_array( $item->slug, $plugins, true ) ) {
		return true;
	} else {
		return $update;
	}
}
add_filter( 'auto_update_plugin', 'spam_master_auto_update', 10, 2 );

