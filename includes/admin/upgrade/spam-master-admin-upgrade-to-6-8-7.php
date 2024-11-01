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

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_signature_registration',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_signature_login',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_signature_comments',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_signature_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'false' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_alert_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_alert_3_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		$data_spam  = array( 'spamvalue' => 'true' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_weekly_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_6_8_7', '1' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_signature_registration',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_signature_login',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_signature_comments',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_signature_email',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'false' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_emails_alert_email',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_emails_alert_3_email',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_emails_weekly_email',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

	// Update.
	update_option( 'spam_master_upgrade_to_6_8_7', '1' );
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
$spamvalue                  = 'Plugin Install or Upgrade Tasks Done 6_8_7.';
$cache                      = '4H';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

