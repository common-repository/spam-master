<?php
/**
 * Load amp check functions for recaptcha.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
// Add Table & Load Spam Master Options.
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_amp_check_fun = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_amp_check_fun'" );

if ( 'true' === $spam_master_amp_check_fun ) {

	/**
	 * Check if is_amp_endpoint.
	 *
	 * @return true|false
	 */
	function spam_master_amp_check() {
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return 'true';
		} else {
			return 'false';
		}
	}
} else {

	/**
	 * Is amp endpoint.
	 *
	 * @return false
	 */
	function spam_master_amp_check() {
		return 'false';
	}
}

