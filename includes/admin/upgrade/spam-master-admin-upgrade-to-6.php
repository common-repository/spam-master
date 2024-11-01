<?php
/**
 * Update script.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
if ( is_multisite() ) {
	// Firewall delete expired transients.
	$sql_firewall_transients_timeout = "delete from t1, t2
										using {$wpdb->sitemeta} t1
										join {$wpdb->sitemeta} t2 on t2.meta_key = replace(t1.meta_key, '_timeout', '')
										where (t1.meta_key like '\_transient\_timeout\_spam_master_firewall_ip%' or t1.meta_key like '\_site\_transient\_timeout\_spam_master_firewall_ip%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_firewall_transients_timeout );

	$sql_firewall_transients = "delete from {$wpdb->sitemeta}
								where (
								meta_key like '\_transient\_timeout\_spam_master_firewall_ip%'
								or meta_key like '\_site\_transient\_timeout\_spam_master_firewall_ip%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_firewall_transients );

	// Invalid delete expired transients.
	$sql_invalid_transients_timeout = "delete from t1, t2
										using {$wpdb->sitemeta} t1
										join {$wpdb->sitemeta} t2 on t2.meta_key = replace(t1.meta_key, '_timeout', '')
										where (t1.meta_key like '\_transient\_timeout\_spam_master_invalid_email%' or t1.meta_key like '\_site\_transient\_timeout\_spam_master_invalid_email%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_invalid_transients_timeout );

	$sql_invalid_transients = "delete from {$wpdb->sitemeta}
								where (
								meta_key like '\_transient\_timeout\_spam_master_invalid_email%'
								or meta_key like '\_site\_transient\_timeout\_spam_master_invalid_email%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_invalid_transients );

	// R-check tables.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$blogs                  = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
	$spam_master_db_version = '1.0';
	$charset_collati        = $wpdb->get_charset_collate();
	foreach ( $blogs as $idb ) {
		// Delete Blacklist.
		delete_blog_option( $idb, 'blacklist_keys' );
		delete_blog_option( $idb, 'blacklist_keys_bk' );
		// Delete White List.
		delete_blog_option( $idb, 'spam_master_whitelist' );
		update_blog_option( $idb, 'spam_master_upgrade_to_6', '1' );
	}
} else {
	// Firewall delete expired transients.
	$sql_firewall_transients_timeout = "delete from t1, t2
										using {$wpdb->options} t1
										join {$wpdb->options} t2 on t2.option_name = replace(t1.option_name, '_timeout', '')
										where (t1.option_name like '\_transient\_timeout\_spam_master_firewall_ip%' or t1.option_name like '\_site\_transient\_timeout\_spam_master_firewall_ip%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_firewall_transients_timeout );

	$sql_firewall_transients = "delete from {$wpdb->options}
								where (
								option_name like '\_transient\_timeout\_spam_master_firewall_ip%'
								or option_name like '\_site\_transient\_timeout\_spam_master_firewall_ip%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_firewall_transients );

	// Invalid delete expired transients.
	$sql_invalid_transients_timeout = "delete from t1, t2
										using {$wpdb->options} t1
										join {$wpdb->options} t2 on t2.option_name = replace(t1.option_name, '_timeout', '')
										where (t1.option_name like '\_transient\_timeout\_spam_master_invalid_email%' or t1.option_name like '\_site\_transient\_timeout\_spam_master_invalid_email%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_invalid_transients_timeout );

	$sql_invalid_transients = "delete from {$wpdb->options}
								where (
								option_name like '\_transient\_timeout\_spam_master_invalid_email%'
								or option_name like '\_site\_transient\_timeout\_spam_master_invalid_email%');";
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql_invalid_transients );

	// Delete Blacklist.
	delete_option( 'blacklist_keys' );
	delete_option( 'blacklist_keys_bk' );
	// Delete White List.
	delete_option( 'spam_master_whitelist' );
	update_option( 'spam_master_upgrade_to_6', '1' );
}

