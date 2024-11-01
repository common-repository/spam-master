<?php
/**
 * Key controller
 *
 * @package Spam Master
 */

/**
 * Main key class.
 *
 * @since 6.0.0
 */
class SpamMasterKeyController {

	/**
	 * Variable spam_master_key.
	 *
	 * @var spam_master_key $spam_master_key
	 **/
	protected $spam_master_key;

	/**
	 * Variable spam_master_do.
	 *
	 * @var spam_master_do $spam_master_do
	 **/
	protected $spam_master_do;

	/**
	 * Spam master key deact.
	 *
	 * @param spam_master_key $spam_master_key for key.
	 * @param spam_master_do  $spam_master_do for key.
	 *
	 * @return void
	 */
	public function spammasterkeydeact( $spam_master_key, $spam_master_do ) {
		global $wpdb, $blog_id;

		if ( 'deact' === $spam_master_do ) {

			// Add Table and load spam master options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_db = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
				// Remote post.
				$spam_master_key_post = array(
					'spam_license_key'   => $spam_license_key,
					'spam_master_db'     => $spam_master_db,
					'spam_master_type'   => $spam_master_type,
					'spam_master_status' => $spam_master_status,
					'spam_master_do'     => 'DEACTIVATION',
				);
				$spam_master_key_url  = 'https://www.spammaster.org/core/lic/get_other.php';
				$response             = wp_remote_post(
					$spam_master_key_url,
					array(
						'method'  => 'POST',
						'timeout' => 90,
						'body'    => $spam_master_key_post,
					)
				);
			}
		}
	}

	/**
	 * Spam master key lazy.
	 *
	 * @param spam_master_key $spam_master_key for key.
	 * @param spam_master_do  $spam_master_do for key.
	 *
	 * @return LAZY
	 */
	public function spammasterkeylazy( $spam_master_key, $spam_master_do ) {
		global $wpdb, $blog_id;

		if ( 'LAZY' === $spam_master_do ) {

			// Add Table and load spam master options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
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

			// Update run notice.
			$data_spam  = array( 'spamvalue' => '0' );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_license_sync_run',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// Prepare Key stuff.
			$platform                         = 'WordPress';
			$spam_master_cron                 = $spam_master_do;
			$spam_master_alert_level_date_set = current_datetime()->format( 'Y-m-d H:i:s' );
			$wordpress                        = substr( get_bloginfo( 'version' ), 0, 12 );
			$address                          = substr( get_site_url(), 0, 360 );
			$spam_master_version              = constant( 'SPAM_MASTER_VERSION' );

			if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
				$spam_master_server_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ), 0, 48 );
				// if empty ip.
				if ( empty( $spam_master_server_ip ) || '0' === $spam_master_server_ip || '127.0.0.1' === $spam_master_server_ip ) {
					if ( isset( $_SERVER['SERVER_NAME'] ) ) {
						$spam_master_ip_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
						$spam_master_server_ip        = substr( $spam_master_ip_gethostbyname, 0, 48 );
						if ( empty( $spam_master_ip_gethostbyname ) || '0' === $spam_master_ip_gethostbyname ) {
							$spam_master_urlparts  = wp_parse_url( $web_address );
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
							$spam_master_urlparts        = wp_parse_url( $web_address );
							$spam_master_hostname        = $spam_master_urlparts['host'];
							$spam_master_result          = dns_get_record( $spam_master_hostname, DNS_A );
							$spam_master_server_hostname = substr( $spam_master_result[0]['ip'], 0, 256 );
						}
					} else {
						$spam_master_server_hostname = 'h 000';
					}
				}
			} else {
				$spam_master_server_ip       = 'I 001';
				$spam_master_server_hostname = 'h 001';
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
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
			$data_address      = array( 'spamvalue' => $address );
			$where_address     = array(
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
			if ( isset( $_SERVER['SERVER_NAME'] ) ) {
				$result = dns_get_record( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
				if ( ! empty( $result[0]['ip'] ) ) {
					$anotherip = $result[0]['ip'];
					$data_ip2  = array( 'spamvalue' => $anotherip );
					$where_ip2 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_ip2',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_ip2, $where_ip2 );
				} else {
					$data_ip2  = array( 'spamvalue' => 'localhost' );
					$where_ip2 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_ip2',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_ip2, $where_ip2 );
				}
			} else {
				$data_ip2  = array( 'spamvalue' => 'localhost' );
				$where_ip2 = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_ip2',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_ip2, $where_ip2 );
			}

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

			// Set malfunctions as VALID.
			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status || 'UNSTABLE' === $spam_master_status || 'HIGH_VOLUME' === $spam_master_status ) {
				// remote post and response.
				$spam_master_license_post = array(
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
						$emptydata = true;
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
						$remote_ip                  = $spam_master_server_ip;
						$blog_threat_email          = 'localhost';
						$remote_referer             = 'localhost';
						$dest_url                   = 'localhost';
						$remote_agent               = 'localhost';
						$spamuser                   = array( 'ID' => 'none' );
						$spamuser_a                 = wp_json_encode( $spamuser );
						$spamtype                   = 'Key Health';
						$spamvalue                  = 'Successfully run with status: ' . $data['status'];
						$cache                      = '4H';
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

						// Bypass log post to rbl based on cache and log.
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
						$wpdb->insert(
							$spam_master_keys,
							array(
								'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
								'spamkey'   => 'System',
								'spamtype'  => 'Cron: lazy Key sender run.',
								'spamy'     => $remote_ip,
								'spamvalue' => '7D',
							)
						);

						$spama = $data['a'];
						if ( '1' === $spama ) {
							// Spam Action Controller.
							$spam_master_action_controller = new SpamMasterActionController();
							$is_action                     = $spam_master_action_controller->spammasteract( $spama );
						}
					}
				}
				// End valid.
			}

			// Run tasks.
			require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-tasks.php';

			return 'LAZY';
		}
	}

}

