<?php
/**
 * Spam Master tasks.
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
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_comments_clean = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_comments_clean'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_free_unstable_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_unstable_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_unstable_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_unstable_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_full_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_full_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_full_inactive_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_inactive_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_full_inactive_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_inactive_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_free_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_expired_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_free_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_expired_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_trial_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_trial_expired_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_trial_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_trial_expired_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_new_options = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_new_options'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_ip2 = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip2'" ), 0, 48 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_cron_alert_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cron_alert_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_cron_alert_date_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cron_alert_date_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_1_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_1_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_1_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_1_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_2_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_2_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_2_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_2_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_6_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_6_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_malfunction_6_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_6_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_high_volume_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_high_volume_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_high_volume_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_high_volume_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_auto_update = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_alert_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_alert_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_alert_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_alert_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spamsenddb = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spamsenddbdatepre = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not_date'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_is_cloudflare = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_is_cloudflare'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_firewall_rules_set = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules_set'" );


// Time Frames.
$current_time = current_datetime()->format( 'Y-m-d H:i:s' );
$cache1h      = gmdate( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( $current_time ) ) );
$cache4h      = gmdate( 'Y-m-d H:i:s', strtotime( '-4 hour', strtotime( $current_time ) ) );
// Above.
$cache1d = gmdate( 'Y-m-d H:i:s', strtotime( '-1 day', strtotime( $current_time ) ) );
$cache7d = gmdate( 'Y-m-d H:i:s', strtotime( '-7 day', strtotime( $current_time ) ) );
$cache3m = gmdate( 'Y-m-d H:i:s', strtotime( '-3 months', strtotime( $current_time ) ) );
// Reduce cache 12 to 6.
$cache12m = gmdate( 'Y-m-d H:i:s', strtotime( '-6 months', strtotime( $current_time ) ) );

// Clean Up Buffer cache1h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '1H' AND time <= %s", $cache1h ) );
// Clean Up Buffer cache4h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '4H' AND time <= %s", $cache4h ) );
// Clean Up Buffer cache1d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '1D' AND time <= %s", $cache1d ) );
// Clean Up Buffer cache7d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '7D' AND time <= %s", $cache7d ) );
// Clean Up Buffer cache3m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '3M' AND time <= %s", $cache3m ) );
// Clean Up Buffer cache12m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamvalue = '12M' AND time <= %s", $cache12m ) );
// Cloudflare is true delete from cache.
if ( 'true' === $spam_master_is_cloudflare ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache'" );
}

// Clean Up White cache1h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '1H' AND time <= %s", $cache1h ) );
// Clean Up White cache4h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '4H' AND time <= %s", $cache4h ) );
// Clean Up White cache1d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '1D' AND time <= %s", $cache1d ) );
// Clean Up White cache7d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '7D' AND time <= %s", $cache7d ) );
// Clean Up White cache3m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '3M' AND time <= %s", $cache3m ) );
// Clean Up White cache12m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamvalue = '12M' AND time <= %s", $cache12m ) );

// Clean Up System cache1h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '1H' AND time <= %s", $cache1h ) );
// Clean Up System cache4h.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '4H' AND time <= %s", $cache4h ) );
// Clean Up System cache1d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '1D' AND time <= %s", $cache1d ) );
// Clean Up System cache7d.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '7D' AND time <= %s", $cache7d ) );
// Clean Up System cache3m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '3M' AND time <= %s", $cache3m ) );
// Clean Up System cache12m.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'System' AND spamvalue = '12M' AND time <= %s", $cache12m ) );

// Delete server ip and whitelist if any.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamy = %s", $spam_master_ip ) );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamy = %s", $spam_master_ip2 ) );

// Clean Up Comments & Clean-up Logs.
if ( is_multisite() ) {
	if ( 'true' === $spam_master_comments_clean ) {
		$blog_prefix = $wpdb->get_blog_prefix();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result_comments_status = $wpdb->get_results( "SELECT comment_ID,comment_author_IP,comment_author_email,comment_approved FROM {$blog_prefix}comments WHERE comment_approved = '0' OR comment_approved = '1' OR comment_approved = 'spam' OR comment_approved = 'trash'" );
		foreach ( $result_comments_status as $statusmore ) {
			$status_id     = $statusmore->comment_ID;
			$status_ip     = $statusmore->comment_author_IP;
			$status_email  = $statusmore->comment_author_email;
			$status_status = $statusmore->comment_approved;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$is_buffer_threat = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamy FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s",
					$status_ip
				)
			);
			if ( ! empty( $is_buffer_threat ) ) {
				wp_delete_comment( $status_id, false );
			}
		}
		// Clean old trashed comments.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"DELETE FROM {$blog_prefix}comments
									WHERE 
									(comment_approved = '0' OR comment_approved = '1' OR comment_approved = 'spam' OR comment_approved = 'trash')
									AND
									comment_date <= %s",
				$cache3m
			)
		);
	}
} else {
	if ( 'true' === $spam_master_comments_clean ) {
		$table_prefixdb = $wpdb->base_prefix;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result_comments_status = $wpdb->get_results( "SELECT comment_ID,comment_author_IP,comment_author_email,comment_approved FROM {$table_prefixdb}comments WHERE comment_approved = '0' OR comment_approved = '1' OR comment_approved = 'spam' OR comment_approved = 'trash'" );
		foreach ( $result_comments_status as $statusmore ) {
			$status_id     = $statusmore->comment_ID;
			$status_ip     = $statusmore->comment_author_IP;
			$status_email  = $statusmore->comment_author_email;
			$status_status = $statusmore->comment_approved;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$is_buffer_threat = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamy FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s",
					$status_ip
				)
			);
			if ( ! empty( $is_buffer_threat ) ) {
				wp_delete_comment( $status_id, false );
			}
		}
		// Clean old trashed comments.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"DELETE FROM {$table_prefixdb}comments
									WHERE 
									(comment_approved = '0' OR comment_approved = '1' OR comment_approved = 'spam' OR comment_approved = 'trash')
									AND
									comment_date <= %s",
				$cache3m
			)
		);
	}
}

// Need to make sure unstable notice is set to 0 to warn users.
$spam_master_current_date = current_datetime()->format( 'Y-m-d' );
if ( $spam_master_current_date >= $spam_master_free_unstable_date && '1' === $spam_master_unstable_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_unstable_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_high_volume_date && '1' === $spam_master_high_volume_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_high_volume_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_malfunction_1_date && '1' === $spam_master_malfunction_1_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_malfunction_1_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_malfunction_2_date && '1' === $spam_master_malfunction_2_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_malfunction_2_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_malfunction_6_date && '1' === $spam_master_malfunction_6_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_malfunction_6_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_cron_alert_date && '1' === $spam_master_cron_alert_date_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_cron_alert_date_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_full_expired_date && 'FULL' === $spam_master_type && '1' === $spam_master_full_expired_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_full_expired_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_full_inactive_date && 'FULL' === $spam_master_type && '1' === $spam_master_full_inactive_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_full_inactive_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_free_expired_date && 'FREE' === $spam_master_type && '1' === $spam_master_free_expired_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_free_expired_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_trial_expired_date && 'TRIAL' === $spam_master_type && '1' === $spam_master_trial_expired_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_trial_expired_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spam_master_emails_alert_date && '1' === $spam_master_emails_alert_notice ) {
	// Update Notice.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_emails_alert_notice',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( $spam_master_current_date >= $spamsenddbdatepre && '1' === $spamsenddb ) {
	// Update disc notification.
	$data_spam  = array( 'spamvalue' => '0' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_disc_not',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}

// Update Options.
if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {
	if ( '1' === $spam_master_new_options ) {
		// Spam Action Controller.
		$spam_master_action_controller = new SpamMasterActionController();
		$is_act                        = $spam_master_action_controller->spammastergetact();
	}
}

// Is update turned off by mistake. Security should always be up-to-date.
if ( 'false' === $spam_master_auto_update ) {
	// Update auto update.
	$data_spam  = array( 'spamvalue' => 'true' );
	$where_spam = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_auto_update',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
}
if ( 'FULL' !== $spam_master_type ) {
	$data_address  = array( 'spamvalue' => 'true' );
	$where_address = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_signature',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_address, $where_address );
	$data_address  = array( 'spamvalue' => 'false' );
	$where_address = array(
		'spamkey'  => 'Option',
		'spamtype' => 'spam_master_is_cloudflare',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->update( $spam_master_keys, $data_address, $where_address );

	if ( '1' === $spam_master_firewall_rules_set ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
		$data_address  = array( 'spamvalue' => '3' );
		$where_address = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_firewall_rules',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
}
// Clean up buffer from whitelist.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$get_whites = $wpdb->get_results( "SELECT spamy FROM $spam_master_keys WHERE spamkey = 'White'" );
if ( ! empty( $get_whites ) ) {
	foreach ( $get_whites as $the_white ) {
		$white = $the_white->spamy;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s", $white ) );
	}
}

// Log inup controller.
$remote_ip                  = $spam_master_ip;
$blog_threat_email          = 'localhost';
$remote_referer             = 'localhost';
$dest_url                   = 'localhost';
$remote_agent               = 'localhost';
$spamuser                   = array( 'ID' => 'none' );
$spamuser_a                 = wp_json_encode( $spamuser );
$spamtype                   = 'Cron Tasks';
$spamvalue                  = 'Successfully run.';
$cache                      = '7D';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

