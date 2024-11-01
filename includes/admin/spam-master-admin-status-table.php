<?php
/**
 * Settings main table.
 *
 * @package Spam Master
 */

if ( isset( $_SERVER['DOCUMENT_ROOT'] ) ) {
	require_once esc_url_raw( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . '/wp-load.php';
}
global $wpdb, $blog_id;
$plugin_master_name    = constant( 'SPAM_MASTER_NAME' );
$plugin_master_domain  = constant( 'SPAM_MASTER_DOMAIN' );
$plugin_master_version = constant( 'SPAM_MASTER_VERSION' );

$platform                          = 'WordPress';
$spam_master_alert_level_date_set  = gmdate( 'Y-m-d H:i:s' );
$spam_master_alert_level_date_auto = gmdate( 'Y-m-d' );
$wordpress                         = substr( get_bloginfo( 'version' ), 0, 12 );
$address                           = substr( get_site_url(), 0, 360 );
$spam_master_version               = constant( 'SPAM_MASTER_VERSION' );
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

// Add Table & Load Spam Master Options.
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_license_key_old = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key_old'" ), 0, 64 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_expires = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_expires'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_auto_update = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" ), 0, 5 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_is_cloudflare = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_is_cloudflare'" ), 0, 5 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_db_protection_hash = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );

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
// Spam Collect Controller.
$spam_master_collect_controller = new SpamMasterCollectController();
$collect_now                    = true;
$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

// Spam User Controller.
$spam_master_user_controller = new SpamMasterUserController();
$spaminitial                 = 'status-table';
if ( ! empty( $email ) ) {
	if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
		$spampreemail = wp_strip_all_tags( substr( $email, 0, 256 ) );
	} else {
		$spampreemail = false;
	}
} else {
	$spampreemail = false;
}
$is_user = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

if ( is_multisite() ) {
	$admin_email = substr( get_blog_option( $blog_id, 'admin_email' ), 0, 128 );
	$blog        = substr( get_blog_option( $blog_id, 'blogname' ), 0, 256 );
	if ( empty( $blog ) ) {
		$blog = 'Wp multi';
	}
	$spam_master_multisite        = 'YES';
	$spam_master_multisite_number = get_blog_count();
	$spam_master_multisite_joined = substr( $spam_master_multisite . ' - ' . $spam_master_multisite_number, 0, 11 );
} else {
	$admin_email = substr( get_option( 'admin_email' ), 0, 128 );
	$blog        = substr( get_option( 'blogname' ), 0, 256 );
	if ( empty( $blog ) ) {
		$blog = 'Wp single';
	}
	$spam_master_multisite        = 'NO';
	$spam_master_multisite_number = '0';
	$spam_master_multisite_joined = substr( $spam_master_multisite . ' - ' . $spam_master_multisite_number, 0, 11 );
}
if ( empty( $admin_email ) ) {
	$admin_email = 'weird-no-email@' . gmdate( 'YmdHis' ) . '.wp';
}

