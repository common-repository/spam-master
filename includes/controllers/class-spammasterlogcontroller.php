<?php
/**
 * Log controller
 *
 * @package Spam Master
 */

/**
 * Main log class.
 *
 * @since 6.0.0
 */
class SpamMasterLogController {

	/**
	 * Variable remote_ip.
	 *
	 * @var remote_ip $remote_ip
	 **/
	protected $remote_ip;

	/**
	 * Variable blog_threat_email.
	 *
	 * @var blog_threat_email $blog_threat_email
	 **/
	protected $blog_threat_email;

	/**
	 * Variable remote_referer.
	 *
	 * @var remote_referer $remote_referer
	 **/
	protected $remote_referer;

	/**
	 * Variable dest_url.
	 *
	 * @var dest_url $dest_url
	 **/
	protected $dest_url;

	/**
	 * Variable remote_agent.
	 *
	 * @var remote_agent $remote_agent
	 **/
	protected $remote_agent;

	/**
	 * Variable spamuser_a.
	 *
	 * @var spamuser_a $spamuser_a
	 **/
	protected $spamuser_a;

	/**
	 * Variable spamtype.
	 *
	 * @var spamtype $spamtype
	 **/
	protected $spamtype;

	/**
	 * Variable spamvalue.
	 *
	 * @var spamvalue $spamvalue
	 **/
	protected $spamvalue;

	/**
	 * Variable cache.
	 *
	 * @var cache $cache
	 **/
	protected $cache;

	/**
	 * Spam master log.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 * @param spamvalue         $spamvalue for scan.
	 * @param cache             $cache for scan.
	 *
	 * @return void
	 */
	public function spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache ) {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			// Time Frames.
			$current_time = current_datetime()->format( 'Y-m-d H:i:s' );
			if ( '1H' === $cache ) {
				$setcache = '1';
			}
			if ( '4H' === $cache ) {
				$setcache = '4';
			}
			if ( '1D' === $cache ) {
				$setcache = '24';
			}
			if ( '7D' === $cache ) {
				$setcache = '168';
			}
			if ( '3M' === $cache ) {
				$setcache = '792';
			}
			if ( '12M' === $cache ) {
				$setcache = '8784';
			}

			// Combine values.
			$spam_combo = $spamtype . ': ' . $spamvalue;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$is_log = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'System' AND spamtype = %s AND spamy = %s AND time >= NOW() - INTERVAL %s HOUR", $spam_combo, $remote_ip, $setcache ) );
			if ( empty( $is_log ) ) {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_address = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_address'" ), 0, 256 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$spam_master_exempt_system = $wpdb->get_var(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s AND POSITION(spamvalue IN %s) > %s",
						'Option',
						'exempt-value',
						$remote_agent,
						'0',
					)
				);
				if ( empty( $spam_master_exempt_system ) ) {
					$spam_master_log_url       = 'https://www.spammaster.org/core/learn/get_learn_sys.php';
					$spam_master_learning_post = array(
						'blog_license_key'    => $spam_license_key,
						'blog_threat_ip'      => $remote_ip,
						'blog_threat_user'    => $spamuser_a,
						'blog_threat_type'    => 'System',
						'blog_threat_email'   => $blog_threat_email,
						'blog_threat_content' => $spam_combo,
						'blog_threat_agent'   => $remote_agent,
						'blog_threat_refe'    => $remote_referer,
						'blog_threat_dest'    => $dest_url,
						'blog_web_adress'     => $spam_master_address,
						'blog_server_ip'      => $spam_master_ip,
					);
					$response                  = wp_remote_post(
						$spam_master_log_url,
						array(
							'method'  => 'POST',
							'timeout' => 90,
							'body'    => $spam_master_learning_post,
						)
					);
					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam_master' ) . $error_message );
					}

					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wpdb->insert(
						$spam_master_keys,
						array(
							'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
							'spamkey'   => 'System',
							'spamtype'  => $spam_combo,
							'spamy'     => $remote_ip,
							'spamvalue' => $cache,
						)
					);
				}
			}
		}
	}

	/**
	 * Spam master log flood.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 * @param spamvalue         $spamvalue for scan.
	 * @param cache             $cache for scan.
	 *
	 * @return void
	 */
	public function spammasterlogflood( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache ) {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			// Combine values.
			$spam_combo = $spamtype . ': ' . $spamvalue;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert(
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'System',
					'spamtype'  => $spam_combo,
					'spamy'     => $remote_ip,
					'spamvalue' => $cache,
				)
			);

		}
	}

	/**
	 * Spam master log flood warning.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 * @param spamvalue         $spamvalue for scan.
	 * @param cache             $cache for scan.
	 *
	 * @return void
	 */
	public function spammasterlogfloodwarn( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache ) {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			// Time Frames.
			$current_time = current_datetime()->format( 'Y-m-d H:i:s' );
			if ( '1H' === $cache ) {
				$setcache = '1';
			}
			if ( '4H' === $cache ) {
				$setcache = '4';
			}
			if ( '1D' === $cache ) {
				$setcache = '24';
			}
			if ( '7D' === $cache ) {
				$setcache = '168';
			}
			if ( '3M' === $cache ) {
				$setcache = '792';
			}
			if ( '12M' === $cache ) {
				$setcache = '8784';
			}

			// Combine values.
			$spam_combo = $spamtype . ': ' . $spamvalue;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$is_log = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'System' AND spamtype = %s AND spamy = %s AND time >= NOW() - INTERVAL %s HOUR", $spam_combo, $remote_ip, $setcache ) );
			if ( empty( $is_log ) ) {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_address = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_address'" ), 0, 256 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

				$spam_master_log_url       = 'https://www.spammaster.org/core/learn/get_learn_flood.php';
				$spam_master_learning_post = array(
					'blog_license_key'    => $spam_license_key,
					'blog_threat_ip'      => $remote_ip,
					'blog_threat_user'    => $spamuser_a,
					'blog_threat_type'    => 'FLOOD',
					'blog_threat_email'   => $blog_threat_email,
					'blog_threat_content' => $spamvalue,
					'blog_threat_agent'   => $remote_agent,
					'blog_threat_refe'    => $remote_referer,
					'blog_threat_dest'    => $dest_url,
					'blog_web_adress'     => $spam_master_address,
					'blog_server_ip'      => $spam_master_ip,
				);
				$response                  = wp_remote_post(
					$spam_master_log_url,
					array(
						'method'  => 'POST',
						'timeout' => 90,
						'body'    => $spam_master_learning_post,
					)
				);
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam_master' ) . $error_message );
				}
			}
		}
	}

}

