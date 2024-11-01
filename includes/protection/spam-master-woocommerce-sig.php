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
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );

if ( 'true' === $spam_master_signature ) {
	if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

		/**
		 * Spam master registration signature.
		 *
		 * @return void
		 */
		function spam_master_woo_extra_register_field() {
			global $wpdb, $blog_id;

			?>
			<div class="clear"></div>
			<p class="form-row form-row-wide">
			<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
			</p>
			<?php
		}
		add_filter( 'woocommerce_register_form_end', 'spam_master_woo_extra_register_field' );

		/**
		 * Spam master login signature.
		 *
		 * @return void
		 */
		function spam_master_woo_extra_login_field() {
			global $wpdb, $blog_id;

			?>
			<div class="clear"></div>
			<p class="form-row form-row-wide">
			<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
			</p>
			<?php
		}
		add_filter( 'woocommerce_login_form_end', 'spam_master_woo_extra_login_field' );

		/**
		 * Spam master reset signature.
		 *
		 * @return void
		 */
		function spam_master_woo_extra_reset_field() {
			global $wpdb, $blog_id;

			?>
			<div class="clear"></div>
			<p class="form-row form-row-wide">
			<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
			</p>
			<?php
		}
		add_filter( 'woocommerce_after_lost_password_form', 'spam_master_woo_extra_reset_field' );

		/**
		 * Spam master checkout signature.
		 *
		 * @return void
		 */
		function spam_master_woo_extra_checkout_field() {
			global $wpdb, $blog_id;

			?>
			<div class="clear"></div>
			<p class="form-row form-row-wide">
			<label for="spam_master"><a href="https://www.spammaster.org/" target="_blank" title="Protected by Spam Master">Protected by Spam Master</a></label>
			</p>
			<?php
		}
		add_action( 'woocommerce_after_checkout_form', 'spam_master_woo_extra_checkout_field' );

		/**
		 * Spam master email signature.
		 *
		 * @param email $email for validation.
		 *
		 * @return void
		 */
		function spam_master_woo_extra_email_field( $email ) {
			global $wpdb, $blog_id;

			?>
			<p></p>
			<p><?php printf( esc_attr( __( 'Protected by <b>Spam Master</b>', 'spam_master' ) ) ); ?></p>
			<?php
		}
		add_action( 'woocommerce_email_footer', 'spam_master_woo_extra_email_field', 10, 1 );
	}
}
