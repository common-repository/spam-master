<?php
/**
 * Protection tools tables.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
$plugin_master_name   = constant( 'SPAM_MASTER_NAME' );
$plugin_master_domain = constant( 'SPAM_MASTER_DOMAIN' );

// Add Table & Load Spam Master Options.
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_firewall_rules_set = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules_set'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_firewall_rules = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_rules'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_is_cloudflare = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_is_cloudflare'" ), 0, 5 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_message = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_message'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_honeypot_timetrap_speed = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap_speed'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_cache_proxie = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cache_proxie'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_auto_update = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_amp_check_fun = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_amp_check_fun'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_comment_strict_on = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_comment_strict_on'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_comments_clean = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_comments_clean'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_alert_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_alert_email'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_weekly_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_email'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_weekly_stats = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_stats'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_emails_alert_3_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_alert_3_email'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_heads_up = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_heads_up'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_statistics = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_statistics'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_firewall = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_firewall'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_dashboard_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_dashboard_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_dashboard_statistics = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_dashboard_statistics'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_widget_top_menu_firewall = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_widget_top_menu_firewall'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_shortcodes_total_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_shortcodes_total_count'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_signature = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_signature'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_is_cloudflare = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_is_cloudflare'" );

// Test light firewall.
if ( isset( $_POST['test_light_firewall'] ) ) {
	// Spam Collect Controller.
	$spam_master_collect_controller = new SpamMasterCollectController();
	$collect_now                    = true;
	$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

	$selected_allowed = array(
		'pre'    => array(),
		'strong' => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
		),
		'br'     => array(),
	);
	$spam_die_message = '<pre>Test Start...</pre><br><pre>' . __( '403 Forbidden', 'spam-master' ) . '</pre><pre>' . __( 'IP: ', 'spam-master' ) . $is_collected['remote_ip'] . '</pre><pre>' . __( 'Browser: ', 'spam-master' ) . $is_collected['remote_agent'] . '</pre><pre>' . __( 'Protected by ', 'spam-master' ) . '<a href="https://www.spammaster.org/contact/" target="_self>' . __( 'Spam Master', 'spam-master' ) . '</a></pre><br><pre>Test Status: Success. You are not blocked, a successful test should display your current IP and browser agent.</pre>';
	wp_die( wp_kses( $spam_die_message, $selected_allowed ), 'Firewall', array( 'response' => '403' ) );
}
// Update firewall tools.
if ( isset( $_POST['update_spam_master_tools_firewall'] ) ) {

	check_admin_referer( 'nonce_spam_master_tools_firewall' );

	if ( ! empty( $_POST['spam_master_message'] ) ) {
		$spam_master_message = sanitize_text_field( wp_unslash( $_POST['spam_master_message'] ) );
		$data_address        = array( 'spamvalue' => $spam_master_message );
		$where_address       = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_message',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	} else {
		?>
		<div class="notice notice-error is-dismissible">
		<p><?php echo esc_attr( __( 'ERROR: saving Spam Master Message in Firewall Options.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	}
	if ( 'FULL' === $spam_master_type ) {
		if ( ! empty( $_POST['spam_master_firewall_rules'] ) ) {
			$spam_master_firewall_rules = sanitize_text_field( wp_unslash( $_POST['spam_master_firewall_rules'] ) );
			$data_address               = array( 'spamvalue' => $spam_master_firewall_rules );
			$where_address              = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_firewall_rules',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_address, $where_address );
		}
	} else {
		if ( '0' === $spam_master_firewall_rules_set ) {
			if ( ! empty( $_POST['spam_master_firewall_rules'] ) ) {
				$spam_master_firewall_rules = sanitize_text_field( wp_unslash( $_POST['spam_master_firewall_rules'] ) );
				$data_address               = array( 'spamvalue' => $spam_master_firewall_rules );
				$where_address              = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_firewall_rules',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_address, $where_address );
			}
		} else {
			if ( ! empty( $_POST['spam_master_firewall_rules'] ) ) {
				$spam_master_firewall_rules = sanitize_text_field( wp_unslash( $_POST['spam_master_firewall_rules'] ) );
				$data_address               = array( 'spamvalue' => '3' );
				$where_address              = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_firewall_rules',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_address, $where_address );
			}
		}
	}
	?>
	<div class="notice notice-success is-dismissible">
	<p><?php echo esc_attr( __( 'Firewall options saved.', 'spam-master' ) ); ?></p>
	</div>
	<?php
}
// Update integration api.
if ( isset( $_POST['update_spam_master_integration_api'] ) ) {
	check_admin_referer( 'nonce_spam_master_integration_api' );

	if ( ! empty( $_POST['spam_master_honeypot_timetrap_speed'] ) ) {
		$spam_master_honeypot_timetrap_speed = sanitize_text_field( wp_unslash( $_POST['spam_master_honeypot_timetrap_speed'] ) );
		$spam_error                          = false;
		if ( is_numeric( $spam_master_honeypot_timetrap_speed ) && '0' !== $spam_master_honeypot_timetrap_speed && $spam_master_honeypot_timetrap_speed < '10' ) {
			$data_address  = array( 'spamvalue' => $spam_master_honeypot_timetrap_speed );
			$where_address = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_honeypot_timetrap_speed',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_address, $where_address );
		} else {
			$spam_error = 'ERROR: Integration API Time trap Speed needs to be between 1 and 10.';
		}
	}
	if ( ! empty( $_POST['spam_master_cache_proxie'] ) ) {
		$spam_master_cache_proxie = sanitize_text_field( wp_unslash( $_POST['spam_master_cache_proxie'] ) );
		$data_address             = array( 'spamvalue' => $spam_master_cache_proxie );
		$where_address            = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_cache_proxie',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_auto_update'] ) ) {
		$spam_master_cache_proxie = sanitize_text_field( wp_unslash( $_POST['spam_master_auto_update'] ) );
		$data_address             = array( 'spamvalue' => 'true' );
		$where_address            = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_auto_update',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_amp_check_fun'] ) ) {
		$spam_master_amp_check_fun = sanitize_text_field( wp_unslash( $_POST['spam_master_amp_check_fun'] ) );
		$data_address              = array( 'spamvalue' => $spam_master_amp_check_fun );
		$where_address             = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_amp_check_fun',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( empty( $spam_error ) ) {
		?>
		<div class="notice notice-success is-dismissible">
		<p><?php echo esc_attr( __( 'Integration API options saved.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	} else {
		?>
		<div class="notice notice-error is-dismissible">
		<p><?php echo esc_attr( $spam_error ); ?></p>
		</div>
		<?php
	}
}
// Update WordPress comments.
if ( isset( $_POST['update_spam_master_wp_comments'] ) ) {
	check_admin_referer( 'nonce_spam_master_wp_comments' );

	if ( ! empty( $_POST['spam_master_comment_strict_on'] ) ) {
		$spam_master_comment_strict_on = sanitize_text_field( wp_unslash( $_POST['spam_master_comment_strict_on'] ) );
		$data_address                  = array( 'spamvalue' => $spam_master_comment_strict_on );
		$where_address                 = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_comment_strict_on',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_comments_clean'] ) ) {
		$spam_master_comments_clean = sanitize_text_field( wp_unslash( $_POST['spam_master_comments_clean'] ) );
		$data_address               = array( 'spamvalue' => $spam_master_comments_clean );
		$where_address              = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_comments_clean',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	?>
	<div class="notice notice-success is-dismissible">
	<p><?php echo esc_attr( __( 'Comment options saved.', 'spam-master' ) ); ?></p>
	</div>
	<?php
}
// Update emails and reporting.
if ( isset( $_POST['update_spam_master_emails'] ) ) {
	check_admin_referer( 'nonce_spam_master_emails' );

	if ( ! empty( $_POST['spam_master_emails_extra_email'] ) ) {
		$spam_master_emails_extra_email = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_extra_email'] ) );
		$data_address                   = array( 'spamvalue' => $spam_master_emails_extra_email );
		$where_address                  = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_extra_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_emails_extra_email_list'] ) ) {
		$spam_master_emails_extra_email_list = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_extra_email_list'] ) );
		$data_address                        = array( 'spamvalue' => $spam_master_emails_extra_email_list );
		$where_address                       = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_extra_email_list',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	} else {
		$spam_master_emails_extra_email_list = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_extra_email_list'] ) );
		$data_address                        = array( 'spamvalue' => false );
		$where_address                       = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_extra_email_list',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_emails_alert_3_email'] ) ) {
		$spam_master_emails_alert_3_email = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_alert_3_email'] ) );
		$data_address                     = array( 'spamvalue' => 'true' );
		$where_address                    = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_alert_3_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_emails_alert_email'] ) ) {
		$spam_master_emails_alert_email = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_alert_email'] ) );
		$data_address                   = array( 'spamvalue' => $spam_master_emails_alert_email );
		$where_address                  = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_alert_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_emails_weekly_email'] ) ) {
		$spam_master_emails_weekly_email = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_weekly_email'] ) );
		$data_address                    = array( 'spamvalue' => $spam_master_emails_weekly_email );
		$where_address                   = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_weekly_email',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_emails_weekly_stats'] ) ) {
		$spam_master_emails_weekly_stats = sanitize_text_field( wp_unslash( $_POST['spam_master_emails_weekly_stats'] ) );
		$data_address                    = array( 'spamvalue' => $spam_master_emails_weekly_stats );
		$where_address                   = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_emails_weekly_stats',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	?>
	<div class="notice notice-success is-dismissible">
	<p><?php echo esc_attr( __( 'Emails & reporting options saved.', 'spam-master' ) ); ?></p>
	</div>
	<?php
}
// Update widgets.
if ( isset( $_POST['update_spam_master_widgets'] ) ) {
	check_admin_referer( 'nonce_spam_master_widgets' );

	if ( ! empty( $_POST['spam_master_widget_heads_up'] ) ) {
		$spam_master_widget_heads_up = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_heads_up'] ) );
		$data_address                = array( 'spamvalue' => $spam_master_widget_heads_up );
		$where_address               = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_heads_up',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_widget_statistics'] ) ) {
		$spam_master_widget_statistics = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_statistics'] ) );
		$data_address                  = array( 'spamvalue' => $spam_master_widget_statistics );
		$where_address                 = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_statistics',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_widget_firewall'] ) ) {
		$spam_master_widget_firewall = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_firewall'] ) );
		$data_address                = array( 'spamvalue' => $spam_master_widget_firewall );
		$where_address               = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_firewall',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_widget_dashboard_status'] ) ) {
		$spam_master_widget_dashboard_status = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_dashboard_status'] ) );
		$data_address                        = array( 'spamvalue' => $spam_master_widget_dashboard_status );
		$where_address                       = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_dashboard_status',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_widget_dashboard_statistics'] ) ) {
		$spam_master_widget_dashboard_statistics = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_dashboard_statistics'] ) );
		$data_address                            = array( 'spamvalue' => $spam_master_widget_dashboard_statistics );
		$where_address                           = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_dashboard_statistics',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	if ( ! empty( $_POST['spam_master_widget_top_menu_firewall'] ) ) {
		$spam_master_widget_top_menu_firewall = sanitize_text_field( wp_unslash( $_POST['spam_master_widget_top_menu_firewall'] ) );
		$data_address                         = array( 'spamvalue' => $spam_master_widget_top_menu_firewall );
		$where_address                        = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_widget_top_menu_firewall',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="1">';
	}
	if ( ! empty( $_POST['spam_master_shortcodes_total_count'] ) ) {
		$spam_master_shortcodes_total_count = sanitize_text_field( wp_unslash( $_POST['spam_master_shortcodes_total_count'] ) );
		$data_address                       = array( 'spamvalue' => $spam_master_shortcodes_total_count );
		$where_address                      = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_shortcodes_total_count',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_address, $where_address );
	}
	?>
	<div class="notice notice-success is-dismissible">
	<p><?php echo esc_attr( __( 'Widgets & shortcodes options saved.', 'spam-master' ) ); ?></p>
	</div>
	<?php
}
// Update signatures.
if ( isset( $_POST['update_spam_master_signatures'] ) ) {
	check_admin_referer( 'nonce_spam_master_signatures' );

	if ( 'FULL' === $spam_master_type ) {
		if ( ! empty( $_POST['spam_master_signature'] ) ) {
			$spam_master_signature = sanitize_text_field( wp_unslash( $_POST['spam_master_signature'] ) );
			$data_address          = array( 'spamvalue' => $spam_master_signature );
			$where_address         = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_signature',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_address, $where_address );
		}
		?>
		<div class="notice notice-success is-dismissible">
		<p><?php echo esc_attr( __( 'Signature options saved.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	} else {
		?>
		<div class="notice notice-error is-dismissible">
		<p><?php echo esc_attr( __( 'ERROR: Signature requires a Pro key.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	}
}
// Update cdn.
if ( isset( $_POST['update_spam_master_cdn'] ) ) {
	check_admin_referer( 'nonce_spam_master_cdn' );

	if ( 'FULL' === $spam_master_type ) {
		if ( ! empty( $_POST['spam_master_is_cloudflare'] ) ) {
			$spam_master_is_cloudflare = sanitize_text_field( wp_unslash( $_POST['spam_master_is_cloudflare'] ) );
			$data_address              = array( 'spamvalue' => $spam_master_is_cloudflare );
			$where_address             = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_is_cloudflare',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_address, $where_address );
		}
		?>
		<div class="notice notice-success is-dismissible">
		<p><?php echo esc_attr( __( 'CDN & WAF options saved.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	} else {
		?>
		<div class="notice notice-error is-dismissible">
		<p><?php echo esc_attr( __( 'ERROR: CDN & WAF requires a Pro key.', 'spam-master' ) ); ?></p>
		</div>
		<?php
	}
}
if ( 'FULL' === $spam_master_type ) {
	$is_full         = false;
	$is_link         = false;
	$is_link_fir_set = false;
} else {
	$is_full          = 'disabled="disabled"';
	$selected_allowed = array(
		'tr'     => array(
			'class' => array(),
		),
		'td'     => array(
			'colspan' => array(),
		),
		'strong' => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
			'class'  => array(),
			'title'  => array(),
		),
		'span'   => array(
			'class' => array(),
		),
		'small'  => array(),
	);
	$is_link          = '<tr class="alternate">
							<td colspan="2">
								<a class="spam-master-admin-red spam-master-top-admin-shadow-offline" href="https://www.techgasp.com/downloads/spam-master-license/" title="1 Year Pro Spam Master Key - Costs Peanuts" target="_blank"><small><strong><span class="dashicons dashicons-admin-links"></span> Requires Pro Key.</strong></small></a>
							</td>
						</tr>';
	if ( '1' === $spam_master_firewall_rules_set ) {
		$is_link_fir_set = '<tr class="alternate">
								<td colspan="2">
									<a class="spam-master-admin-red spam-master-top-admin-shadow-offline" href="https://www.techgasp.com/downloads/spam-master-license/" title="1 Year Pro Spam Master Key - Costs Peanuts" target="_blank"><small><strong><span class="dashicons dashicons-admin-links"></span> Normal and Relaxed require a Pro Key.</strong></small></a>
								</td>
							</tr>';
	} else {
		$is_link_fir_set = false;
	}
}
if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status || 'MALFUNCTION_8' === $spam_master_status ) {
	?>
<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_tools_firewall' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'HAF Firewall', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_tools_firewall" id="update_spam_master_tools_firewall" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Firewall Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Firewall Options', 'spam-master' ) ); ?></button>
				<button type="submit" name="test_light_firewall" id="test_light_firewall" class="btn-spammaster red roundedspam" href="#" title="<?php echo esc_attr( __( 'Test Low Resources Firewall', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Test Low Resources Firewall', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'High Availability Firewall', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_buffer" name="spam_master_buffer">
					<option value="true"><?php echo esc_attr( __( 'On' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'HAF Firewall Scan Level', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_firewall_rules" name="spam_master_firewall_rules">
					<?php
					$select_attribute = '';
					if ( '1' === $spam_master_firewall_rules ) {
						$select_1 = 'selected';
					} else {
						$select_1 = '';
					}
					if ( '2' === $spam_master_firewall_rules ) {
						$select_2 = 'selected';
					} else {
						$select_2 = '';
					}
					if ( '3' === $spam_master_firewall_rules ) {
						$select_3 = 'selected';
					} else {
						$select_3 = '';
					}
					?>
					<option value="1" <?php echo esc_attr( $select_1 ); ?>><?php echo esc_attr( __( 'Normal' ) ); ?></option>
					<option value="2" <?php echo esc_attr( $select_2 ); ?>><?php echo esc_attr( __( 'Relaxed' ) ); ?></option>
					<option value="3" <?php echo esc_attr( $select_3 ); ?>><?php echo esc_attr( __( 'Super Relaxed' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><span class="spam-master-admin-blue spam-master-top-admin-shadow-offline"><span class="dashicons dashicons-info-outline"></span> <?php echo esc_attr( __( 'New:', 'spam-master' ) ); ?></span> <strong><em><?php echo esc_attr( __( 'Normal', 'spam-master' ) ); ?></em></strong><?php echo esc_attr( __( ', active strict firewall stance for high levels of spam.', 'spam-master' ) ); ?> <strong><em><?php echo esc_attr( __( 'Relaxed', 'spam-master' ) ); ?></em></strong><?php echo esc_attr( __( ', active firewall stance for large corporate websites or local, state and federal government agencies.', 'spam-master' ) ); ?> <strong><em><?php echo esc_attr( __( 'Super Relaxed', 'spam-master' ) ); ?></em></strong><?php echo esc_attr( __( ', passive firewall stance with low footprint and for low spam levels.', 'spam-master' ) ); ?></td>
		</tr>
		<?php echo wp_kses( $is_link_fir_set, $selected_allowed ); ?>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Spam Master Buffer', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_buffer" name="spam_master_buffer">
					<option value="true"><?php echo esc_attr( __( 'On' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Spam Master Learning', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_learning" name="spam_master_learning">
					<option value="true"><?php echo esc_attr( __( 'On' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Spam Master Low Resources Firewall', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_low_resources_firewall" name="spam_master_learning">
					<option value="true"><?php echo esc_attr( __( 'On' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Firewall Block Message', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<input class="spam-master-100" id="spam_master_message" name="spam_master_message" placeholder="<?php echo esc_attr( __( ': Email, Domain, or Ip banned.', 'spam-master' ) ); ?>" type="text" value="<?php echo esc_attr( $spam_master_message ); ?>">
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_integration_api' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'Integration API', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_integration_api" id="update_spam_master_integration_api" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Integration API Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Integration API Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'Spam Master is compatible with all plugins and the Integrations API allows the implementation of special protection functions that further extend your website protection checks for threats and spam.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Honeypot & Honey Version 2', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'Activating Honeypot adds invisible traps for "bots" or "robots, "persons" or "humans" will not see these traps.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Honeypot Time Trap', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_honeypot_timetrap" name="spam_master_honeypot_timetrap">
					<option value="true"><?php echo esc_attr( __( 'On' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Honeypot Trap Speed', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<input class="spam-master-100" id="spam_master_honeypot_timetrap_speed" name="spam_master_honeypot_timetrap_speed" placeholder="<?php echo esc_attr( __( 'Number between 1 and 10.', 'spam-master' ) ); ?>" type="text" value="<?php echo esc_attr( $spam_master_honeypot_timetrap_speed ); ?>">
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Cache Control', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'Cache Control:no-cache is specially useful for firewall redirects behind proxies. Default is Off.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Cache Control: no-cache', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_cache_proxie" name="spam_master_cache_proxie">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_cache_proxie ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_cache_proxie ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Spam Master Auto-Updates', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'Uses WordPress automatic updates API to automatically install the latest Spam Master version that may contain important security improvements and fixes. Default is On.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Spam Master Auto-Updates', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_auto_update" name="spam_master_auto_update">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_auto_update ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_auto_update ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'WordPress AMP Projects', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'AMP is a fully responsive web component framework, which means that you can provide AMP experiences for your users on both mobile and desktop devices. Default is Off.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate AMP Check', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_amp_check_fun" name="spam_master_amp_check_fun">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_amp_check_fun ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_amp_check_fun ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_wp_comments' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'WordPress Native Comment Options', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_wp_comments" id="update_spam_master_wp_comments" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Comment Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Comment Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'These are native WordPress Comment options that seem to be overlook. It is highly recommended to have them active if your blog is under spam attack.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Comments Scan', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_comment_strict_on" name="spam_master_comment_strict_on">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_comment_strict_on ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_comment_strict_on ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Comments Clean-Up', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_comments_clean" name="spam_master_comments_clean">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_comments_clean ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_comments_clean ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_emails' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'Emails & Reporting', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_emails" id="update_spam_master_emails" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Emails & Reporting Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Emails & Reporting Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'These are optional settings. Activating Emails & Reporting adds an extra watchful eye over your WordPress website security. All emails and reports are sent to the administrator email address found in your WordPress Settings, General options page. If you want to receive alerts and reports in other email addresses, add your emails below comma-separated. Example: email@myemail.com, email1@myemail.com, other@gmail.com', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate to add more emails comma-separated', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_emails_extra_email" name="spam_master_emails_extra_email">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_emails_extra_email ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_emails_extra_email ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20" nowrap><?php echo esc_attr( __( 'Example: email@myemail.com, email1@myemail.com, other@gmail.com', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<input class="spam-master-100" id="spam_master_emails_extra_email_list" name="spam_master_emails_extra_email_list" placeholder="<?php echo esc_attr( __( 'Activate above and insert comma separated emails.', 'spam-master' ) ); ?>" type="text" value="<?php echo esc_attr( $spam_master_emails_extra_email_list ); ?>">
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Alert Level 3 Warning Email', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_emails_alert_3_email" name="spam_master_emails_alert_3_email">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_emails_alert_3_email ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_emails_alert_3_email ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Daily Report Email', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_emails_alert_email" name="spam_master_emails_alert_email">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_emails_alert_email ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_emails_alert_email ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
			<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Weekly Report Email', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_emails_weekly_email" name="spam_master_emails_weekly_email">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_emails_weekly_email ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_emails_weekly_email ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
			</tr>
			<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Help Us Improve Spam Master', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_emails_weekly_stats" name="spam_master_emails_weekly_stats">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_emails_weekly_stats ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_emails_weekly_stats ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_widgets' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'Widgets & Shortcodes', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_widgets" id="update_spam_master_widgets" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Widgets & Shortcodes Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Widgets & Shortcodes Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'Usually plugins load all their widgets automatically upon install. Experience tells us that many of the Widgets are never used or deployed by users while taking some of the website resources. Here you have full control of which Widgets to use and load, just activate them below.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Widgets', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Heads Up Widget (Visible by Users & Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_heads_up" name="spam_master_widget_heads_up">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_heads_up ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_heads_up ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Statistics Widget (Visible by Users & Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_statistics" name="spam_master_widget_statistics">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_statistics ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_statistics ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Firewall Status Widget (Visible by Users & Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_firewall" name="spam_master_widget_firewall">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_firewall ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_firewall ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Dashboard Widgets', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Dashboard Status Widget (Visible by Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_dashboard_status" name="spam_master_widget_dashboard_status">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_dashboard_status ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_dashboard_status ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Dashboard Statistics Widget (Visible by Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_dashboard_statistics" name="spam_master_widget_dashboard_statistics">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_dashboard_statistics ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_dashboard_statistics ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Top Menu Firewall Widget (Visible by Admins)', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_widget_top_menu_firewall" name="spam_master_widget_top_menu_firewall">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_widget_top_menu_firewall ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_widget_top_menu_firewall ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><h4><?php echo esc_attr( __( 'Shortcodes', 'spam-master' ) ); ?><h4></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Threat Protection Total Count [spam_master_stats_total_count]', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_shortcodes_total_count" name="spam_master_shortcodes_total_count">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_shortcodes_total_count ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_shortcodes_total_count ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_signatures' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'Protected by Spam Master Signatures', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_signatures" id="update_spam_master_signatures" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Signatures Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Signatures Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'This small extra protection tool is a huge deterrent against all forms of human span. Most of the automatic spam bots are already blocked by the licensed RBL Servers and other extra protection tools like Honeypot. Signatures are displayed in the login form, registration form, comments form and emails, i.e. registration email. You can turn them off here.', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate Signatures', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_signature" name="spam_master_signature">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_signature ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_signature ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?> <?php echo esc_attr( $is_full ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?> <?php echo esc_attr( $is_full ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="alternate">
			<td colspan="2"><small><span class="dashicons dashicons-warning spam-master-admin-green spam-master-top-admin-shadow-offline"></span><?php echo esc_attr( __( 'If you find a misplaced signature please', 'spam-master' ) ); ?> <a href="mailto:info@spammaster.org?subject=Plugin support misplaced signature&body=<?php echo esc_attr( $spam_license_key ); ?> *** WRITE BELOW THIS LINE AND INSERT URL(s) OF MISPLACED SIGNATURE PAGES ***" target="_blank" title="<?php echo esc_attr( $plugin_master_domain ); ?>"><?php echo esc_attr( __( 'email us', 'spam-master' ) ); ?></a> <?php echo esc_attr( __( 'for a speedy fix.', 'spam-master' ) ); ?></small></td>
		</tr>
		<?php echo wp_kses( $is_link, $selected_allowed ); ?>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

<form method="post" width='1'>
<fieldset class="options">

	<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_cdn' ); ?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><strong><?php echo esc_html( __( 'CDN & WAF', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th colspan="2">
				<button type="submit" name="update_spam_master_cdn" id="update_spam_master_cdn" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save CDN & WAF Options', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save CDN & WAF Options', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td colspan="2"><?php echo esc_attr( __( 'If you are using Cloudflare or Fastly this setting is usually set to Yes. Before activating please read the online documentation. ', 'spam-master' ) ); ?></td>
		</tr>
		<tr class="alternate">
			<td class="spam-master-middle-20"><?php echo esc_attr( __( 'Activate for CDN and WAF integration', 'spam-master' ) ); ?></td>
			<td class="spam-master-middle">
				<select class="spam-master-100" id="spam_master_is_cloudflare" name="spam_master_is_cloudflare">
					<?php
					$select_attribute = '';
					if ( 'true' === $spam_master_is_cloudflare ) {
						$select_true = 'selected';
					} else {
						$select_true = '';
					}
					if ( 'false' === $spam_master_is_cloudflare ) {
						$select_false = 'selected';
					} else {
						$select_false = '';
					}
					?>
					<option value="true" <?php echo esc_attr( $select_true ); ?> <?php echo esc_attr( $is_full ); ?>><?php echo esc_attr( __( 'On' ) ); ?></option>
					<option value="false" <?php echo esc_attr( $select_false ); ?> <?php echo esc_attr( $is_full ); ?>><?php echo esc_attr( __( 'Off' ) ); ?></option>
				</select>
			</td>
		</tr>
		<?php echo wp_kses( $is_link, $selected_allowed ); ?>
	</tbody>
</table>
</fieldset>
</form>
	<?php
} else {
	?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_html( __( 'Protection Tools', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td><?php echo esc_attr( __( 'Please visit the Settings tab and correct your key issue. ', 'spam-master' ) ); ?></td>
		</tr>
	</tbody>
</table>
	<?php
}
