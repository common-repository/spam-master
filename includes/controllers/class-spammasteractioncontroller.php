<?php
/**
 * Action controller
 *
 * @package Spam Master
 */

/**
 * Main action class.
 *
 * @since 6.0.0
 */
class SpamMasterActionController {

	/**
	 * Variable spama.
	 *
	 * @var spama $spama
	 **/
	protected $spama;

	/**
	 * Spam master action.
	 *
	 * @param spama $spama for action.
	 *
	 * @return void
	 */
	public function spammasteract( $spama ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}

		if ( '1' === $spama ) {
			// Update Spama for Cron.
			$data_spam  = array( 'spamvalue' => $spama );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_new_options',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// Spam Action Controller.
			$spam_master_action_controller = new SpamMasterActionController();
			$is_more                       = $spam_master_action_controller->spammastergetact();

		}

	}

	/**
	 * Spam master get action.
	 *
	 * @return void
	 */
	public function spammastergetact() {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_license_key = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_db_protection_hash = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_address = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_address'" ), 0, 256 );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		$spam_master_learn_act_url = 'https://www.spammaster.org/core/learn/get_learn_act.php';
		$spam_master_learning_post = array(
			'blog_license_key' => $spam_license_key,
			'blog_hash_key'    => $spam_master_db_protection_hash,
		);
		$response                  = wp_remote_post(
			$spam_master_learn_act_url,
			array(
				'method'  => 'POST',
				'timeout' => 90,
				'body'    => $spam_master_learning_post,
			)
		);
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam_master' ) . $error_message );
		} else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $data['key'] ) || empty( $data['hash'] ) ) {

				// Update Spama Done.
				$data_spam  = array( 'spamvalue' => '0' );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_new_options',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			} else {
				// Check Key & Hash.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_key = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'spam_license_key' AND spamvalue = %s", $data['key'] ) );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$is_hash = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash' AND spamvalue = %s", $data['hash'] ) );
				if ( ! empty( $is_key ) && ! empty( $is_hash ) ) {

					if ( 'Add' === $data['action'] ) {
						if ( 'Buffer' === $data['where'] ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $data['pack'] ) );
						}
						if ( 'White' === $data['where'] ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamy = %s", $data['pack'] ) );
						}
						if ( 'Option' === $data['where'] ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = %s AND spamvalue = %s", $data['type'], $data['value'] ) );
						}
						// No duplicates.
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$is_double = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s AND spamy = %s AND spamvalue = %s", $data['where'], $data['type'], $data['pack'], $data['value'] ) );
						if ( empty( $is_double ) ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
							$wpdb->insert(
								$spam_master_keys,
								array(
									'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
									'spamkey'   => $data['where'],
									'spamtype'  => $data['type'],
									'spamy'     => $data['pack'],
									'spamvalue' => $data['value'],
								)
							);
						}
					}
					if ( 'Remove' === $data['action'] ) {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE spamkey = %s AND spamtype = %s AND spamy = %s AND spamvalue = %s", $data['where'], $data['type'], $data['pack'], $data['value'] ) );
					}
					if ( 'Change' === $data['action'] ) {
						$data_up  = array(
							'spamy'     => $data['pack'],
							'spamvalue' => $data['value'],
						);
						$where_up = array(
							'spamkey'  => $data['where'],
							'spamtype' => $data['type'],
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_up, $where_up );
					}

					// Spam Action Controller.
					$spam_master_action_controller = new SpamMasterActionController();
					$is_more                       = $spam_master_action_controller->spammastergetactmore();
				}
			}
		}
	}

	/**
	 * Spam master get more action.
	 *
	 * @return void
	 */
	public function spammastergetactmore() {
		global $wpdb, $blog_id;

		// Spam Action Controller.
		$spam_master_action_controller = new SpamMasterActionController();
		$is_more                       = $spam_master_action_controller->spammastergetact();

	}

}
