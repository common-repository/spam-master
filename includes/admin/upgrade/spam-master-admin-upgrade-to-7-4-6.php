<?php
/**
 * Update script.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;

if ( is_multisite() ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
	foreach ( $blogs as $idb ) {
		// Update DB.
		$spam_master_keys = $wpdb->get_blog_prefix( $idb ) . 'spam_master_keys';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$security = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
		if ( isset( $security ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$queue = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'queue'" );
		if ( isset( $queue ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'queue'" );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$cart = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'cart'" );
		if ( isset( $cart ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'cart'" );
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_7_4_6', '1' );
		update_blog_option( $idb, 'spam_master_db_version', '746' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$security = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
	if ( isset( $security ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$queue = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'queue'" );
	if ( isset( $queue ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'queue'" );
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$cart = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'cart'" );
	if ( isset( $cart ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'cart'" );
	}

	// Update.
	update_option( 'spam_master_upgrade_to_7_4_6', '1' );
	update_option( 'spam_master_db_version', '746' );
}

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_ip = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" );

// Log inup controller.
$remote_ip                  = $spam_master_ip;
$blog_threat_email          = 'localhost';
$remote_referer             = 'localhost';
$dest_url                   = 'localhost';
$remote_agent               = 'localhost';
$spamuser                   = array( 'ID' => 'none' );
$spamuser_a                 = wp_json_encode( $spamuser );
$spamtype                   = 'Upgraded';
$spamvalue                  = 'Plugin Install or Upgrade Tasks Done 7_4_6.';
$cache                      = '4H';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
