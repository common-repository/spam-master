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
		// White Empath.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_there = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_white_empath'" );
		if ( empty( $is_there ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_white_empath',
					'spamy'     => 'localhost',
					'spamvalue' => 'false',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_there1 = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_stats_date'" );
		if ( empty( $is_there1 ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_emails_weekly_stats_date',
					'spamy'     => 'localhost',
					'spamvalue' => '1970-01-01',
				)
			);
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_6_6_5', '1' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	// White Empath.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$is_there = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_white_empath'" );
	if ( empty( $is_there ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_white_empath',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$is_there1 = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_stats_date'" );
	if ( empty( $is_there1 ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_weekly_stats_date',
				'spamy'     => 'localhost',
				'spamvalue' => '1970-01-01',
			)
		);
	}

	// Update.
	update_option( 'spam_master_upgrade_to_6_6_5', '1' );
}
