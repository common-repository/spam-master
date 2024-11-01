<?php
/**
 * Load spam master contact form 7.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_cache_proxie = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cache_proxie'" );

if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

	/**
	 * Spam master firewall.
	 *
	 * @return void
	 */
	function spam_master_frontend_firewall() {
		global $wpdb, $blog_id;

		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_on = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_on'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_die = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_die'" );
		if ( empty( $spam_master_firewall_die ) || 'false' === $spam_master_firewall_die ) {
			$spam_master_firewall_die = 'false';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_firewall_page = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_page'" );
		}
		// Spam Master page.
		$spam_master_page = 'HAF';
		// Spam Type.
		$spamtype = 'HAF';
		// Disc notifications.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
		if ( 'FREE' === $spam_master_type ) {
			$spammasterdateshort               = current_datetime()->format( 'Y-m-d' );
			$spam_master_invitation_controller = new SpamMasterInvitationController();
			$is_invitation                     = $spam_master_invitation_controller->spammasterdatecheck( $spammasterdateshort );
		}

		// Spam Collect Controller.
		$spam_master_collect_controller = new SpamMasterCollectController();
		$collect_now                    = true;
		$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

		// Spam User Controller.
		$spam_master_user_controller = new SpamMasterUserController();
		// Spam Initial.
		$spaminitial  = 'haf';
		$spampreemail = false;
		$is_user      = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

		// Spam White Controller.
		$spam_master_white_controller = new SpamMasterWhiteController();
		$is_spamadmin                 = $spam_master_white_controller->spammasterwhiteadmin( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype );
		if ( ! empty( $is_spamadmin ) ) {
			$yesitisa = true;
		} else {
			// Spam White Controller.
			$spam_master_white_controller = new SpamMasterWhiteController();
			$is_white                     = $spam_master_white_controller->spammasterwhitesearch( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype );
			if ( ! empty( $is_white ) ) {
				$yesitis = true;
			} else {
				// Spam Buffer Controller.
				$spam_master_buffer_controller = new SpamMasterBufferController();
				$is_buffer                     = $spam_master_buffer_controller->spammasterbuffersearch( $is_collected['remote_ip'], $is_user['blog_threat_email'] );
				if ( ! empty( $is_buffer ) ) {
					if ( 'true' === $spam_master_firewall_on ) {
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
							$spam_die_message = '<pre>' . __( '403 Forbidden', 'spam-master' ) . '</pre><pre>' . __( 'IP: ', 'spam-master' ) . $is_collected['remote_ip'] . '</pre><pre>' . __( 'Browser: ', 'spam-master' ) . $is_collected['remote_agent'] . '</pre><pre><strong>Hint: Upgrade your browser to the latest version</strong></pre><pre>' . __( 'Protected by ', 'spam-master' ) . '<a href="https://www.spammaster.org/contact/" target="_self>' . __( 'Spam Master', 'spam-master' ) . '</a></pre>';
							wp_die( wp_kses( $spam_die_message, $selected_allowed ), 'Firewall', array( 'response' => '403' ) );
						} else {
							// Firewall page.
							wp_safe_redirect( $spam_master_firewall_page );
							exit;
						}
					}
				} else {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing
					if ( ! empty( $_POST ) && ! is_user_logged_in() ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing
						$spam_elusive = $_POST;
						if ( '1' === $spam_master_firewall_rules || '2' === $spam_master_firewall_rules ) {
							// Spam Elusive Controller.
							$spam_master_elusive_controller = new SpamMasterElusiveController();
							$is_elusive                     = $spam_master_elusive_controller->spammasterelusive( $spam_elusive, $is_collected['dest_url'] );
							if ( ! empty( $is_elusive ) && 'bail' !== $is_elusive ) {
								$blog_threat_email         = $is_elusive;
								$result_post_content_json  = wp_json_encode( $spam_elusive );
								$result_post_content_trim  = substr( wp_unslash( $result_post_content_json ), 0, 963 );
								$result_post_content_clean = wp_strip_all_tags( stripslashes_deep( $result_post_content_trim ), true );
								if ( empty( $result_post_content_clean ) ) {
									$result_post_content_clean = 'is_elusive_w';
								}
								// Spam HAF Controller.
								$spam_master_haf_controller = new SpamMasterHAFController();
								$is_haf                     = $spam_master_haf_controller->spammasterhaf( $is_collected['remote_ip'], $blog_threat_email, $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], 'is_elusive_w - ' . $result_post_content_clean );
							}
						}
					}
				}
			}
		}
	}
	// Login page.
	// add_action( 'login_init', 'spam_master_frontend_firewall' );
	// Admin pages.
	// add_action('admin_init', 'spam_master_frontend_firewall');
	// Frontend pages.
	add_action( 'init', 'spam_master_frontend_firewall' );

	// IMPLEMENT PROXY FRONTEND.
	if ( 'true' === $spam_master_cache_proxie ) {

		/**
		 * Cache Control.
		 *
		 * @return void
		 */
		function spam_master_no_cache() {
			global $wpdb, $blog_id;

			session_cache_limiter( '' );
			header( 'Cache-Control: no-store' );
			if ( ! session_id() ) {
				session_start();
			}
		}
		add_action( 'init', 'spam_master_no_cache', 0 );
	}

	// End Valid.
}

