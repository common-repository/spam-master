<?php
/**
 * User controller
 *
 * @package Spam Master
 */

/**
 * Main user class.
 *
 * @since 6.0.0
 */
class SpamMasterUserController {

	/**
	 * Variable spaminitial.
	 *
	 * @var spaminitial $spaminitial
	 **/
	protected $spaminitial;

	/**
	 * Variable spampreemail.
	 *
	 * @var spampreemail $spampreemail
	 **/
	protected $spampreemail;

	/**
	 * Spam master get user.
	 *
	 * @param spaminitial  $spaminitial for scan.
	 * @param spampreemail $spampreemail for scan.
	 *
	 * @return array
	 */
	public function spammastergetuser( $spaminitial, $spampreemail ) {
		global $wpdb, $blog_id;

		// exempt admins from check.
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include ABSPATH . 'wp-includes/pluggable.php';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$spampost = $_POST;
		// Start scan of post.
		if ( ! empty( $spampost ) && is_array( $spampost ) ) {
			$spampoststr = str_replace( '=', ' ', urldecode( http_build_query( $spampost, '', ' ' ) ) );
		} else {
			$spampoststr = 'contentless';
		}

		// Current User.
		$current_user_id = get_current_user_id();
		if ( ! empty( $current_user_id ) && '0' !== $current_user_id ) {
			$spam_new_user = get_userdata( $current_user_id );
			if ( ! empty( $spam_new_user ) ) {
				$spam_username     = $spam_new_user->user_login;
				$blog_threat_email = $spam_new_user->user_email;
			} else {
				$spam_username     = 'none';
				$blog_threat_email = $spaminitial . '@' . wp_rand( 10000000, 99999999 ) . '.wp';
			}
			$spam_avatar = get_avatar(
				$current_user_id,
				48,
				'',
				$current_user_id,
				array(
					'scheme'        => 'https',
					'force_display' => false,
				)
			);
			$spamuser    = array(
				'ID'       => $current_user_id,
				'username' => $spam_username,
				'avatar'   => $spam_avatar,
			);
		} else {
			$blog_threat_email = $spaminitial . '@' . wp_rand( 10000000, 99999999 ) . '.wp';
			// Collect email to scan.
			preg_match( '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i', $spampoststr, $matches );
			if ( $matches ) {
				foreach ( $matches as $key => $val ) {
					if ( filter_var( $val, FILTER_VALIDATE_EMAIL ) ) {
						$blog_threat_email = wp_strip_all_tags( substr( $val, 0, 256 ) );
					}
				}
			} else {
				$blog_threat_email = $spaminitial . '@' . wp_rand( 10000000, 99999999 ) . '.wp';
			}
			$spamuser = array( 'ID' => 'none' );
		}

		$spamuser_a    = wp_json_encode( $spamuser );
		$spampoststr_a = wp_json_encode( $spampost );
		return array(
			'spamuserA'           => $spamuser_a,
			'blog_threat_email'   => $blog_threat_email,
			'blog_threat_content' => $spampoststr_a,
		);
	}

}

