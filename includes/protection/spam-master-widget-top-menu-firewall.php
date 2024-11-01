<?php
/**
 * Load firewall menu.
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
$spam_master_widget_top_menu_firewall = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_top_menu_firewall'" );

if ( 'true' === $spam_master_widget_top_menu_firewall ) {

	if ( is_network_admin() ) {
		$notdoneyet = true;
	} else {

		/**
		 * Spam master top menu.
		 */
		function spam_master_menu() {
			global $wpdb, $blog_id, $wp_admin_bar;

			if ( current_user_can( 'manage_options' ) ) {
				// Add Table & Load Spam Master Options.
				if ( is_multisite() ) {
					$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
					$admin_email      = get_blog_option( $blog_id, 'admin_email' );
				} else {
					$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
					$admin_email      = get_option( 'admin_email' );
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_buffer_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
				if ( $spam_master_buffer_count <= '10' ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
					if ( $spam_master_block_count <= '10' ) {
						$spam_master_count = '';
					} else {
						$spam_master_count = ' : <span class="spam-master-top-admin-bar-bubble"><span>' . number_format( $spam_master_buffer_count ) . '</span></span>';
					}
				} else {
					$spam_master_count = ' : <span class="spam-master-top-admin-bar-bubble"><span>' . number_format( $spam_master_buffer_count ) . '</span></span>';
				}
				$techgasp_plugin_url = admin_url( 'options-general.php?page=spam-master' );
				if ( 'INACTIVE' === $spam_master_status ) {
					$response_message            = __( 'Please visit the plugin settings page.', 'spam_master' );
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-warning spam-master-dash-f24 spam-master-top-admin-shadow-yellow" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-yellow';
				}
				if ( 'UNSTABLE' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-warning spam-master-dash-f24 spam-master-top-admin-shadow-orange" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-yellow';
				}
				if ( 'VALID' === $spam_master_status ) {
					$response_message            = __( 'Protected', 'spam_master' );
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-yes-alt spam-master-dash-f24 spam-master-top-admin-shadow-green" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-green';
				}
				if ( 'MALFUNCTION_1' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-warning spam-master-dash-f24 spam-master-top-admin-shadow-orange" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orange';
				}
				if ( 'MALFUNCTION_2' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-warning spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'MALFUNCTION_3' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-red" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-red';
				}
				if ( 'MALFUNCTION_4' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'MALFUNCTION_5' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'MALFUNCTION_6' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'MALFUNCTION_7' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'MALFUNCTION_8' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-orangina" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-orangina';
				}
				if ( 'DISCONNECTED' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-yes-alt spam-master-dash-f24 spam-master-top-admin-shadow-green" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-green';
				}
				if ( 'EXPIRED' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-red" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-red';
				}
				if ( 'UNSTABLE' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-red" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-red';
				}
				if ( 'HIGH_VOLUME' === $spam_master_status ) {
					$response_message            = $spam_master_status;
					$plugin_master_status_icon   = '<span class="ab-icon"><span class="dashicons-before dashicons-dismiss spam-master-dash-f24 spam-master-top-admin-shadow-red" title="' . $response_message . '"></span></span>';
					$plugin_master_status_shadow = 'spam-master-top-admin-shadow-red';
				}

				$plugin_master_name = '<span class="ab-label"><span class="' . $plugin_master_status_shadow . '">' . constant( 'SPAM_MASTER_NAME' ) . '</span>' . $spam_master_count . '</span>';
				$menu_id            = 'spam_master';

				$wp_admin_bar->add_menu(
					array(
						'id'    => $menu_id,
						'title' => $plugin_master_status_icon . ' ' . $plugin_master_name,
						'href'  => $techgasp_plugin_url,
						'meta'  => array( 'title' => $response_message ),
					)
				);
			}
		}
		add_action( 'admin_bar_menu', 'spam_master_menu', 2000 );
	}
}

