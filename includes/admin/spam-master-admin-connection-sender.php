<?php
/**
 * Connection sender.
 *
 * Warning, fiddling here can cause trouble.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
if ( is_multisite() ) {
	$doesnone = true;
} else {
	// Add Table & Load Spam Master Options.
	$spam_master_connection = get_option( 'spam_master_connection' );
	$spam_master_keys       = $wpdb->prefix . 'spam_master_keys';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_auto_update = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" ), 0, 5 );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_is_cloudflare = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_is_cloudflare'" ), 0, 5 );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_db_protection_hash = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );

	if ( empty( $spam_master_connection ) && empty( $spam_license_key ) && 'INACTIVE' === $spam_master_status && 'EMPTY' === $spam_master_type ) {
		// Remote Ip.
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ), 0, 48 );
		} else {
			$remote_ip = 'Ip 000';
		}
		// Remote Agent.
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$remote_agent = substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 360 );
		} else {
			$remote_agent = 'Sniffer';
		}
		// Remote Referer.
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$remote_referer = substr( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 0, 360 );
		} else {
			$remote_referer = 'Direct';
		}
		// DEST URL.
		if ( isset( $_SERVER['REQUEST_SCHEME'] ) && isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$dest_url = substr( esc_url_raw( wp_unslash( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) ), 0, 360 );
		} else {
			$dest_url = 'Weird';
		}
		// Prepare Connection.
		$platform                         = 'WordPress';
		$spam_master_cron                 = 'AUT';
		$spam_master_lic_nounce           = 'PW9pdXNkbmVXMndzUw==';
		$spam_master_type_set             = 'FREE';
		$spam_master_alert_level_date_set = gmdate( 'Y-m-d H:i:s' );
		$spam_master_version              = constant( 'SPAM_MASTER_VERSION' );
		$wordpress                        = substr( get_bloginfo( 'version' ), 0, 12 );
		$spam_master_multisite            = 'NO';
		$spam_master_multisite_number     = '0';
		$spam_master_multisite_joined     = substr( $spam_master_multisite . ' - ' . $spam_master_multisite_number, 0, 11 );
		$blog                             = substr( get_option( 'blogname' ), 0, 256 );
		if ( empty( $blog ) ) {
			$blog = 'Wp empty';
		}
		$admin_email = substr( get_option( 'admin_email' ), 0, 128 );
		if ( empty( $admin_email ) ) {
			$admin_email = 'weird-no-email@' . gmdate( 'YmdHis' ) . '.wp';
		}
		$address       = substr( get_site_url(), 0, 360 );
		$data_address  = array( 'spamvalue' => $address );
		$where_address = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_address',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );

		if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
			$spam_master_server_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ), 0, 48 );
			// if empty ip.
			if ( empty( $spam_master_server_ip ) || '0' === $spam_master_server_ip || '127.0.0.1' === $spam_master_server_ip ) {
				if ( isset( $_SERVER['SERVER_NAME'] ) ) {
					$spam_master_ip_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
					$spam_master_server_ip        = substr( $spam_master_ip_gethostbyname, 0, 48 );
					if ( empty( $spam_master_ip_gethostbyname ) || '0' === $spam_master_ip_gethostbyname ) {
						$spam_master_urlparts  = wp_parse_url( $address );
						$spam_master_hostname  = $spam_master_urlparts['host'];
						$spam_master_result    = dns_get_record( $spam_master_hostname, DNS_A );
						$spam_master_server_ip = substr( $spam_master_result[0]['ip'], 0, 48 );
					}
				} else {
					$spam_master_server_ip = 'I 000';
				}
			}
			$spam_master_server_hostname = substr( gethostbyaddr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) ), 0, 256 );
			// if empty host.
			if ( empty( $spam_master_server_hostname ) || '0' === $spam_master_server_hostname || '127.0.0.1' === $spam_master_server_hostname ) {
				if ( isset( $_SERVER['SERVER_NAME'] ) ) {
					$spam_master_ho_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
					$spam_master_server_hostname  = substr( $spam_master_ho_gethostbyname, 0, 256 );
					if ( empty( $spam_master_ho_gethostbyname ) || '0' === $spam_master_ho_gethostbyname ) {
						$spam_master_urlparts        = wp_parse_url( $address );
						$spam_master_hostname        = $spam_master_urlparts['host'];
						$spam_master_result          = dns_get_record( $spam_master_hostname, DNS_A );
						$spam_master_server_hostname = substr( $spam_master_result[0]['ip'], 0, 256 );
					}
				} else {
					$spam_master_server_hostname = 'H 000';
				}
			}
		} else {
			if ( isset( $_SERVER['SERVER_NAME'] ) ) {
				$spam_master_ip_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
				$spam_master_server_ip        = substr( $spam_master_ip_gethostbyname, 0, 48 );
				if ( empty( $spam_master_ip_gethostbyname ) || '0' === $spam_master_ip_gethostbyname ) {
					$spam_master_urlparts  = wp_parse_url( $address );
					$spam_master_hostname  = $spam_master_urlparts['host'];
					$spam_master_result    = dns_get_record( $spam_master_hostname, DNS_A );
					$spam_master_server_ip = substr( $spam_master_result[0]['ip'], 0, 48 );
				} else {
					$spam_master_server_ip = 'I 001';
				}
				$spam_master_ho_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
				$spam_master_server_hostname  = substr( $spam_master_ho_gethostbyname, 0, 256 );
				if ( empty( $spam_master_ho_gethostbyname ) || '0' === $spam_master_ho_gethostbyname ) {
					$spam_master_urlparts        = wp_parse_url( $address );
					$spam_master_hostname        = $spam_master_urlparts['host'];
					$spam_master_result          = dns_get_record( $spam_master_hostname, DNS_A );
					$spam_master_server_hostname = substr( $spam_master_result[0]['ip'], 0, 256 );
				} else {
					$spam_master_server_hostname = 'H 001';
				}
			} else {
				$spam_master_server_ip       = 'I 002';
				$spam_master_server_hostname = 'H 002';
			}
		}
		$data_ip  = array( 'spamvalue' => $spam_master_server_ip );
		$where_ip = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_ip',
		);
		// create lic hash.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		$spam_master_lic_hash = substr( md5( uniqid( mt_rand(), true ) ), 0, 64 );
		if ( empty( $spam_master_lic_hash ) ) {
			$spam_master_lic_hash = 'md5-' . gmdate( 'YmdHis' );
		}
		// Update Key.
		$data_spam1  = array( 'spamvalue' => $spam_master_lic_hash );
		$where_spam1 = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_license_key',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
		// Get Counts.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_buffer_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
		if ( empty( $spam_master_buffer_count ) ) {
			$spam_master_buffer_count = '0';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_white_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'White'" );
		if ( empty( $spam_master_white_count ) ) {
			$spam_master_white_count = '0';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_logs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys}" );
		if ( empty( $spam_master_logs_count ) ) {
			$spam_master_logs_count = '0';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_exempt_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype LIKE '%exempt%'" );
		if ( empty( $spam_master_exempt_count ) ) {
			$spam_master_exempt_count = '0';
		}
		$spam_count_pre_ar = array(
			'buf' => $spam_master_buffer_count,
			'whi' => $spam_master_white_count,
			'log' => $spam_master_logs_count,
			'exe' => $spam_master_exempt_count,
			'fir' => $spam_master_firewall_rules,
		);
		$spam_count_ar     = wp_json_encode( $spam_count_pre_ar );
		// Remote post and response.
		$spam_master_license_post = array(
			'spam_license_key'    => $spam_master_lic_hash,
			'spam_trial_nounce'   => $spam_master_lic_nounce,
			'platform'            => $platform,
			'platform_version'    => $wordpress,
			'platform_type'       => $spam_master_multisite_joined,
			'spam_master_version' => $spam_master_version,
			'spam_master_type'    => $spam_master_type_set,
			'blog_name'           => $blog,
			'blog_address'        => $address,
			'blog_email'          => $admin_email,
			'blog_hostname'       => $spam_master_server_hostname,
			'blog_ip'             => $spam_master_server_ip,
			'blog_up'             => $spam_master_auto_update,
			'blog_cloud'          => $spam_master_is_cloudflare,
			'spam_master_db'      => $spam_master_db_protection_hash,
			'spam_master_logs'    => $spam_count_ar,
			'spam_master_cron'    => $spam_master_cron,
		);
		$spam_master_license_url  = 'https://www.spammaster.org/core/lic/lic_gen.php';
		$response                 = wp_remote_post(
			$spam_master_license_url,
			array(
				'method'  => 'POST',
				'timeout' => 90,
				'body'    => $spam_master_license_post,
			)
		);
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			// Update connection.
			update_option( 'spam_master_connection', 'checked-remote-error-curl' );
			echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam-master' ) . $error_message );
		} else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( empty( $data ) ) {
				$spam_master_status = false;
				// Update connection.
				update_option( 'spam_master_connection', 'checked-remote-error-data' );
			} else {
				$spam_master_status = $data['status'];
				if ( 'VALID' === $spam_master_status ) {
					// Update connection.
					update_option( 'spam_master_connection', 'checked-remote-success' );

					$data_spam1  = array( 'spamvalue' => $data['type'] );
					$where_spam1 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_type',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
					$data_spam2  = array( 'spamvalue' => $spam_master_status );
					$where_spam2 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_status',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
					$data_spam3  = array( 'spamvalue' => $data['attached'] );
					$where_spam3 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_attached',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
					$data_spam4  = array( 'spamvalue' => $data['expires'] );
					$where_spam4 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_expires',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam4, $where_spam4 );
					$data_spam5  = array( 'spamvalue' => $data['threats'] );
					$where_spam5 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_protection_total_number',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam5, $where_spam5 );
					$data_spam6  = array( 'spamvalue' => $data['alert'] );
					$where_spam6 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam6, $where_spam6 );
					$data_spam7  = array( 'spamvalue' => $spam_master_alert_level_date_set );
					$where_spam7 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_date',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam7, $where_spam7 );
					$data_spam8  = array( 'spamvalue' => $data['percent'] );
					$where_spam8 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_p_text',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam8, $where_spam8 );

					// Spam Email Controller.
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterautofree( $spammail );
				} else {
					// Update connection.
					update_option( 'spam_master_connection', 'checked-remote-error' );

					$data_spam1  = array( 'spamvalue' => $data['type'] );
					$where_spam1 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_type',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
					$data_spam2  = array( 'spamvalue' => $spam_master_status );
					$where_spam2 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_status',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
					$data_spam3  = array( 'spamvalue' => $data['attached'] );
					$where_spam3 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_attached',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
					$data_spam4  = array( 'spamvalue' => $data['expires'] );
					$where_spam4 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_expires',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam4, $where_spam4 );
					$data_spam5  = array( 'spamvalue' => $data['threats'] );
					$where_spam5 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_protection_total_number',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam5, $where_spam5 );
					$data_spam6  = array( 'spamvalue' => $data['alert'] );
					$where_spam6 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam6, $where_spam6 );
					$data_spam7  = array( 'spamvalue' => $spam_master_alert_level_date_set );
					$where_spam7 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_date',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam7, $where_spam7 );
					$data_spam8  = array( 'spamvalue' => $data['percent'] );
					$where_spam8 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_p_text',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam8, $where_spam8 );

					$data_spam  = array( 'spamvalue' => '' );
					$where_spam = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_license_key',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
				}

				// Log InUp Controller.
				$remote_ip                  = $spam_master_server_ip;
				$blog_threat_email          = 'localhost';
				$remote_referer             = 'localhost';
				$dest_url                   = 'localhost';
				$remote_agent               = 'localhost';
				$spamuser                   = array( 'ID' => 'none' );
				$spamuser_a                 = wp_json_encode( $spamuser );
				$spamtype                   = 'Connection';
				$spamvalue                  = 'Successfully run with status: ' . $spam_master_status;
				$cache                      = '12M';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

				$spama = $data['a'];
				if ( '1' === $spama ) {
					// Spam Action Controller.
					$spam_master_action_controller = new SpamMasterActionController();
					$is_action                     = $spam_master_action_controller->spammasteract( $spama );
				}

				// Get date.
				$spam_master_current_date = current_datetime()->format( 'Y-m-d' );
				// Update run date.
				$data_spam  = array( 'spamvalue' => $spam_master_current_date );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_license_sync_date',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			}
		}
	} else {
		// This is a WP Option.
		update_option( 'spam_master_connection', 'checked-local-error' );
	}
}

