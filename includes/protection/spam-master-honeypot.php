<?php
/**
 * Load spam master honeypot.
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
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_honeypot_timetrap = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_honeypot_timetrap_speed = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap_speed'" );

if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

	if ( 'true' === $spam_master_honeypot_timetrap ) {
		// MULTISITE HOOKS.
		if ( is_multisite() ) {
			add_action( 'signup_extra_fields', 'spam_master_honeypot_register_field' );
			add_filter( 'wpmu_validate_user_signup', 'spam_master_honeypot_register_errors_multi', 10, 1 );
			add_action( 'register_form', 'spam_master_honeypot_register_field' );
			add_filter( 'registration_errors', 'spam_master_honeypot_register_single_errors', 10, 3 );
			add_action( 'login_form', 'spam_master_honeypot_register_field' );
			add_filter( 'login_errors', 'spam_master_honeypot_login_single_errors', 10, 1 );
			add_filter( 'lostpassword_form', 'spam_master_honeypot_register_field' );
			add_filter( 'lostpassword_post', 'spam_master_honeypot_login_single_errors', 10, 1 );
			add_action( 'comment_form_before_fields', 'spam_master_honeypot_register_field' );
			add_filter( 'preprocess_comment', 'spam_master_verify_honey_comment_data', 10, 1 );
		} else {
			// SINGLE SITE HOOKS.
			add_action( 'register_form', 'spam_master_honeypot_register_field' );
			add_filter( 'registration_errors', 'spam_master_honeypot_register_single_errors', 10, 3 );
			add_action( 'login_form', 'spam_master_honeypot_register_field' );
			add_filter( 'login_errors', 'spam_master_honeypot_login_single_errors', 10, 1 );
			add_filter( 'lostpassword_form', 'spam_master_honeypot_register_field' );
			add_filter( 'lostpassword_post', 'spam_master_honeypot_login_single_errors', 10, 1 );
			add_action( 'comment_form_before_fields', 'spam_master_honeypot_register_field' );
			add_filter( 'preprocess_comment', 'spam_master_verify_honey_comment_data', 10, 1 );
		}

		/**
		 * Spam master honeypot fields.
		 *
		 * @return void
		 */
		function spam_master_honeypot_register_field() {
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
		 * Spam master multi-site validation errors.
		 *
		 * @param result $result for honey.
		 *
		 * @return result
		 */
		function spam_master_honeypot_register_errors_multi( $result ) {
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
			$spam_master_page = 'Registration';

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
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<p class="error"><strong>SPAM MASTER</strong> ' . $spam_master_message . '</p>';
				exit();
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
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<p class="error"><strong>SPAM MASTER</strong> ' . $spam_master_message . '</p>';
					exit();
				} else {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<p class="error"><strong>SPAM MASTER</strong> ' . $spam_master_message . '</p>';
					exit();
				}
			}
			return $result;
			// End Honey multi validation.
		}

		/**
		 * Spam master single-site validation errors.
		 *
		 * @param errors               $errors for honey.
		 * @param sanitized_user_login $sanitized_user_login for honey.
		 * @param user_email           $user_email for honey.
		 *
		 * @return errors
		 */
		function spam_master_honeypot_register_single_errors( $errors, $sanitized_user_login, $user_email ) {
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
			$spam_master_page = 'Registration';

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
				$errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				return $errors;
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
					$errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $errors;
				} else {
					$errors->add( 'invalid_email', esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
					return $errors;
				}
			}
			return $errors;
			// End Honey single validation.
		}

		/**
		 * Spam master single-site login validation errors.
		 *
		 * @param error $error for honey.
		 *
		 * @return error
		 */
		function spam_master_honeypot_login_single_errors( $error ) {
			global $wpdb, $blog_id, $errors, $user_email;

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
			$spam_master_page = 'Login';

			// Spam Collect Controller.
			//
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			//
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				$error = '<strong>SPAM MASTER</strong>: ' . $spam_master_message;
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
					$error = '<strong>SPAM MASTER</strong>: ' . $spam_master_message;
				} else {
					$error = '<strong>SPAM MASTER</strong>: ' . $spam_master_message;
				}
			}
			return $error;
			// End Honey single validation.
		}

		/**
		 * Spam master comment verification.
		 *
		 * @param commentdata $commentdata for honey.
		 *
		 * @return commentdata
		 */
		function spam_master_verify_honey_comment_data( $commentdata ) {
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
			$spam_master_page = 'Comment';

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'honey_bot';
			$spampreemail                = false;
			$is_user                     = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Prepare Comment.
			if ( ! empty( $commentdata['comment_content'] ) ) {
				$result_comment_content_trim  = substr( $commentdata['comment_content'], 0, 963 );
				$result_comment_content_clean = wp_strip_all_tags( stripslashes_deep( $result_comment_content_trim ), true );
			} else {
				$result_comment_content_clean = 'empty';
			}

			// Spam Buffer Controller.
			$spam_master_buffer_controller = new SpamMasterBufferController();
			$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
			if ( ! empty( $is_buffer ) ) {
				return wp_die( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
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
					return wp_die( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				} else {
					return wp_die( esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message ) );
				}
			}
			return $commentdata;
		}
	}
}
