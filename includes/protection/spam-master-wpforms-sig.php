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
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_signature ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		/**
		 * Spam master wpforms fields.
		 *
		 * @param form_data $form_data for signature.
		 *
		 * @return void
		 */
		function spam_master_wpforms_extra_field( $form_data ) {
			global $wpdb, $blog_id;

			// phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction, WordPress.WP.I18n.NoHtmlWrappedStrings
			_e(
				// phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
				'<br><p class="spam-master-sig">
					<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
				</p>'
			);

		}
		add_filter( 'wpforms_display_submit_after', 'spam_master_wpforms_extra_field', 10, 1 );
	}
}
