<?php
/**
 * Collect controller
 *
 * @package Spam Master
 */

/**
 * Main collect class.
 *
 * @since 6.0.0
 */
class SpamMasterCollectController {

	/**
	 * Variable collect_now.
	 *
	 * @var collect_now $collect_now
	 **/
	protected $collect_now;

	/**
	 * Variable spam_master_block_count.
	 *
	 * @var spam_master_block_count $spam_master_block_count
	 **/
	protected $spam_master_block_count;

	/**
	 * Spam master get collect.
	 *
	 * @param collect_now $collect_now for collection.
	 *
	 * @return array
	 */
	public function spammastergetcollect( $collect_now ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );

		// Remote Ip.
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ), 0, 48 );
		} else {
			$remote_ip = 'I 666';
		}
		// Remote Agent.
		if ( '1' === $spam_master_firewall_rules ) {
			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$remote_agent = substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 360 );
			} else {
				$remote_agent = 'Sniffer';
			}
		} else {
			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$remote_agent = substr( 'Relaxed - ' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 360 );
			} else {
				$remote_agent = 'Relaxed - Sniffer';
			}
		}
		// Remote Referer.
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$remote_referer = substr( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 0, 360 );
		} else {
			$remote_referer = 'Direct';
		}
		// DEST URL.
		if ( isset( $_SERVER['REQUEST_SCHEME'] ) ) {
			$spam_request_scheme = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_SCHEME'] ) );
		} else {
			$spam_request_scheme = 'https';
		}
		if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$spam_server_name = sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
		} else {
			$spam_server_name = 'false';
		}
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$spam_request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		} else {
			$spam_request_uri = '/false';
		}
		$dest_url = substr( $spam_request_scheme . '://' . $spam_server_name . $spam_request_uri, 0, 360 );

		return array(
			'remote_ip'      => $remote_ip,
			'remote_agent'   => $remote_agent,
			'remote_referer' => $remote_referer,
			'dest_url'       => $dest_url,
		);
	}

	/**
	 * Spam master get number.
	 *
	 * @param spam_master_block_count $spam_master_block_count for display.
	 *
	 * @return string
	 */
	public function spammastergetnumber( $spam_master_block_count ) {
		if ( empty( $spam_master_block_count ) || '0' === $spam_master_block_count ) {
			return false;
		} else {
			if ( $spam_master_block_count >= 1 && $spam_master_block_count <= 99 ) {
				return ' <span class="spam-master-admin-red"><strong>' . __( 'Firewall Triggers: ', 'spam-master' ) . $spam_master_block_count . ' dangerous blocks.</strong></span>';
			}
			if ( $spam_master_block_count >= 100 && $spam_master_block_count <= 999 ) {
				return ' <span class="spam-master-admin-red"><strong>' . __( 'Firewall Triggers: ', 'spam-master' ) . $spam_master_block_count . ' dangerous blocks.</strong></span>';
			}
			if ( $spam_master_block_count >= 1000 && $spam_master_block_count <= 999999 ) {
				return ' <span class="spam-master-admin-red"><strong>' . __( 'Firewall Triggers: ', 'spam-master' ) . $spam_master_block_count . ' dangerous blocks.</strong></span>';
			}
			if ( $spam_master_block_count >= 1000000 && $spam_master_block_count <= 999999999 ) {
				return ' <span class="spam-master-admin-red"><strong>' . __( 'Firewall Triggers: ', 'spam-master' ) . $spam_master_block_count . ' dangerous blocks.</strong></span>';
			}
			if ( $spam_master_block_count > 1000000000 ) {
				return ' <span class="spam-master-admin-red"><strong>' . __( 'Firewall Triggers: ', 'spam-master' ) . $spam_master_block_count . ' dangerous blocks.</strong></span>';
			}
		}
	}
}

