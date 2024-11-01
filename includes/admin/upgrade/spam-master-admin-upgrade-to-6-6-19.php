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
		// Spam Master Firewall Page.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_spam_master_firewall_page = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_page'" );
		if ( empty( $is_spam_master_firewall_page ) ) {
			$spam_master_temp_firewall_page = get_site_url() . '/wp-content/plugins/spam-master/includes/protection/spam-master-admin-other-protection-frontend-firewall.html';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_firewall_page',
					'spamy'     => 'localhost',
					'spamvalue' => $spam_master_temp_firewall_page,
				)
			);
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_6_6_19', '1' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	// Spam Master Firewall Page.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$is_spam_master_firewall_page = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_page'" );
	if ( empty( $is_spam_master_firewall_page ) ) {
		$spam_master_temp_firewall_page = get_site_url() . '/wp-content/plugins/spam-master/includes/protection/spam-master-admin-other-protection-frontend-firewall.html';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_firewall_page',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_temp_firewall_page,
			)
		);
	}

	// Update.
	update_option( 'spam_master_upgrade_to_6_6_19', '1' );
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
$spamvalue                  = 'Plugin Install or Upgrade Tasks Done.';
$cache                      = '4H';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

/**
 * Flush htaccess.
 *
 * @param wp_rewrite $wp_rewrite for rewrite.
 *
 * @return void
 */
function spam_master_flush_rewrites( $wp_rewrite ) {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
add_action( 'admin_init', 'spam_master_flush_rewrites' );
