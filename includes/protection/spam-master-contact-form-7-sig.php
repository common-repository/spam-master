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
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_signature ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		/**
		 * Spam master contact form 7 fields.
		 *
		 * @param content $content for signature.
		 *
		 * @return content
		 */
		function spam_master_contact_form_7_extra_field( $content ) {
			global $wpdb, $blog_id;

			$content .= '<p class="spam-master-sig">
						<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
						</p>';
			return $content;
		}
		add_filter( 'wpcf7_form_elements', 'spam_master_contact_form_7_extra_field', 10, 1 );
	}
}
