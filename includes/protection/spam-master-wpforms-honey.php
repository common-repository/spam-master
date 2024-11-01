<?php
/**
 * Load spam master wpforms signature.
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
$spam_master_integrations_contact_form_7 = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_integrations_contact_form_7'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_honeypot_timetrap ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		if ( is_multisite() ) {
			add_action( 'wpforms_frontend_output', 'spam_master_add_honeypot_to_wpforms', 10, 2 );
			add_filter( 'wpforms_process_validate_email', 'spam_master_wpforms_honeypot_validate', 10, 3 );
		} else {
			add_action( 'wpforms_frontend_output', 'spam_master_add_honeypot_to_wpforms', 10, 2 );
			add_filter( 'wpforms_process_validate_email', 'spam_master_wpforms_honeypot_validate', 10, 3 );
		}

		/**
		 * Spam master wpforms fields.
		 *
		 * @param form_data $form_data for honey.
		 * @param form      $form for honey.
		 *
		 * @return void
		 */
		function spam_master_add_honeypot_to_wpforms( $form_data, $form ) {
			global $wpdb, $blog_id;

			$spam_master_field_1 = '<p class="spam-master-hidden">
						<label class="spam-master-hidden" for="spammaster_extra_field_1">Insert your mother second name<br>
						<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_1" id="spammaster_extra_field_1" autocomplete="off" value="" />
						</label>
						</p>';
			$spam_master_field_2 = '<p class="spam-master-hidden">
						<label class="spam-master-hidden" for="spammaster_extra_field_2">Insert your father second name<br>
						<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_2" id="spammaster_extra_field_2" autocomplete="off" value="" />
						</label>
						</p>';
			$new_content         = $spam_master_field_1;
			$new_content        .= $spam_master_field_2;
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $new_content;
		}

		/**
		 * Spam master wpforms verification.
		 *
		 * @param field_id     $field_id for honey.
		 * @param field_submit $field_submit for honey.
		 * @param form_data    $form_data for honey.
		 *
		 * @return void
		 */
		function spam_master_wpforms_honeypot_validate( $field_id, $field_submit, $form_data ) {
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
			$spam_master_page = 'Contact Form';

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
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message );
				return;
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
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = 'empty - Content' . wp_unslash( wp_json_encode( $_POST ) );
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$spammaster_extra_field_2 = wp_unslash( $_POST['spammaster_extra_field_2'] ) . ' - Content' . wp_unslash( wp_json_encode( $_POST ) );
				}
				// Spam Honey Controller.
				$spam_master_honey_controller = new SpamMasterHoneyController();
				$is_honey                     = $spam_master_honey_controller->spammasterhoney( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $is_user['blog_threat_content'] );
				if ( $is_honey ) {
					wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message );
					return;
				} else {
					wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_attr( __( 'SPAM MASTER: ', 'spam_master' ) . $spam_master_message );
					return;
				}
			}
		}
	}
}
