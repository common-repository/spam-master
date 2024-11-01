<?php
/**
 * Load spam master actions.
 *
 * @package Spam Master
 */

/**
 * This is our callback function that embeds our resource in a WP_REST_Response
 *
 * @param \WP_REST_Request $request Full data about the request.
 *
 * @return \WP_Error|\WP_REST_Response
 */
function spam_master_private( WP_REST_Request $request ) {
	global $wpdb, $blog_id;

	// Add Table & Load Spam Master Options.
	if ( is_multisite() ) {
		$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
	} else {
		$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	}

	$data = json_decode( $request->get_body(), true );

	// Restrict endpoint to only valid key and hash.
	if ( empty( $request['k'] ) ) {
		return new WP_REST_Response( esc_html__( 'Silence is Golden. Request k.', 'spam_master' ), 401 );
	}
	if ( empty( $request['h'] ) ) {
		return new WP_REST_Response( esc_html__( 'Silence is Golden. Request h.', 'spam_master' ), 401 );
	}
	if ( ! empty( $request['k'] ) && ! empty( $request['h'] ) ) {
		$my_k = sanitize_text_field( $request['k'] );
		$my_h = sanitize_text_field( $request['h'] );
		if ( ! empty( $request['v'] ) ) {
			$my_v = sanitize_text_field( $request['v'] );
		} else {
			$my_v = '0';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_key = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'spam_license_key' AND spamvalue = %s", $my_k ) );
		if ( empty( $is_key ) ) {
			return new WP_REST_Response( esc_html__( 'Silence is Golden. K.', 'spam_master' ), 401 );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$is_hash = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash' AND spamvalue = %s", $my_h ) );
		if ( empty( $is_hash ) ) {
			return new WP_REST_Response( esc_html__( 'Silence is Golden. H.', 'spam_master' ), 401 );
		}
		if ( ! empty( $is_key ) && ! empty( $is_hash ) && '0' === $my_v ) {

			// Spam Action Controller.
			$spam_master_action_controller = new SpamMasterActionController();
			$is_more                       = $spam_master_action_controller->spammastergetact();

			return new WP_REST_Response( esc_html__( 'Successful Transfer.', 'spam_master' ), 200 );
		}
		if ( ! empty( $is_key ) && ! empty( $is_hash ) && '1' === $my_v ) {
			// Process stats.
			$exempt_count = array();
			// Process version.
			$spam_master_version          = constant( 'SPAM_MASTER_VERSION' );
			$db_install_version           = get_option( 'spam_master_db_version' );
			$exempt_count['Statistics'][] = array(
				'Version' => $spam_master_version . '-' . $db_install_version,
			);
			// Process status.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status           = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			$exempt_count['Statistics'][] = array(
				'Status' => $spam_master_status,
			);
			// Process firewall rules.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_firewall_rules   = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );
			$exempt_count['Statistics'][] = array(
				'Firewall' => $spam_master_firewall_rules,
			);
			// Process buffer count.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_total_buffer     = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
			$exempt_count['Statistics'][] = array(
				'Buffer' => $spam_master_total_buffer,
			);
			// Process white count.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_total_white      = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'White'" );
			$exempt_count['Statistics'][] = array(
				'White' => $spam_master_total_white,
			);
			// Process exempt count.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_total_exempt = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype LIKE %s",
					'Option',
					'%exempt%',
				)
			);
			$exempt_count['Statistics'][] = array(
				'Needles' => $spam_master_total_exempt,
			);
			// Process all keys count.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_total_logging_count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys}" );
			$exempt_count['Statistics'][]    = array(
				'Keys' => $spam_master_total_logging_count,
			);
			// Process exempt actions.
			$exempt_action  = array();
			$spampostarract = array(
				'interval'  => '60',
				'_nonce'    => '1b9e43ec5c',
				'action'    => 'heartbeat',
				'screen_id' => 'options-general',
				'has_focus' => 'true',
			);
			$spampoststract = str_replace( '=', ' ', urldecode( http_build_query( $spampostarract, '', ' ' ) ) );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$test_action = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s AND POSITION(spamvalue IN %s) > %s",
					'Option',
					'exempt-action',
					$spampoststract,
					'0',
				)
			);
			if ( ! empty( $test_action ) ) {
				$exempt_action['Exempt-Actions']['Locate'][] = array(
					'Value'  => 'heartbeat',
					'String' => $spampoststract,
					'Result' => 'Found: ' . $test_action,
				);
			} else {
				$exempt_action['Exempt-Actions']['Locate'][] = array(
					'Value'  => 'heartbeat',
					'String' => $spampoststract,
					'Result' => 'Not Found action heartbeat',
				);
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_exempt_actions = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s",
					'Option',
					'exempt-action',
				)
			);
			if ( ! empty( $spam_master_exempt_actions ) ) {
				foreach ( $spam_master_exempt_actions as $action ) {
					$spam_id                           = $action->id;
					$spam_time                         = $action->time;
					$spam_key                          = $action->spamkey;
					$spam_type                         = $action->spamtype;
					$spam_spamy                        = $action->spamy;
					$spam_value                        = $action->spamvalue;
					$exempt_action['Exempt-Actions'][] = array(
						'id'        => $spam_id,
						'time'      => $spam_time,
						'spamkey'   => $spam_key,
						'spamtype'  => $spam_type,
						'spamy'     => $spam_spamy,
						'spamvalue' => $spam_value,
					);
				}
			}
			// Process exempt keys.
			$exempt_key     = array();
			$spampostarrkey = array(
				'security' => '88c5570e1a',
			);
			$spampoststrkey = str_replace( '=', ' ', urldecode( http_build_query( $spampostarrkey, '', ' ' ) ) );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$test_key = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s AND POSITION(spamvalue IN %s) > %s",
					'Option',
					'exempt-key',
					$spampoststrkey,
					'0',
				)
			);
			if ( ! empty( $test_key ) ) {
				$exempt_key['Exempt-Keys']['Locate'][] = array(
					'Value'  => 'security',
					'String' => $spampoststrkey,
					'Result' => 'Found: ' . $test_key,
				);
			} else {
				$exempt_key['Exempt-Keys']['Locate'][] = array(
					'Value'  => 'security',
					'String' => $spampoststrkey,
					'Result' => 'Not Found key security',
				);
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_exempt_keys = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s",
					'Option',
					'exempt-key',
				)
			);
			if ( ! empty( $spam_master_exempt_keys ) ) {
				foreach ( $spam_master_exempt_keys as $key ) {
					$spam_id                     = $key->id;
					$spam_time                   = $key->time;
					$spam_key                    = $key->spamkey;
					$spam_type                   = $key->spamtype;
					$spam_spamy                  = $key->spamy;
					$spam_value                  = $key->spamvalue;
					$exempt_key['Exempt-Keys'][] = array(
						'id'        => $spam_id,
						'time'      => $spam_time,
						'spamkey'   => $spam_key,
						'spamtype'  => $spam_type,
						'spamy'     => $spam_spamy,
						'spamvalue' => $spam_value,
					);
				}
			}
			// Process exempt values.
			$exempt_value   = array();
			$spampostarrval = array(
				'security' => 'cart',
			);
			$spampoststrval = str_replace( '=', ' ', urldecode( http_build_query( $spampostarrval, '', ' ' ) ) );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$test_value = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s AND POSITION(spamvalue IN %s) > %s",
					'Option',
					'exempt-value',
					$spampoststrval,
					'0',
				)
			);
			if ( ! empty( $test_value ) ) {
				$exempt_value['Exempt-Values']['Locate'][] = array(
					'Value'  => 'cart',
					'String' => $spampoststrval,
					'Result' => 'Found: ' . $test_value,
				);
			} else {
				$exempt_value['Exempt-Values']['Locate'][] = array(
					'Value'  => 'cart',
					'String' => $spampoststrval,
					'Result' => 'Not Found value cart',
				);
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_exempt_values = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$spam_master_keys} WHERE spamkey = %s AND spamtype = %s",
					'Option',
					'exempt-value'
				)
			);
			if ( ! empty( $spam_master_exempt_values ) ) {
				foreach ( $spam_master_exempt_values as $value ) {
					$spam_id                         = $value->id;
					$spam_time                       = $value->time;
					$spam_key                        = $value->spamkey;
					$spam_type                       = $value->spamtype;
					$spam_spamy                      = $value->spamy;
					$spam_value                      = $value->spamvalue;
					$exempt_value['Exempt-Values'][] = array(
						'id'        => $spam_id,
						'time'      => $spam_time,
						'spamkey'   => $spam_key,
						'spamtype'  => $spam_type,
						'spamy'     => $spam_spamy,
						'spamvalue' => $spam_value,
					);
				}
			}
			$exempt_result = array(
				$exempt_count,
				$exempt_action,
				$exempt_key,
				$exempt_value,
			);
			return new WP_REST_Response( $exempt_result, 200 );
		}
	} else {
		return new WP_REST_Response( esc_html__( 'Silence is Golden. Request Last.', 'spam_master' ), 401 );
	}
}

/**
 * This function is where we register our routes for our example endpoint.
 *
 * @return void
 */
function prefix_register_spam_master_routes() {
	register_rest_route(
		'spam-master/v1',
		'/action',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'spam_master_private',
			'args'                => array(),
			'permission_callback' => function () {
				return true;
			},
		)
	);
}
add_action( 'rest_api_init', 'prefix_register_spam_master_routes' );

