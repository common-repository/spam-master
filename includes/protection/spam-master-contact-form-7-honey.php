<?php
/**
 * Load spam master contact form 7 signature.
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
			add_filter( 'wpcf7_form_elements', 'spam_master_add_honeypot_to_contact_form_7', 10, 1 );
			add_filter( 'wpcf7_spam', 'spam_master_contact_form_7_honeypot_validate', 10, 1 );
		} else {
			add_filter( 'wpcf7_form_elements', 'spam_master_add_honeypot_to_contact_form_7', 10, 1 );
			add_filter( 'wpcf7_spam', 'spam_master_contact_form_7_honeypot_validate', 10, 1 );
		}

		/**
		 * Spam master contact form 7 fields.
		 *
		 * @param content $content for honey.
		 *
		 * @return content
		 */
		function spam_master_add_honeypot_to_contact_form_7( $content ) {
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
			$new_content        .= $content;
			return $new_content;
		}

		/**
		 * Spam master contact form 7 verification.
		 *
		 * @param spam $spam for honey.
		 *
		 * @return spam
		 */
		function spam_master_contact_form_7_honeypot_validate( $spam ) {
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
				// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				return $result['reason'] = array( 'spam' => wpcf7_get_message( 'spam' ) );
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
					// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					return $result['reason'] = array( 'spam' => wpcf7_get_message( 'spam' ) );
				} else {
					// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
					return $result['reason'] = array( 'spam' => wpcf7_get_message( 'spam' ) );
				}
			}
		}
	}
}
