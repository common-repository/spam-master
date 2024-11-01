<?php
/**
 * Haf controller
 *
 * @package Spam Master
 */

/**
 * Main haf class.
 *
 * @since 6.0.0
 */
class SpamMasterHAFController {

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
	 * Variable result_post_content_clean.
	 *
	 * @var result_post_content_clean $result_post_content_clean
	 **/
	protected $result_post_content_clean;

	/**
	 * Spam master haf.
	 *
	 * @param remote_ip                 $remote_ip for scan.
	 * @param blog_threat_email         $blog_threat_email for scan.
	 * @param remote_referer            $remote_referer for scan.
	 * @param dest_url                  $dest_url for scan.
	 * @param remote_agent              $remote_agent for scan.
	 * @param spamuser_a                $spamuser_a for scan.
	 * @param result_post_content_clean $result_post_content_clean for scan.
	 *
	 * @return void
	 */
	public function spammasterhaf( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $result_post_content_clean ) {
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
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_white_empath = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_white_empath'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_die = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_die'" );
		if ( empty( $spam_master_firewall_die ) || 'false' === $spam_master_firewall_die ) {
			$spam_master_firewall_die = 'false';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_firewall_page = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_page'" );
		}

		if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

			$spam_master_learn_haf_url = 'https://www.spammaster.org/core/learn/get_learn_haf.php';
			$spam_master_learning_post = array(
				'blog_license_key'    => $spam_license_key,
				'blog_threat_ip'      => $remote_ip,
				'blog_threat_email'   => $blog_threat_email,
				'blog_threat_user'    => $spamuser_a,
				'blog_threat_content' => $result_post_content_clean,
				'blog_threat_agent'   => $remote_agent,
				'blog_threat_refe'    => $remote_referer,
				'blog_threat_dest'    => $dest_url,
				'blog_web_adress'     => $spam_master_address,
				'blog_server_ip'      => $spam_master_ip,
			);
			$response                  = wp_remote_post(
				$spam_master_learn_haf_url,
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
				$data = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( empty( $data['threat'] ) ) {
					if ( 'true' === $spam_master_white_empath ) {

						// Spam White Controller.
						$spamtype                     = 'HAF';
						$spam_master_white_controller = new SpamMasterWhiteController();
						$is_pre_white                 = $spam_master_white_controller->spammasterwhiteempat( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype );
					}
				} else {
					$remote_ip = $data['threat'];
					$spamc     = $data['c'];

					// Spam Buffer Controller.
					$blog_threat_email             = false;
					$spam_master_buffer_controller = new SpamMasterBufferController();
					$is_threat                     = $spam_master_buffer_controller->spammasterbufferinsert( $remote_ip, $blog_threat_email, $spamc );

					if ( 'true' === $spam_master_firewall_die ) {
						// Wp page.
						$selected_allowed = array(
							'pre'    => array(),
							'strong' => array(),
							'a'      => array(
								'href'   => array(),
								'target' => array(),
							),
						);
						$spam_die_message = '<pre>' . __( '403 Forbidden', 'spam-master' ) . '</pre><pre>' . __( 'IP: ', 'spam-master' ) . $remote_ip . '</pre><pre>' . __( 'Browser: ', 'spam-master' ) . $remote_agent . '</pre><pre><strong>Hint: Upgrade your browser to the latest version</strong></pre><pre>' . __( 'Protected by ', 'spam-master' ) . '<a href="https://www.spammaster.org/contact/" target="_self>' . __( 'Spam Master', 'spam-master' ) . '</a></pre>';
						wp_die( wp_kses( $spam_die_message, $selected_allowed ), 'Firewall', array( 'response' => '403' ) );
					} else {
						// Firewall Page.
						wp_safe_redirect( $spam_master_firewall_page );
						exit();
					}
				}
			}
		}
	}

}