// RE-SYNC if not valid.
if ( 'VALID' !== $spam_master_status ) {
	// Creates button.
	$spam_master_resync = '<button type="submit" name="resync_license" id="resync_license" class="btn-spammaster red roundedspam" href="#" title="' . __( 'RE-SYNCHRONIZE CONNECTION', 'spam-master' ) . '">' . __( 'Re-Synchronize ', 'spam-master' ) . $spam_master_status . __( ' Connection!', 'spam-master' ) . '</button>';

	// Post button.
	if ( isset( $_POST['resync_license'] ) ) {
		check_admin_referer( 'save-settings_update_license' );
		$spam_master_cron = 'RESYN';
		$data_address     = array( 'spamvalue' => $address );
		$where_address    = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_address',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
		$data_ip  = array( 'spamvalue' => $spam_master_server_ip );
		$where_ip = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_ip',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_ip, $where_ip );

		if ( ! empty( $_POST['spam_master_new_license'] ) ) {
			$spam_master_new_license = sanitize_text_field( wp_unslash( $_POST['spam_master_new_license'] ) );
			$spam_license_key        = $spam_master_new_license;
			$data_spam2              = array( 'spamvalue' => substr( $spam_master_new_license, 0, 64 ) );
			$where_spam2             = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_license_key',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
		}

		if ( ! empty( $spam_license_key ) ) {
			// remote post and response.
			$spam_master_license_sync = array(
				'spam_license_key'    => $spam_license_key,
				'platform'            => $platform,
				'platform_version'    => $wordpress,
				'platform_type'       => $spam_master_multisite_joined,
				'spam_master_version' => $spam_master_version,
				'spam_master_type'    => $spam_master_type,
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
			$spam_master_license_url  = 'https://www.spammaster.org/core/lic/get_sync.php';
			$response                 = wp_remote_post(
				$spam_master_license_url,
				array(
					'method'  => 'POST',
					'timeout' => 90,
					'body'    => $spam_master_license_sync,
				)
			);
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam-master' ) . $error_message );
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( empty( $data ) ) {
					$ohmy = true;
				} else {
					$data_spam1  = array( 'spamvalue' => $data['type'] );
					$where_spam1 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_type',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
					$data_spam2  = array( 'spamvalue' => $data['status'] );
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

					// Log InUp Controller.
					$spamtype                   = 'Key Re-Sync';
					$spamvalue                  = 'Successfully run with status: ' . $data['status'];
					$cache                      = '4H';
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log                     = $spam_master_log_controller->spammasterlog( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype, $spamvalue, $cache );

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
			?>
			<div id="message" class="updated fade">
			<p><strong><?php echo esc_attr( __( 'Key RE-SYNC Done. Please wait refreshing in 5 seconds.', 'spam-master' ) ); ?></strong></p>
			</div>
			<?php
		} else {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Your Key is empty, insert a valid key, press Save & Refresh. Please wait refreshing in 5 seconds.', 'spam-master' ) ); ?></strong></p>
			</div>
			<?php
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="5">';
		// END POST.
	}
} else {
	$spam_master_resync = false;
}

// Key Update post in WordPress.
if ( isset( $_POST['update_license'] ) ) {

	check_admin_referer( 'save-settings_update_license' );
	$spam_master_cron = 'MAN';
	if ( ! empty( $_POST['spam_master_new_license'] ) ) {
		$spam_master_new_license = sanitize_text_field( wp_unslash( $_POST['spam_master_new_license'] ) );
		$data_address            = array( 'spamvalue' => $address );
		$where_address           = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_address',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
		$data_ip  = array( 'spamvalue' => $spam_master_server_ip );
		$where_ip = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_ip',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_ip, $where_ip );
		// ONLY IF KEY IS DIFFERENT.
		if ( $spam_license_key_old !== $spam_master_new_license ) {
			$data_spam1  = array( 'spamvalue' => substr( $spam_master_new_license, 0, 64 ) );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_license_key_old',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => substr( $spam_master_new_license, 0, 64 ) );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_license_key',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
			// remote post and response.
			$spam_master_license_post = array(
				'spam_license_key'    => $spam_master_new_license,
				'platform'            => $platform,
				'platform_version'    => $wordpress,
				'platform_type'       => $spam_master_multisite_joined,
				'spam_master_version' => $spam_master_version,
				'spam_master_type'    => $spam_master_type,
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
			$spam_master_license_url  = 'https://www.spammaster.org/core/lic/get_lic.php';
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
				echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam-master' ) . $error_message );
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( empty( $data ) ) {
					$ohmy = true;
				} else {
					$data_spam1  = array( 'spamvalue' => $data['type'] );
					$where_spam1 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_type',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
					$data_spam2  = array( 'spamvalue' => $data['status'] );
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

					// Log InUp Controller.
					$spamtype                   = 'Key Change';
					$spamvalue                  = 'Successfully run with status: ' . $data['status'];
					$cache                      = '1H';
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log                     = $spam_master_log_controller->spammasterlog( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype, $spamvalue, $cache );

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
		}
		?>
		<div id="message" class="updated fade">
		<p><strong><?php echo esc_attr( __( 'Key Saved. Please wait refreshing in 5 seconds.', 'spam-master' ) ); ?></strong></p>
		</div>
		<?php
	} else {
		?>
		<div class="notice notice-warning is-dismissible">
		<p><strong><?php echo esc_attr( __( 'Your Key is empty, insert a valid key. Please wait refreshing in 5 seconds.', 'spam-master' ) ); ?></strong></p>
		</div>
		<?php
	}
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<META HTTP-EQUIV="REFRESH" CONTENT="5">';
	// END POST.
}

// STATUS VALID.
if ( 'VALID' === $spam_master_status ) {
	if ( 'FULL' === $spam_master_type ) {
		$spam_master_type_display         = 'PRO KEY > ';
		$spam_master_type_small_display   = 'PRO';
		$spam_master_type_small_span      = 'spam-master-admin-green spam-master-top-admin-shadow-offline';
		$spam_master_server_small_display = 'PRO';
		$spam_license_connection_status   = $spam_master_server_small_display . __( ' SERVERS > CONNECTION > OPTIMAL', 'spam-master' );
	} else {
		$spam_master_type_display         = $spam_master_type . ' KEY > ';
		$spam_master_type_small_display   = $spam_master_type;
		$spam_master_type_small_span      = 'spam-master-admin-yellow spam-master-top-admin-shadow-red';
		$spam_master_server_small_display = $spam_master_type;
		$spam_license_connection_status   = $spam_master_server_small_display . __( ' SERVERS > CONNECTION > OK > May be improved with a PRO key.', 'spam-master' );
	}
	$license_color                    = 'spam-master-top-admin-green';
	$spam_master_protection_selection = $spam_master_type_display . __( 'ACTIVE > ONLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-green';
	$license_status                   = $spam_master_type_small_display . __( ' KEY > VALID', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-yes-alt"></span>';
	$protection_total_number_text     = ' > ' . number_format( $spam_master_protection_total_number ) . ' Threats & Exploits.';
}
// STATUS EXPIRED.
if ( 'EXPIRED' === $spam_master_status ) {
	$spam_master_type_small_display   = 'PRO';
	$spam_master_type_small_span      = 'spam-master-admin-red spam-master-top-admin-shadow-offline';
	$spam_master_protection_selection = __( 'EXPIRED > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-red';
	$license_color                    = 'spam-master-top-admin-red';
	$license_status                   = __( 'EXPIRED KEY', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-dismiss"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - EXPIRED > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > EXPIRED', 'spam-master' );
}
// STATUS MALFUNCTION_1.
if ( 'MALFUNCTION_1' === $spam_master_status ) {
	if ( 'FULL' === $spam_master_type ) {
		$spam_master_type_small_display = 'PRO';
		$spam_master_type_small_span    = 'spam-master-admin-green spam-master-top-admin-shadow-offline';
	} else {
		$spam_master_type_small_display = $spam_master_type;
		$spam_master_type_small_span    = 'spam-master-admin-yellow spam-master-top-admin-shadow-red';
	}
	$spam_master_protection_selection = __( 'MALFUNCTION 1 > ACTIVE > ONLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-orange';
	$license_color                    = 'spam-master-top-admin-orange';
	$license_status                   = __( 'VALID KEY', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION_1', 'spam-master' );
	$protection_total_number_text     = ' > ' . number_format( $spam_master_protection_total_number ) . ' Threats & Exploits.';
}
// STATUS MALFUNCTION_2.
if ( 'MALFUNCTION_2' === $spam_master_status ) {
	if ( 'FULL' === $spam_master_type ) {
		$spam_master_type_small_display = 'PRO';
		$spam_master_type_small_span    = 'spam-master-admin-green spam-master-top-admin-shadow-offline';
	} else {
		$spam_master_type_small_display = $spam_master_type;
		$spam_master_type_small_span    = 'spam-master-admin-yellow spam-master-top-admin-shadow-red';
	}
	$spam_master_protection_selection = __( 'MALFUNCTION 2 > ACTIVE > ONLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-orangina';
	$license_color                    = 'spam-master-top-admin-orangina';
	$license_status                   = __( 'VALID KEY', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION_2', 'spam-master' );
	$protection_total_number_text     = ' > ' . number_format( $spam_master_protection_total_number ) . ' Threats & Exploits.';
}
// STATUS MALFUNCTION_3.
if ( 'MALFUNCTION_3' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 3 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-red';
	$license_color                    = 'spam-master-top-admin-red';
	$license_status                   = __( 'MALFUNCTION 3 > OFFLINE', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-dismiss"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 3 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 3', 'spam-master' );

}
// STATUS MALFUNCTION_4.
if ( 'MALFUNCTION_4' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 4 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'MALFUNCTION 4 > KEY NOT AUTO GENERATED EMAIL IN USE', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 4 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 4', 'spam-master' );

}
// STATUS MALFUNCTION_5.
if ( 'MALFUNCTION_5' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 5 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'MALFUNCTION 5 > KEY NOT GENERATED', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 5 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 5', 'spam-master' );

}
// STATUS MALFUNCTION_6.
if ( 'MALFUNCTION_6' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 6 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'MALFUNCTION 6 > 1 KEY PER WEBSITE', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 6 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 6', 'spam-master' );

}
// STATUS MALFUNCTION_7.
if ( 'MALFUNCTION_7' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 7 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'MALFUNCTION 7 > KEY NOT AUTO GENERATED UPDATE SPAM MASTER', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 7 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 7', 'spam-master' );

}
// STATUS MALFUNCTION_8.
if ( 'MALFUNCTION_8' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'MALFUNCTION 8 > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'MALFUNCTION 8 > CLOUDFLARE WAF DETECTED ACTIVATE TRUE-CLIENT-IP', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - MALFUNCTION 8 > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > MALFUNCTION 8', 'spam-master' );

}
// STATUS DISCONNECTED.
if ( 'DISCONNECTED' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'DISCONNECTED > INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'DISCONNECTED > COULD NOT CONNECT', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - DISCONNECTED > OFFLINE.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > DISCONNECTED', 'spam-master' );

}
// STATUS UNSTABLE.
if ( 'UNSTABLE' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'UNSTABLE > WARNING', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'UNSTABLE > WARNING', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - Upgrade to PRO.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > UNSTABLE', 'spam-master' );

}
// STATUS HIGH VOLUME.
if ( 'HIGH_VOLUME' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'HIGH VOLUME > WARNING', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'HIGH VOLUME > WARNING', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	$protection_total_number_text     = __( '0 Threats & Exploits - Upgrade to PRO.', 'spam-master' );
	$spam_license_connection_status   = __( 'SERVER CONNECTION > HIGH VOLUME', 'spam-master' );

}
// STATUS INACTIVE, NO KEY SENT YET.
if ( 'INACTIVE' === $spam_master_status ) {
	$spam_master_type_small_display   = false;
	$spam_master_type_small_span      = false;
	$spam_master_protection_selection = __( 'INACTIVE > OFFLINE', 'spam-master' );
	$spam_master_protection_bgcolor   = 'spam-master-top-admin-yellow';
	$license_color                    = 'spam-master-top-admin-yellow';
	$license_status                   = __( 'INACTIVE KEY', 'spam-master' );
	$spam_license_status_icon         = '<span class="dashicons dashicons-warning"></span>';
	if ( 'TRIAL' === $spam_master_type || 'FREE' === $spam_master_type || 'EMPTY' === $spam_master_type ) {
		$spam_license_connection_status = __( 'SERVER CONNECTION > INACTIVE > OFFLINE', 'spam-master' );
	}
	if ( 'FULL' === $spam_master_type ) {
		$spam_license_connection_status = false;
	}
	$protection_total_number_text = false;
}

if ( empty( $spam_master_expires ) ) {
	$spam_master_expires = '0000-00-00 00:00:00';
}
if ( empty( $spam_master_attached ) ) {
	$spam_master_attached = 'NO ATTACHED EMAIL';
}

?>
<form method="post" width='1'>
<fieldset class="options">
<?php $sec_nonce = wp_nonce_field( 'save-settings_update_license' ); ?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><h2><?php echo esc_attr( $plugin_master_name ); ?> <span class="<?php echo esc_attr( $spam_master_type_small_span ); ?>"><?php echo esc_attr( $spam_master_type_small_display ); ?></span>  <?php echo esc_attr( __( 'Version:&nbsp;', 'spam-master' ) . $plugin_master_version ); ?></h2></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th>
				<button type="submit" name="update_license" id="update_license" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save & Refresh', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save & Refresh', 'spam-master' ) ); ?></button>
			</th>
			<th>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_master_resync;
				?>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Connection Key:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle-p spam-master-flex"><input class="spam-master-100" id="spam_master_new_license" name="spam_master_new_license" type="text" value="<?php echo esc_attr( $spam_license_key ); ?>">
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Key Attached Email:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle <?php echo esc_attr( $spam_master_protection_bgcolor ); ?>"><font color="white"><b>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_status_icon;
				?>
				&nbsp;&nbsp;
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_master_attached;
				?>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Key Status:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle <?php echo esc_attr( $license_color ); ?>"><font color="white"><b>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_status_icon;
				?>
				&nbsp;&nbsp;
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $license_status;
				?>
				</b></font></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Protection Status:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle <?php echo esc_attr( $spam_master_protection_bgcolor ); ?>"><font color="white"><b>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_status_icon;
				?>
				&nbsp;&nbsp;
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_master_protection_selection;
				?>
				</b></td>
		</tr>
		<?php
		if ( 'FULL' === $spam_master_type ) {
			?>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Protection Renews:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle <?php echo esc_attr( $spam_master_protection_bgcolor ); ?>"><font color="white"><b>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_status_icon;
				?>
				&nbsp;&nbsp;
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_master_expires;
				?>
				</b></td>
		</tr>
			<?php
		}
		if ( 'FREE' === $spam_master_type ) {
			?>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Server Connection Status:', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle <?php echo esc_attr( $spam_master_protection_bgcolor ); ?>"><font color="white"><b>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_status_icon;
				?>
				&nbsp;&nbsp;
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $spam_license_connection_status;
				?>
				</b></td>
		</tr>
			<?php
		}
		if ( 'FREE' === $spam_master_type ) {
			?>
		<tr class="alternate">
			<td colspan="2" class="spam-master-middle">
				<?php
				$spam_master_invitation_notice_plus_15 = gmdate( 'Y-m-d', strtotime( '+15 days', strtotime( $spam_master_expires ) ) );
				if ( $spam_master_alert_level_date_auto >= $spam_master_invitation_notice_plus_15 ) {
					$yesitsempty = true;
				}
				?>
				<a class="btn-spammaster green roundedspam" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_attr( __( 'Premium Server Connection for peanuts', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Need a Pro Key?', 'spam-master' ) ); ?></a> 
				<a class="btn-spammaster green roundedspam" href="https://www.spammaster.org/rbl-servers-status/" target="_blank" title="<?php echo esc_attr( __( 'Spam Master Servers Status', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Spam Master Service > Servers Status', 'spam-master' ) ); ?></a>
			</td>
		</tr>
			<?php
		}
		if ( 'FULL' === $spam_master_type && 'EXPIRED' === $spam_master_status ) {
			?>
		<tr class="alternate">
			<td colspan="2" class="spam-master-middle">
				<a class="btn-spammaster green roundedspam" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_attr( __( 'Premium Server Connection for peanuts', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Renew Pro Key', 'spam-master' ) ); ?></a> 
			</td>
		</tr>
			<?php
		}
		?>
	</tbody>
</table>
</fieldset>
</form>
