<?php
/**
 * Honey controller
 *
 * @package Spam Master
 */

/**
 * Main honey class.
 *
 * @since 6.0.0
 */
class SpamMasterHoneyController {

	/**
	 * Variable remote_ip.
	 *
	 * @var remote_ip $remote_ip
	 **/
	protected $remote_ip;

	/**
	 * Variable blog_threat_email.
	 *
	 * @var blog_threat_email $blog_threat_email
	 **/
	protected $blog_threat_email;

	/**
	 * Variable remote_referer.
	 *
	 * @var remote_referer $remote_referer
	 **/
	protected $remote_referer;

	/**
	 * Variable dest_url.
	 *
	 * @var dest_url $dest_url
	 **/
	protected $dest_url;

	/**
	 * Variable remote_agent.
	 *
	 * @var remote_agent $remote_agent
	 **/
	protected $remote_agent;

	/**
	 * Variable spamuser_a.
	 *
	 * @var spamuser_a $spamuser_a
	 **/
	protected $spamuser_a;

	/**
	 * Variable spammaster_extra_field_1.
	 *
	 * @var spammaster_extra_field_1 $spammaster_extra_field_1
	 **/
	protected $spammaster_extra_field_1;

	/**
	 * Variable spammaster_extra_field_2.
	 *
	 * @var spammaster_extra_field_2 $spammaster_extra_field_2
	 **/
	protected $spammaster_extra_field_2;

	/**
	 * Variable trigger_s.
	 *
	 * @var trigger_s $trigger_s
	 **/
	protected $trigger_s;

	/**
	 * Variable spam_master_page.
	 *
	 * @var spam_master_page $spam_master_page
	 **/
	protected $spam_master_page;

	/**
	 * Variable spam_master_content.
	 *
	 * @var spam_master_content $spam_master_content
	 **/
	protected $spam_master_content;

	/**
	 * Spam master honey.
	 *
	 * @param remote_ip                $remote_ip for scan.
	 * @param blog_threat_email        $blog_threat_email for scan.
	 * @param remote_referer           $remote_referer for scan.
	 * @param dest_url                 $dest_url for scan.
	 * @param remote_agent             $remote_agent for scan.
	 * @param spamuser_a               $spamuser_a for scan.
	 * @param spammaster_extra_field_1 $spammaster_extra_field_1 for scan.
	 * @param spammaster_extra_field_2 $spammaster_extra_field_2 for scan.
	 * @param spam_master_page         $spam_master_page for scan.
	 * @param spam_master_content      $spam_master_content for scan.
	 *
	 * @return void
	 */
	public function spammasterhoney( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spammaster_extra_field_1, $spammaster_extra_field_2, $spam_master_page, $spam_master_content ) {
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
		$spam_license_key = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_address = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_address'" ), 0, 256 );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		$result_post_content_trim  = substr( wp_unslash( $spam_master_content ), 0, 963 );
		$result_post_content_clean = wp_strip_all_tags( stripslashes_deep( $result_post_content_trim ), true );

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			$spam_master_learn_h_url   = 'https://www.spammaster.org/core/learn/get_learn_honey_2.php';
			$spam_master_learning_post = array(
				'blog_license_key'    => $spam_license_key,
				'blog_threat_ip'      => $remote_ip,
				'blog_threat_user'    => $spamuser_a,
				'blog_threat_type'    => 'honeypot',
				'blog_threat_email'   => $blog_threat_email,
				'blog_threat_content' => substr( 'Honeypot ' . $spam_master_page . ' Field 1: ' . $spammaster_extra_field_1 . ', Field 2: ' . $spammaster_extra_field_2 . ', MSG: ' . $result_post_content_clean, 0, 963 ),
				'blog_threat_agent'   => $remote_agent,
				'blog_threat_refe'    => $remote_referer,
				'blog_threat_dest'    => $dest_url,
				'blog_web_adress'     => $spam_master_address,
				'blog_server_ip'      => $spam_master_ip,
			);

			$response = wp_remote_post(
				$spam_master_learn_h_url,
				array(
					'method'  => 'POST',
					'timeout' => 90,
					'body'    => $spam_master_learning_post,
				)
			);
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam_master' ) . $error_message );
			} else {
				$data      = json_decode( wp_remote_retrieve_body( $response ), true );
				$remote_ip = $data['threat'];
				$spamc     = $data['c'];

				// Spam Buffer Controller.
				$blog_threat_email             = false;
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_threat                     = $spam_master_buffer_controller->spammasterbufferinsert( $remote_ip, $blog_threat_email, $spamc );

				// Spam Buffer Controller.
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_buffer_count               = $spam_master_buffer_controller->spammasterbuffercount();

			}
		}
	}

}

