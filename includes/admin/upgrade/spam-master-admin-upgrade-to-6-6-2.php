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
		// DB Protection hash.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_there = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" );
		if ( empty( $is_there ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
			$spam_master_db_protection_hash = substr( md5( uniqid( mt_rand(), true ) ), 0, 64 );
			if ( empty( $spam_master_db_protection_hash ) ) {
				$spam_master_db_protection_hash = 'md5-' . gmdate( 'YmdHis' );
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_db_protection_hash',
					'spamy'     => 'localhost',
					'spamvalue' => $spam_master_db_protection_hash,
				)
			);
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_6_6_2', '1' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	// DB Protection hash.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$is_there = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" );
	if ( empty( $is_there ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		$spam_master_db_protection_hash = substr( md5( uniqid( mt_rand(), true ) ), 0, 64 );
		if ( empty( $spam_master_db_protection_hash ) ) {
			$spam_master_db_protection_hash = 'md5-' . gmdate( 'YmdHis' );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_db_protection_hash',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_db_protection_hash,
			)
		);
	}

	// Update.
	update_option( 'spam_master_upgrade_to_6_6_2', '1' );
}

