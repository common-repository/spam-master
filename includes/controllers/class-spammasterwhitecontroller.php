<?php
/**
 * Whitelist controller
 *
 * @package Spam Master
 */

/**
 * Main whitelist class.
 *
 * @since 6.0.0
 */
class SpamMasterWhiteController {

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
	 * Spam master white search.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 *
	 * @return WHITE
	 */
	public function spammasterwhitesearch( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}

		if ( ! empty( $remote_ip ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$is_white = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $remote_ip ) );
			if ( ! empty( $is_white ) ) {

				if ( ! is_admin() ) {

					// Log InUp Controller.
					$spamvalue                  = 'Whitelist Ip';
					$cache                      = '3M';
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

				}

				return 'WHITE';
			}
		}
		if ( ! empty( $blog_threat_email ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$is_white = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $blog_threat_email ) );
			if ( ! empty( $is_white ) ) {

				if ( ! is_admin() ) {

					// Log InUp Controller.
					$spamvalue                  = 'Whitelist Email';
					$cache                      = '3M';
					$spam_master_log_controller = new SpamMasterLogController();
					$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

				}

				return 'WHITE';
			}
		}
	}

	/**
	 * Spam master white admin.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 *
	 * @return ISADMIN
	 */
	public function spammasterwhiteadmin( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype ) {
		global $wpdb, $blog_id;

		if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) || current_user_can( 'author' ) || current_user_can( 'contributor' ) || current_user_can( 'super_admin' ) ) {

			if ( ! is_admin() ) {

				// Log InUp Controller.
				$spamvalue                  = 'Whitelist Administrator';
				$cache                      = '3M';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

			}

			return 'ISADMIN';
		}
	}

	/**
	 * Spam master white empath.
	 *
	 * @param remote_ip         $remote_ip for scan.
	 * @param blog_threat_email $blog_threat_email for scan.
	 * @param remote_referer    $remote_referer for scan.
	 * @param dest_url          $dest_url for scan.
	 * @param remote_agent      $remote_agent for scan.
	 * @param spamuser_a        $spamuser_a for scan.
	 * @param spamtype          $spamtype for scan.
	 *
	 * @return EMPATH
	 */
	public function spammasterwhiteempat( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			if ( ! empty( $remote_ip ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_white = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $remote_ip ) );
				if ( empty( $is_white ) ) {

					if ( ! is_admin() ) {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$is_empath = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$spam_master_keys} WHERE spamkey = 'White' AND spamy = %s", $remote_ip ) );
						if ( empty( $is_empath ) ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
							$wpdb->insert(
								$spam_master_keys,
								array(
									'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
									'spamkey'   => 'White',
									'spamtype'  => 'Cache',
									'spamy'     => $remote_ip,
									'spamvalue' => '4H',
								)
							);

							return 'EMPATH';
						}
					}
				}
			}
			if ( ! empty( $blog_threat_email ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_white = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $blog_threat_email ) );
				if ( empty( $is_white ) ) {

					if ( ! is_admin() ) {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$is_empath = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$spam_master_keys} WHERE spamkey = 'White' AND spamy = %s", $remote_ip ) );
						if ( empty( $is_empath ) ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
							$wpdb->insert(
								$spam_master_keys,
								array(
									'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
									'spamkey'   => 'White',
									'spamtype'  => 'Cache',
									'spamy'     => $blog_threat_email,
									'spamvalue' => '4H',
								)
							);

							return 'EMPATH';
						}
					}
				}
			}
		}
	}

}

