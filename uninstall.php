<?php
/**
 * Uninstall tasks.
 *
 * @package Spam Master
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}
if ( ! function_exists( 'wp_get_current_user' ) ) {
	include ABSPATH . 'wp-includes/pluggable.php';
}
global $wpdb, $blog_id, $current_user;
if ( ( is_user_logged_in() ) && ( current_user_can( 'administrator' ) ) ) {
	if ( is_multisite() ) {
		// Delete spam master table and options.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
		foreach ( $blogs as $idb ) {
			$table_keys = $wpdb->get_blog_prefix( $idb ) . 'spam_master_keys';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DROP TABLE IF EXISTS $table_keys" );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_0' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_1' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_2' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_3' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_5' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_6' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_6_19' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_7_0' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_7_2' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_7_6' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_8_5' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_8_6' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_8_7' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_6_9_8' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_1_1' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_1_2' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_2_7' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_2_8' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_2_9' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_3_1' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_3_2' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_3_6' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_3_7' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_4_0' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_4_1' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_4_5' );
			delete_blog_option( $idb, 'spam_master_upgrade_to_7_4_6' );
			delete_blog_option( $idb, 'spam_master_db_version' );
			delete_blog_option( $idb, 'spam_master_keys_db_version' );
			delete_blog_option( $idb, 'spam_master_connection' );
		}
	} else {
		// Delete spam master table and options.
		$table_keys = $wpdb->prefix . 'spam_master_keys';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS $table_keys" );
		delete_option( 'spam_master_upgrade_to_6' );
		delete_option( 'spam_master_upgrade_to_6_6_0' );
		delete_option( 'spam_master_upgrade_to_6_6_1' );
		delete_option( 'spam_master_upgrade_to_6_6_2' );
		delete_option( 'spam_master_upgrade_to_6_6_3' );
		delete_option( 'spam_master_upgrade_to_6_6_5' );
		delete_option( 'spam_master_upgrade_to_6_6_6' );
		delete_option( 'spam_master_upgrade_to_6_6_19' );
		delete_option( 'spam_master_upgrade_to_6_7_0' );
		delete_option( 'spam_master_upgrade_to_6_7_2' );
		delete_option( 'spam_master_upgrade_to_6_7_6' );
		delete_option( 'spam_master_upgrade_to_6_8_5' );
		delete_option( 'spam_master_upgrade_to_6_8_6' );
		delete_option( 'spam_master_upgrade_to_6_8_7' );
		delete_option( 'spam_master_upgrade_to_6_9_8' );
		delete_option( 'spam_master_upgrade_to_7_1_1' );
		delete_option( 'spam_master_upgrade_to_7_1_2' );
		delete_option( 'spam_master_upgrade_to_7_2_7' );
		delete_option( 'spam_master_upgrade_to_7_2_8' );
		delete_option( 'spam_master_upgrade_to_7_2_9' );
		delete_option( 'spam_master_upgrade_to_7_3_1' );
		delete_option( 'spam_master_upgrade_to_7_3_2' );
		delete_option( 'spam_master_upgrade_to_7_3_6' );
		delete_option( 'spam_master_upgrade_to_7_3_7' );
		delete_option( 'spam_master_upgrade_to_7_4_0' );
		delete_option( 'spam_master_upgrade_to_7_4_1' );
		delete_option( 'spam_master_upgrade_to_7_4_5' );
		delete_option( 'spam_master_upgrade_to_7_4_6' );
		delete_option( 'spam_master_db_version' );
		delete_option( 'spam_master_keys_db_version' );
		delete_option( 'spam_master_connection' );
	}
}
