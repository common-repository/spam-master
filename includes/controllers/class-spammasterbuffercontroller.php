<?php
/**
 * Buffer controller
 *
 * @package Spam Master
 */

/**
 * Main buffer class.
 *
 * @since 6.0.0
 */
class SpamMasterBufferController {

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
	 * Variable spamc.
	 *
	 * @var spamc $spamc
	 **/
	protected $spamc;

	/**
	 * Spam master buffer search.
	 *
	 * @param remote_ip         $remote_ip for buffer.
	 * @param blog_threat_email $blog_threat_email for buffer.
	 *
	 * @return buffer
	 */
	public function spammasterbuffersearch( $remote_ip, $blog_threat_email ) {
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
				$is_buffer_threat = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s", $remote_ip ) );
				if ( ! empty( $is_buffer_threat ) ) {
					// Update Buffer if it keeps nagging.
					$data_spam  = array( 'time' => current_datetime()->format( 'Y-m-d H:i:s' ) );
					$where_spam = array( 'id' => $is_buffer_threat );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

					// Spam Buffer Controller.
					$spam_master_buffer_controller = new SpamMasterBufferController();
					$is_count                      = $spam_master_buffer_controller->spammasterbuffercount();

					return 'BUFFER';
				}
			}
			if ( ! empty( $blog_threat_email ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_buffer_threat = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s", $blog_threat_email ) );
				if ( ! empty( $is_buffer_threat ) ) {
					// Update Buffer if it keeps nagging.
					$data_spam  = array( 'time' => current_datetime()->format( 'Y-m-d H:i:s' ) );
					$where_spam = array( 'id' => $is_buffer_threat );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

					// Spam Buffer Controller.
					$spam_master_buffer_controller = new SpamMasterBufferController();
					$is_count                      = $spam_master_buffer_controller->spammasterbuffercount();

					return 'BUFFER';
				}
			}
		}
	}

	/**
	 * Spam master buffer insert.
	 *
	 * @param remote_ip         $remote_ip for buffer.
	 * @param blog_threat_email $blog_threat_email for buffer.
	 * @param spamc             $spamc for buffer.
	 *
	 * @return void
	 */
	public function spammasterbufferinsert( $remote_ip, $blog_threat_email, $spamc ) {
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
				$is_buffer = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$spam_master_keys} WHERE spamkey = 'Buffer' AND spamy = %s", $remote_ip ) );
				if ( empty( $is_buffer ) ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$wpdb->insert(
						$spam_master_keys,
						array(
							'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
							'spamkey'   => 'Buffer',
							'spamtype'  => 'Cache',
							'spamy'     => $remote_ip,
							'spamvalue' => $spamc,
						)
					);
				}

				// Spam Buffer Controller.
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_count                      = $spam_master_buffer_controller->spammasterbuffercount();
			}
			if ( ! empty( $blog_threat_email ) ) {

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_buffer = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$spam_master_keys} WHERE spamkey = 'Buffer' AND spamy = %s", $blog_threat_email ) );
				if ( empty( $is_buffer ) ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$wpdb->insert(
						$spam_master_keys,
						array(
							'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
							'spamkey'   => 'Buffer',
							'spamtype'  => 'Cache',
							'spamy'     => $blog_threat_email,
							'spamvalue' => $spamc,
						)
					);
				}

				// Spam Buffer Controller.
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_count                      = $spam_master_buffer_controller->spammasterbuffercount();
			}
		}
	}

	/**
	 * Spam master buffer count.
	 *
	 * @return void
	 */
	public function spammasterbuffercount() {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_block_count_pre = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
		$spam_master_block_count     = $spam_master_block_count_pre + 1;
		// Update Count.
		$data_spam  = array( 'spamvalue' => $spam_master_block_count );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_block_count',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
	}

}

