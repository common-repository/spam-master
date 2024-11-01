<?php
/**
 * Load spam master woo signature.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
// Add Table & Load Spam Master Options.
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_honeypot_timetrap = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_honeypot_timetrap_speed = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap_speed'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_integrations_woocommerce = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_integrations_woocommerce'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_honeypot_timetrap ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		if ( is_multisite() ) {
			add_filter( 'woocommerce_login_form_end', 'spam_master_honeypot_register_woo_field' );
			add_filter( 'woocommerce_process_login_errors', 'spam_master_honeypot_process_woo_login_errors', 10, 3 );
			add_filter( 'woocommerce_register_form_end', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_register_post', 'spam_master_honeypot_register_woocommerce_errors', 10, 3 );
			add_action( 'woocommerce_lostpassword_form', 'spam_master_honeypot_register_woo_field' );
			add_action( 'validate_password_reset', 'spam_master_honeypot_reset_woocommerce_errors', 10, 2 );
			add_filter( 'woocommerce_checkout_form_end', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_after_order_notes', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_checkout_process', 'spam_master_honeypot_process_checkout_errors' );
		} else {
			add_filter( 'woocommerce_login_form_end', 'spam_master_honeypot_register_woo_field' );
			add_filter( 'woocommerce_process_login_errors', 'spam_master_honeypot_process_woo_login_errors', 10, 3 );
			add_filter( 'woocommerce_register_form_end', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_register_post', 'spam_master_honeypot_register_woocommerce_errors', 10, 3 );
			add_action( 'woocommerce_lostpassword_form', 'spam_master_honeypot_register_woo_field' );
			add_action( 'validate_password_reset', 'spam_master_honeypot_reset_woocommerce_errors', 10, 2 );
			add_filter( 'woocommerce_checkout_form_end', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_after_order_notes', 'spam_master_honeypot_register_woo_field' );
			add_action( 'woocommerce_checkout_process', 'spam_master_honeypot_process_checkout_errors' );
		}

		/**
		 * Spam master woo honeypot fields.
		 *
		 * @return void
		 */
		function spam_master_honeypot_register_woo_field() {
			global $wpdb, $blog_id;

			?>
			<p class="spam-master-hidden">
			<label for="spammaster_extra_field_1" class="spam-master-hidden"><?php echo esc_attr( __( 'Insert your mother second name', 'spam_master' ) ); ?><br>
			<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_1" id="spammaster_extra_field_1" placeholder="Mother Name" autocomplete="off" value="" />
			</label>
			</p>
			<p class="spam-master-hidden">
			<label for="spammaster_extra_field_2" class="spam-master-hidden"><?php echo esc_attr( __( 'Insert your father second name', 'spam_master' ) ); ?><br>
			<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_2" id="spammaster_extra_field_2" placeholder="Mother Last Name" autocomplete="off" value="" />
			</label>
			</p>
			<?php
			// END FIELD.
		}

		/**
		 * Spam master woocommerce login validation errors.
		 *
		 * @param validation_error    $validation_error for honey.
		 * @param creds_user_login    $creds_user_login for honey.
		 * @param creds_user_password $creds_user_password for honey.
		 *
		 * @return errors
		 */
		function spam_master_honeypot_process_woo_login_errors( $validation_error, $creds_user_login, $creds_user_password ) {
			global $wpdb, $blog_id;

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );

			// Spam Master page.
			$spam_master_page = 'Woocommerce Login';

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				$validation_error->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				return $validation_error;
			}

			// Check Fields.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['spammaster_extra_field_1'] ) || ! empty( $_POST['spammaster_extra_field_2'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_1'] ) || empty( $_POST['spammaster_extra_field_1'] ) ) {
					$spammaster_extra_field_1 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_1 = wp_unslash( $_POST['spammaster_extra_field_1'] );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_2'] ) || empty( $_POST['spammaster_extra_field_2'] ) ) {
					$spammaster_extra_field_2 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = wp_unslash( $_POST['spammaster_extra_field_2'] );
				}
				// Spam Honey Controller.
				$spam_master_honey_controller = new SpamMasterHoneyController();
				$is_honey                     = $spam_master_honey_controller->spammasterhoney( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $is_user['blog_threat_content'] );
				if ( $is_honey ) {
					$validation_error->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $validation_error;
				} else {
					$validation_error->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $validation_error;
				}
			}
			return $validation_error;
			// End Honey single validation.
		}

		/**
		 * Spam master woocommerce registration validation errors.
		 *
		 * @param username          $username for honey.
		 * @param email             $email for honey.
		 * @param validation_errors $validation_errors for honey.
		 *
		 * @return errors
		 */
		function spam_master_honeypot_register_woocommerce_errors( $username, $email, $validation_errors ) {
			global $wpdb, $blog_id;

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );

			// Spam Master page.
			$spam_master_page = 'Woocommerce Registration';

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				$validation_errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				return $validation_errors;
			}

			// Check Fields.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['spammaster_extra_field_1'] ) || ! empty( $_POST['spammaster_extra_field_2'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_1'] ) || empty( $_POST['spammaster_extra_field_1'] ) ) {
					$spammaster_extra_field_1 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_1 = wp_unslash( $_POST['spammaster_extra_field_1'] );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_2'] ) || empty( $_POST['spammaster_extra_field_2'] ) ) {
					$spammaster_extra_field_2 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = wp_unslash( $_POST['spammaster_extra_field_2'] );
				}
				// Spam Honey Controller.
				$spam_master_honey_controller = new SpamMasterHoneyController();
				$is_honey                     = $spam_master_honey_controller->spammasterhoney( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $is_user['blog_threat_content'] );
				if ( $is_honey ) {
					$validation_errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $validation_errors;
				} else {
					$validation_errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $validation_errors;
				}
			}
			return $validation_errors;
			// End Honey single validation.
		}

		/**
		 * Spam master woocommerce checkout validation errors.
		 */
		function spam_master_honeypot_process_checkout_errors() {
			global $wpdb, $blog_id;

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );

			// Spam Master page.
			$spam_master_page = 'Woocommerce Checkout';

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				wc_add_notice( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
			}

			// Check Fields.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['spammaster_extra_field_1'] ) || ! empty( $_POST['spammaster_extra_field_2'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_1'] ) || empty( $_POST['spammaster_extra_field_1'] ) ) {
					$spammaster_extra_field_1 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_1 = wp_unslash( $_POST['spammaster_extra_field_1'] );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_2'] ) || empty( $_POST['spammaster_extra_field_2'] ) ) {
					$spammaster_extra_field_2 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = wp_unslash( $_POST['spammaster_extra_field_2'] );
				}
				// Spam Honey Controller.
				$spam_master_honey_controller = new SpamMasterHoneyController();
				$is_honey                     = $spam_master_honey_controller->spammasterhoney( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $is_user['blog_threat_content'] );
				if ( $is_honey ) {
					wc_add_notice( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				} else {
					wc_add_notice( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				}
			}
		}

		/**
		 * Spam master woocommerce reset password validation errors.
		 *
		 * @param errors $errors for honey.
		 * @param user   $user for honey.
		 *
		 * @return errors
		 */
		function spam_master_honeypot_reset_woocommerce_errors( $errors, $user ) {
			global $wpdb, $blog_id;

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );

			// Spam Master page.
			$spam_master_page = 'Woocommerce Login';

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				$errors->add( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
			}

			// Check Fields.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['spammaster_extra_field_1'] ) || ! empty( $_POST['spammaster_extra_field_2'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_1'] ) || empty( $_POST['spammaster_extra_field_1'] ) ) {
					$spammaster_extra_field_1 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_1 = wp_unslash( $_POST['spammaster_extra_field_1'] );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( ! isset( $_POST['spammaster_extra_field_2'] ) || empty( $_POST['spammaster_extra_field_2'] ) ) {
					$spammaster_extra_field_2 = 'empty';
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = wp_unslash( $_POST['spammaster_extra_field_2'] );
				}
				// Spam Honey Controller.
				$spam_master_honey_controller = new SpamMasterHoneyController();
				$is_honey                     = $spam_master_honey_controller->spammasterhoney( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $is_user['blog_threat_content'] );
				if ( $is_honey ) {
					$errors->add( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				} else {
					$errors->add( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				}
			}
			return $errors;
		}
	}
}
