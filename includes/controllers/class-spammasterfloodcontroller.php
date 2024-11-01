<?php
/**
 * Flood controller
 *
 * @package Spam Master
 */

/**
 * Main flood class.
 *
 * @since 6.0.0
 */
class SpamMasterFloodController {

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
	 * @var spamuserA $spamuser_a
	 **/
	protected $spamuser_a;

	/**
	 * Variable spamtype.
	 *
	 * @var spamtype $spamtype
	 **/
	protected $spamtype;

	/**
	 * Spam master alert 3.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 *
	 * @return ALERT_3
	 */
	public function spammasteralert3( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype ) {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_last_honey = $wpdb->get_var( $wpdb->prepare( "SELECT time FROM $spam_master_keys WHERE spamy = %s ORDER BY time DESC LIMIT 1", $remote_ip ) );

			$flood_date_plus_3 = gmdate( 'Y-m-d H:i:s', strtotime( $spam_master_last_honey . '+ 3 minute' ) );
			$flood_date        = current_datetime()->format( 'Y-m-d H:i:s' );

			if ( $flood_date_plus_3 >= $flood_date ) {

				// Spam Log Controller.
				$spamvalue                  = 'Flood Alert 3';
				$cache                      = '1D';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

				// Spam Buffer Controller.
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

				return 'ALERT_3';
			}
		}
	}

	/**
	 * Spam master flood.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 *
	 * @return is_count
	 */
	public function spammasterflood( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype ) {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			// Wp Login.
			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				if ( ! strncmp( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/wp-login.php', strlen( '/wp-login.php' ) ) || ! strncmp( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '//wp-login.php', strlen( '//wp-login.php' ) ) ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$is_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $spam_master_keys WHERE spamkey = 'System' AND spamtype = 'HAF: Flood Login Check.' OR spamtype = 'honeypot 2: Flood Login Check.' AND spamy = %s AND time > DATE_SUB(NOW(), INTERVAL 1 HOUR)", $remote_ip ) );

					// Per hour login page.
					if ( $is_count >= '600' ) {

						// Spam Buffer Controller.
						$spam_master_buffer_controller = new SpamMasterBufferController();
						$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

						$spamc = '12M';
						// Spam Buffer Controller.
						$spam_master_buffer_controller = new SpamMasterBufferController();
						$is_threat                     = $spam_master_buffer_controller->spammasterbufferinsert( $remote_ip, $blog_threat_email, $spamc );

						return $is_count;
					}
					if ( $is_count >= '391' ) {

						$spamvalue = 'Flood Login Count Exceeded, ' . $is_count . ' per Hour.';
						$cache     = '1H';

						// Spam Log Controller.
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log_warn                = $spam_master_log_controller->spammasterlogfloodwarn( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

						// Spam Log Controller.
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log                     = $spam_master_log_controller->spammasterlogflood( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

						// Spam Buffer Controller.
						$spam_master_buffer_controller = new SpamMasterBufferController();
						$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

						$spamc = '12M';
						// Spam Buffer Controller.
						$spam_master_buffer_controller = new SpamMasterBufferController();
						$is_threat                     = $spam_master_buffer_controller->spammasterbufferinsert( $remote_ip, $blog_threat_email, $spamc );

						return $is_count;
					}
				} else {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$is_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $spam_master_keys WHERE spamkey = 'System' AND spamtype = 'Flood Check.' AND spamy = %s AND time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)", $remote_ip ) );

					// Per minute any page.
					if ( $is_count >= '391' ) {

						$spamvalue = 'Flood Count Exceeded, ' . $is_count . ' per minute.';
						$cache     = '1H';

						// Spam Log Controller.
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log_warn                = $spam_master_log_controller->spammasterlogfloodwarn( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

						// Spam Log Controller.
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log                     = $spam_master_log_controller->spammasterlogflood( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

						// Spam Buffer Controller.
						$spam_master_buffer_controller = new SpamMasterBufferController();
						$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

						return $is_count;
					}
				}
			} else {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $spam_master_keys WHERE spamkey = 'System' AND spamtype = 'Flood Check.' AND spamy = %s AND time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)", $remote_ip ) );

				// Per minute any page.
				if ( $is_count >= '391' ) {

					$spamvalue = 'Flood Count Exceeded, ' . $is_count . ' per minute.';
					$cache     = '1H';

					// Spam Log Controller.
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log_warn                = $spam_master_log_controller->spammasterlogfloodwarn( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

					// Spam Log Controller.
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log                     = $spam_master_log_controller->spammasterlogflood( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

					// Spam Buffer Controller.
					$spam_master_buffer_controller = new SpamMasterBufferController();
					$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

					return $is_count;
				}
			}
		}
	}

}

