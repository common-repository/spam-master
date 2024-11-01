<?php
/**
 * Load spam master buddy signature.
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
$spam_master_integrations_buddypress = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_integrations_buddypress'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_signature ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		/**
		 * Spam master BuddyPress registration signature.
		 *
		 * @return void
		 */
		function bp_spam_master_registration_sig() {
			global $wpdb, $blog_id;

				$bp    = buddypress();
				$html  = '<div class="clear"></div>';
				$html .= '<div class="submit">';
				$html .= '<p class="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></p>';
				$html .= '</div>';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $html;
		}
		add_filter( 'bp_after_registration_submit_buttons', 'bp_spam_master_registration_sig' );

		/**
		 * Spam master buddypress activation signature.
		 *
		 * @return void
		 */
		function bp_spam_master_activation_sig() {
			global $wpdb, $blog_id;

			?>
			<div class="clear"></div>
			<p class="submit">
			<p class="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></p>
			</p>
			<?php
		}
		add_filter( 'bp_after_activate_content', 'bp_spam_master_activation_sig' );

		/**
		 * Spam master email signature.
		 *
		 * @param message      $message for email.
		 * @param user_id      $user_id for email.
		 * @param activate_url $activate_url for email.
		 *
		 * @return string
		 */
		function bp_spam_master_email_field( $message, $user_id, $activate_url ) {
			global $wpdb, $blog_id;

			$message .= '<p></p>';
			$message .= '<p>' . printf( esc_attr( __( 'Protected by <b>Spam Master</b>', 'spam_master' ) ) ) . '</p>';

			return $message;
		}
		add_action( 'bp_core_signup_send_validation_email_message', 'bp_spam_master_email_field', 10, 3 );
	}
}
