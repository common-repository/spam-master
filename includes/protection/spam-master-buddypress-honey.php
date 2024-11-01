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
$spam_master_integrations_buddypress = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_integrations_buddypress'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_honeypot_timetrap ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		if ( is_multisite() ) {
			add_filter( 'bp_before_registration_submit_buttons', 'spam_master_honeypot_buddy_field' );
			add_action( 'bp_signup_pre_validate', 'spam_master_honeypot_buddy_validate', 10, 1 );
		} else {
			add_filter( 'bp_before_registration_submit_buttons', 'spam_master_honeypot_buddy_field' );
			add_action( 'bp_signup_pre_validate', 'spam_master_honeypot_buddy_validate', 10, 1 );
		}

		/**
		 * Spam master buddypress honey fields.
		 */
		function spam_master_honeypot_buddy_field() {
			global $wpdb, $blog_id;

			$bp    = buddypress();
			$html  = '<div class="register-section" id="security-section">';
			$html .= '<div class="editfield">';
			$html .= '<p class="spam-master-hidden">';
			$html .= '<label for="spammaster_extra_field_1" class="spam-master-hidden">' . esc_attr( __( 'Mother Name', 'spam_master' ) ) . '<br>';
			$html .= '<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_1" id="spammaster_extra_field_1" placeholder="Insert your mother second name" autocomplete="off" value="" />';
			$html .= '</label>';
			$html .= '</p>';
			$html .= '<p class="spam-master-hidden">';
			$html .= '<label for="spammaster_extra_field_2" class="spam-master-hidden">' . esc_attr( __( 'Mother Last Name', 'spam_master' ) ) . '<br>';
			$html .= '<input class="spam-master-hidden input" type="text" name="spammaster_extra_field_2" id="spammaster_extra_field_2" placeholder="Insert your father second name" autocomplete="off" value="" />';
			$html .= '</label>';
			$html .= '</p>';
			$html .= '</div>';
			$html .= '</div>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		}

		/**
		 * Spam master buddypress validation errors.
		 *
		 * @param errors $errors for honey.
		 *
		 * @return void
		 */
		function spam_master_honeypot_buddy_validate( $errors ) {
			global $wpdb, $blog_id, $bp;

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );

			// Spam Master page.
			$spam_master_page = 'Buddypress';

			// Spam Collect Controller.
			//
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
				$bp->signup->errors['signup_email'] = __( 'SPAM MASTER', 'spam-master' ) . $spam_master_message;
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
					$bp->signup->errors['signup_email'] = __( 'SPAM MASTER', 'spam-master' ) . $spam_master_message;
				} else {
					$bp->signup->errors['signup_email'] = __( 'SPAM MASTER', 'spam-master' ) . $spam_master_message;
				}
			}
			// phpcs:ignore Squiz.PHP.NonExecutableCode.ReturnNotRequired
			return;
		}
	}
}
