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

		// Actions.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wp_remove_post_lock = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wp-remove-post-lock'" );
		if ( ! isset( $wp_remove_post_lock ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'wp-remove-post-lock',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$generate_password = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'generate-password'" );
		if ( ! isset( $generate_password ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'generate-password',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$ai1wm_export = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'ai1wm_export'" );
		if ( ! isset( $ai1wm_export ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'ai1wm_export',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_ajax_test = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_ajax_test'" );
		if ( ! isset( $mmp_ajax_test ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_ajax_test',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$get_remaining_attempts_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'get_remaining_attempts_message'" );
		if ( ! isset( $get_remaining_attempts_message ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'get_remaining_attempts_message',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$h5vp_ajax_handler = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'h5vp_ajax_handler'" );
		if ( ! isset( $h5vp_ajax_handler ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'h5vp_ajax_handler',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$tve_dash_front_ajax = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'tve_dash_front_ajax'" );
		if ( ! isset( $tve_dash_front_ajax ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'tve_dash_front_ajax',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$dokan_pageview = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'dokan_pageview'" );
		if ( ! isset( $dokan_pageview ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'dokan_pageview',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$srs_update_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'srs_update_counter'" );
		if ( ! isset( $srs_update_counter ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'srs_update_counter',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$pys_api_event = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'pys_api_event'" );
		if ( ! isset( $pys_api_event ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'pys_api_event',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpil_link_clicked = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wpil_link_clicked'" );
		if ( ! isset( $wpil_link_clicked ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'wpil_link_clicked',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$feed_locator = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'feed_locator'" );
		if ( ! isset( $feed_locator ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'feed_locator',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$revslider_ajax_call_front = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'revslider_ajax_call_front'" );
		if ( ! isset( $revslider_ajax_call_front ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'revslider_ajax_call_front',
				)
			);
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_7_3_2', '1' );
		update_blog_option( $idb, 'spam_master_db_version', '732' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';

	// Actions.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wp_remove_post_lock = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wp-remove-post-lock'" );
	if ( ! isset( $wp_remove_post_lock ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'wp-remove-post-lock',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$generate_password = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'generate-password'" );
	if ( ! isset( $generate_password ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'generate-password',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$ai1wm_export = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'ai1wm_export'" );
	if ( ! isset( $ai1wm_export ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'ai1wm_export',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_ajax_test = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_ajax_test'" );
	if ( ! isset( $mmp_ajax_test ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_ajax_test',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$get_remaining_attempts_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'get_remaining_attempts_message'" );
	if ( ! isset( $get_remaining_attempts_message ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'get_remaining_attempts_message',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$h5vp_ajax_handler = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'h5vp_ajax_handler'" );
	if ( ! isset( $h5vp_ajax_handler ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'h5vp_ajax_handler',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$tve_dash_front_ajax = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'tve_dash_front_ajax'" );
	if ( ! isset( $tve_dash_front_ajax ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'tve_dash_front_ajax',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$dokan_pageview = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'dokan_pageview'" );
	if ( ! isset( $dokan_pageview ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'dokan_pageview',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$srs_update_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'srs_update_counter'" );
	if ( ! isset( $srs_update_counter ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'srs_update_counter',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$pys_api_event = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'pys_api_event'" );
	if ( ! isset( $pys_api_event ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'pys_api_event',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpil_link_clicked = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wpil_link_clicked'" );
	if ( ! isset( $wpil_link_clicked ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'wpil_link_clicked',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$feed_locator = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'feed_locator'" );
	if ( ! isset( $feed_locator ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'feed_locator',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$revslider_ajax_call_front = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'revslider_ajax_call_front'" );
	if ( ! isset( $revslider_ajax_call_front ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'revslider_ajax_call_front',
			)
		);
	}

	// Update.
	update_option( 'spam_master_upgrade_to_7_3_2', '1' );
	update_option( 'spam_master_db_version', '732' );
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
$spamvalue                  = 'Plugin Install or Upgrade Tasks Done 7_3_2.';
$cache                      = '4H';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

