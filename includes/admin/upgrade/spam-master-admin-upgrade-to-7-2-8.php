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

		// Keys.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$time = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'time'" );
		if ( ! isset( $time ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-key',
					'spamy'     => 'localhost',
					'spamvalue' => 'time',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$security = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
		if ( ! isset( $security ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-key',
					'spamy'     => 'localhost',
					'spamvalue' => 'security',
				)
			);
		}
		// Actions.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sgpb_send_to_open_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'sgpb_send_to_open_counter'" );
		if ( ! isset( $sgpb_send_to_open_counter ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'sgpb_send_to_open_counter',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$vc_get_vc_grid_data = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'vc_get_vc_grid_data'" );
		if ( ! isset( $vc_get_vc_grid_data ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'vc_get_vc_grid_data',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$heartbeat = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'heartbeat'" );
		if ( ! isset( $heartbeat ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'heartbeat',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$itsec = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'itsec-check-loopback'" );
		if ( ! isset( $itsec ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'itsec-check-loopback',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$bloom_handle_stats_adding = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'bloom_handle_stats_adding'" );
		if ( ! isset( $bloom_handle_stats_adding ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'bloom_handle_stats_adding',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpa_stats_action = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wpa_stats_action'" );
		if ( ! isset( $wpa_stats_action ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'wpa_stats_action',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mailpoetcron = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mailpoet-cron-action-scheduler-run'" );
		if ( ! isset( $mailpoetcron ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mailpoet-cron-action-scheduler-run',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_marker_popups = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_marker_popups'" );
		if ( ! isset( $mmp_marker_popups ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_marker_popups',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_map_marker_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_marker_list'" );
		if ( ! isset( $mmp_map_marker_list ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_map_marker_list',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_map_geojson = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_geojson'" );
		if ( ! isset( $mmp_map_geojson ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_map_geojson',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_map_settings = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_settings'" );
		if ( ! isset( $mmp_map_settings ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_map_settings',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$mmp_map_markers = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_markers'" );
		if ( ! isset( $mmp_map_markers ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'mmp_map_markers',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$forminator_get_nonce = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'forminator_get_nonce'" );
		if ( ! isset( $forminator_get_nonce ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'forminator_get_nonce',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sgpb_send_to_open_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'sgpb_send_to_open_counter'" );
		if ( ! isset( $sgpb_send_to_open_counter ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'sgpb_send_to_open_counter',
				)
			);
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$borlabs_cookie_handler = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'borlabs_cookie_handler'" );
		if ( ! isset( $borlabs_cookie_handler ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'exempt-action',
					'spamy'     => 'localhost',
					'spamvalue' => 'borlabs_cookie_handler',
				)
			);
		}

		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_7_2_8', '1' );
	}
} else {
	// Update DB.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';

	// Keys.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$time = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'time'" );
	if ( ! isset( $time ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-key',
				'spamy'     => 'localhost',
				'spamvalue' => 'time',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$security = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-key' AND spamvalue = 'security'" );
	if ( ! isset( $security ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-key',
				'spamy'     => 'localhost',
				'spamvalue' => 'security',
			)
		);
	}
	// Actions.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$sgpb_send_to_open_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'sgpb_send_to_open_counter'" );
	if ( ! isset( $sgpb_send_to_open_counter ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'sgpb_send_to_open_counter',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$vc_get_vc_grid_data = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'vc_get_vc_grid_data'" );
	if ( ! isset( $vc_get_vc_grid_data ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'vc_get_vc_grid_data',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$heartbeat = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'heartbeat'" );
	if ( ! isset( $heartbeat ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'heartbeat',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$itsec = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'itsec-check-loopback'" );
	if ( ! isset( $itsec ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'itsec-check-loopback',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$bloom_handle_stats_adding = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'bloom_handle_stats_adding'" );
	if ( ! isset( $bloom_handle_stats_adding ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'bloom_handle_stats_adding',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpa_stats_action = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'wpa_stats_action'" );
	if ( ! isset( $wpa_stats_action ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'wpa_stats_action',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mailpoetcron = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mailpoet-cron-action-scheduler-run'" );
	if ( ! isset( $mailpoetcron ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mailpoet-cron-action-scheduler-run',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_marker_popups = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_marker_popups'" );
	if ( ! isset( $mmp_marker_popups ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_marker_popups',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_map_marker_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_marker_list'" );
	if ( ! isset( $mmp_map_marker_list ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_map_marker_list',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_map_geojson = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_geojson'" );
	if ( ! isset( $mmp_map_geojson ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_map_geojson',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_map_settings = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_settings'" );
	if ( ! isset( $mmp_map_settings ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_map_settings',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$mmp_map_markers = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'mmp_map_markers'" );
	if ( ! isset( $mmp_map_markers ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'mmp_map_markers',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$forminator_get_nonce = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'forminator_get_nonce'" );
	if ( ! isset( $forminator_get_nonce ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'forminator_get_nonce',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$sgpb_send_to_open_counter = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'sgpb_send_to_open_counter'" );
	if ( ! isset( $sgpb_send_to_open_counter ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'sgpb_send_to_open_counter',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$borlabs_cookie_handler = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'exempt-action' AND spamvalue = 'borlabs_cookie_handler'" );
	if ( ! isset( $borlabs_cookie_handler ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'exempt-action',
				'spamy'     => 'localhost',
				'spamvalue' => 'borlabs_cookie_handler',
			)
		);
	}

	// Update.
	update_option( 'spam_master_upgrade_to_7_2_8', '1' );
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
$spamvalue                  = 'Plugin Install or Upgrade Tasks Done 7_2_8.';
$cache                      = '4H';
$spam_master_log_controller = new SpamMasterLogController();
$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

