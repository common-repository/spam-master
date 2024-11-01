<?php
/**
 * Menu table WP_List_Table based.
 *
 * @package Spam Master
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Main inactive display class.
 *
 * @since 4.0.1
 */
class SpamMasterAdminTableInactiveController extends WP_List_Table {

	/**
	 * Display function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function display() {
		global $wpdb, $blog_id;

		$plugin_master_name   = constant( 'SPAM_MASTER_NAME' );
		$plugin_master_domain = constant( 'SPAM_MASTER_DOMAIN' );

		if ( isset( $_POST['generate_free_spam_master_license'] ) ) {
			check_admin_referer( 'save-settings_generate_free_spam_master_license' );

			// Spam Collect Controller.
			$spam_master_collect_controller = new SpamMasterCollectController();
			$collect_now                    = true;
			$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

			// Spam User Controller.
			$spam_master_user_controller = new SpamMasterUserController();
			$spaminitial                 = 'table-inactive';
			if ( ! empty( $email ) ) {
				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$spampreemail = wp_strip_all_tags( substr( $email, 0, 256 ) );
				} else {
					$spampreemail = false;
				}
			} else {
				$spampreemail = false;
			}
			$is_user = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

			// Add Table & Load Spam Master Options.
			if ( is_multisite() ) {
				$spam_master_keys             = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
				$spam_master_multisite        = 'YES';
				$spam_master_multisite_number = get_blog_count();
				$spam_master_multisite_joined = substr( $spam_master_multisite . ' - ' . $spam_master_multisite_number, 0, 11 );
				$blog                         = substr( get_blog_option( $blog_id, 'blogname' ), 0, 256 );
				if ( empty( $blog ) ) {
					$blog = 'Wp multi';
				}
				$admin_email = substr( get_blog_option( $blog_id, 'admin_email' ), 0, 128 );
				if ( empty( $admin_email ) ) {
					$admin_email = 'weird-no-email@' . gmdate( 'YmdHis' ) . '.wp';
				}
			} else {
				$spam_master_keys             = $wpdb->prefix . 'spam_master_keys';
				$spam_master_multisite        = 'NO';
				$spam_master_multisite_number = '0';
				$spam_master_multisite_joined = substr( $spam_master_multisite . ' - ' . $spam_master_multisite_number, 0, 11 );
				$blog                         = substr( get_option( 'blogname' ), 0, 256 );
				if ( empty( $blog ) ) {
					$blog = 'Wp single';
				}
				$admin_email = substr( get_option( 'admin_email' ), 0, 128 );
				if ( empty( $admin_email ) ) {
					$admin_email = 'weird-no-email@' . gmdate( 'YmdHis' ) . '.wp';
				}
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_auto_update = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_auto_update'" ), 0, 5 );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_db_protection_hash = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
			// Get Counts.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_buffer_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
			if ( empty( $spam_master_buffer_count ) ) {
				$spam_master_buffer_count = '0';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_white_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'White'" );
			if ( empty( $spam_master_white_count ) ) {
				$spam_master_white_count = '0';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_logs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys}" );
			if ( empty( $spam_master_logs_count ) ) {
				$spam_master_logs_count = '0';
			}
			$spam_master_cron     = 'AUT';
			$platform             = 'WordPress';
			$spam_master_version  = constant( 'SPAM_MASTER_VERSION' );
			$spam_master_type_set = 'FREE';
			$wordpress            = substr( get_bloginfo( 'version' ), 0, 12 );
			// create lic hash.
			$spam_master_lic_hash = substr( md5( uniqid( wp_rand(), true ) ), 0, 64 );
			if ( empty( $spam_master_lic_hash ) ) {
				$spam_master_lic_hash = 'md5-' . gmdate( 'YmdHis' );
			}
			$data_spam  = array( 'spamvalue' => substr( $spam_master_lic_hash, 0, 64 ) );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_license_key',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			$address       = substr( get_site_url(), 0, 360 );
			$data_address  = array( 'spamvalue' => $address );
			$where_address = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_address',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_address, $where_address );
			if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
				$spam_master_server_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ), 0, 48 );
				// if empty ip.
				if ( empty( $spam_master_server_ip ) || '0' === $spam_master_server_ip || '127.0.0.1' === $spam_master_server_ip ) {
					if ( isset( $_SERVER['SERVER_NAME'] ) ) {
						$spam_master_ip_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
						$spam_master_server_ip        = substr( $spam_master_ip_gethostbyname, 0, 48 );
						if ( empty( $spam_master_ip_gethostbyname ) || '0' === $spam_master_ip_gethostbyname ) {
							$spam_master_urlparts  = wp_parse_url( $web_address );
							$spam_master_hostname  = $spam_master_urlparts['host'];
							$spam_master_result    = dns_get_record( $spam_master_hostname, DNS_A );
							$spam_master_server_ip = substr( $spam_master_result[0]['ip'], 0, 48 );
						}
					} else {
						$spam_master_server_ip = 'I 000';
					}
				}
				$spam_master_server_hostname = substr( gethostbyaddr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) ), 0, 256 );
				// if empty host.
				if ( empty( $spam_master_server_hostname ) || '0' === $spam_master_server_hostname || '127.0.0.1' === $spam_master_server_hostname ) {
					if ( isset( $_SERVER['SERVER_NAME'] ) ) {
						$spam_master_ho_gethostbyname = gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) );
						$spam_master_server_hostname  = substr( $spam_master_ho_gethostbyname, 0, 256 );
						if ( empty( $spam_master_ho_gethostbyname ) || '0' === $spam_master_ho_gethostbyname ) {
							$spam_master_urlparts        = wp_parse_url( $web_address );
							$spam_master_hostname        = $spam_master_urlparts['host'];
							$spam_master_result          = dns_get_record( $spam_master_hostname, DNS_A );
							$spam_master_server_hostname = substr( $spam_master_result[0]['ip'], 0, 256 );
						}
					} else {
						$spam_master_server_hostname = 'h 000';
					}
				}
			} else {
				$spam_master_server_ip       = 'I 001';
				$spam_master_server_hostname = 'h 001';
			}
			$data_ip  = array( 'spamvalue' => $spam_master_server_ip );
			$where_ip = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_ip',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_ip, $where_ip );
			$spam_master_alert_level_date_set = gmdate( 'Y-m-d H:i:s' );
			$spam_my_nounce                   = 'PW9pdXNkbmVXMndzUw==';
			// remote post and response.
			$spam_master_license_post = array(
				'spam_license_key'    => $spam_master_lic_hash,
				'spam_trial_nounce'   => $spam_my_nounce,
				'platform'            => $platform,
				'platform_version'    => $wordpress,
				'platform_type'       => $spam_master_multisite_joined,
				'spam_master_version' => $spam_master_version,
				'spam_master_type'    => $spam_master_type_set,
				'blog_name'           => $blog,
				'blog_address'        => $address,
				'blog_email'          => $admin_email,
				'blog_hostname'       => $spam_master_server_hostname,
				'blog_ip'             => $spam_master_server_ip,
				'blog_up'             => $spam_master_auto_update,
				'spam_master_db'      => $spam_master_db_protection_hash,
				'spam_master_buffer'  => $spam_master_buffer_count,
				'spam_master_white'   => $spam_master_white_count,
				'spam_master_logs'    => $spam_master_logs_count,
				'spam_master_cron'    => $spam_master_cron,
			);
			$spam_master_license_url  = 'https://www.spammaster.org/core/lic/lic_gen.php';
			$response                 = wp_remote_post(
				$spam_master_license_url,
				array(
					'method'  => 'POST',
					'timeout' => 90,
					'body'    => $spam_master_license_post,
				)
			);
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo esc_attr( __( 'Something went wrong, please get in touch with Spam master Support: ', 'spam-master' ) . $error_message );
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( empty( $data ) ) {
					$data_spam1  = array( 'spamvalue' => 'EMPTY' );
					$where_spam1 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_type',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
					$data_spam2  = array( 'spamvalue' => 'INACTIVE' );
					$where_spam2 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_status',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
					$data_spam3  = array( 'spamvalue' => '' );
					$where_spam3 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_attached',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
					$data_spam4  = array( 'spamvalue' => '' );
					$where_spam4 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_expires',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam4, $where_spam4 );
					$data_spam5  = array( 'spamvalue' => '0' );
					$where_spam5 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_protection_total_number',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam5, $where_spam5 );
					$data_spam6  = array( 'spamvalue' => '' );
					$where_spam6 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam6, $where_spam6 );
					$data_spam7  = array( 'spamvalue' => '' );
					$where_spam7 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_date',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam7, $where_spam7 );
					$data_spam8  = array( 'spamvalue' => '' );
					$where_spam8 = array(
						'spamkey'  => 'Option',
						'spamtype' => 'spam_master_alert_level_p_text',
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $spam_master_keys, $data_spam8, $where_spam8 );
				} else {
					$spam_master_status = $data['status'];
					if ( 'MALFUNCTION_4' === $spam_master_status ) {
						$data_spam1  = array( 'spamvalue' => 'EMPTY' );
						$where_spam1 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_type',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
						$data_spam2  = array( 'spamvalue' => $spam_master_status );
						$where_spam2 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_status',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
						$data_spam3  = array( 'spamvalue' => '' );
						$where_spam3 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_attached',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
						$data_spam4  = array( 'spamvalue' => '' );
						$where_spam4 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_expires',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam4, $where_spam4 );
						$data_spam5  = array( 'spamvalue' => '0' );
						$where_spam5 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_protection_total_number',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam5, $where_spam5 );
						$data_spam6  = array( 'spamvalue' => '' );
						$where_spam6 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam6, $where_spam6 );
						$data_spam7  = array( 'spamvalue' => '' );
						$where_spam7 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level_date',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam7, $where_spam7 );
						$data_spam8  = array( 'spamvalue' => '' );
						$where_spam8 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level_p_text',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam8, $where_spam8 );
					}
					if ( 'VALID' === $spam_master_status ) {
						$data_spam1  = array( 'spamvalue' => $data['type'] );
						$where_spam1 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_type',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
						$data_spam2  = array( 'spamvalue' => $spam_master_status );
						$where_spam2 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_status',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
						$data_spam3  = array( 'spamvalue' => $data['attached'] );
						$where_spam3 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_attached',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
						$data_spam4  = array( 'spamvalue' => $data['expires'] );
						$where_spam4 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_expires',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam4, $where_spam4 );
						$data_spam5  = array( 'spamvalue' => $data['threats'] );
						$where_spam5 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_protection_total_number',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam5, $where_spam5 );
						$data_spam6  = array( 'spamvalue' => $data['alert'] );
						$where_spam6 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam6, $where_spam6 );
						$data_spam7  = array( 'spamvalue' => $spam_master_alert_level_date_set );
						$where_spam7 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level_date',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam7, $where_spam7 );
						$data_spam8  = array( 'spamvalue' => $data['percent'] );
						$where_spam8 = array(
							'spamkey'  => 'Option',
							'spamtype' => 'spam_master_alert_level_p_text',
						);
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
						$wpdb->update( $spam_master_keys, $data_spam8, $where_spam8 );

						// Spam Email Controller.
						$spammail                     = true;
						$spam_master_email_controller = new SpamMasterEmailController();
						$is_deact                     = $spam_master_email_controller->spammasterautofree( $spammail );

						// Log InUp Controller.
						$spamtype                   = 'Key Inactive';
						$spamvalue                  = 'Successfully run with status: ' . $data['status'];
						$cache                      = '4H';
						$spam_master_log_controller = new SpamMasterLogController();
						$is_log                     = $spam_master_log_controller->spammasterlog( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype, $spamvalue, $cache );

						$spama = $data['a'];
						if ( '1' === $spama ) {
							// Spam Action Controller.
							$spam_master_action_controller = new SpamMasterActionController();
							$is_action                     = $spam_master_action_controller->spammasteract( $spama );
						}
						?>
<div id="message" class="updated fade">
<p><?php echo esc_attr( __( 'Congratulations! Automatic Key Generated. Please wait refreshing...', 'spam-master' ) ); ?></p>
</div>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '<META HTTP-EQUIV="REFRESH" CONTENT="3">';
					}
				}
			}
		}
		?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3"><img src="<?php echo esc_url( plugins_url( '../images/spammaster-wp-plugin-internal-banner.jpg', dirname( __FILE__ ) ) ); ?>" alt="<?php echo esc_attr( $plugin_master_name ); ?>" align="left" width="100%" /></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class="spam-master-text-center" colspan="3">
<h2><?php echo esc_attr( __( 'Spam Master munches, feeds and grows on spam ip’s, emails, domains and words. Join one of the top 5 world-wide, real-time online spam checking databases', 'spam-master' ) ); ?> <a href="https://www.spammaster.org" target="_blank" title="<?php echo esc_attr( $plugin_master_domain ); ?>"><?php echo esc_attr( $plugin_master_domain ); ?></a>.</h2>
			</td>
		</tr>
		<tr class="alternate">
			<td></td>
			<td class="spam-master-text-jcenter">
				<div>
					<div style="display: inline-block;">
						<form method="post" id="generate_free_spam_master_license" width="1">
							<fieldset class="options">
								<?php $sec_nonce = wp_nonce_field( 'save-settings_generate_free_spam_master_license' ); ?>
								<div class="spam-master-card spam-master-free-card">
									<div class="spam-master-overlay"></div>
									<div class="spam-master-circle">
										<span class="dashicons dashicons-database spam-master-admin-f70y"></span>
									</div>
									<p><?php echo esc_attr( __( 'Free Server Cluster', 'spam-master' ) ); ?></p>
									<p><?php echo esc_attr( __( 'Free RBL Server Connection', 'spam-master' ) ); ?></p>
									<p><?php echo esc_attr( __( 'Auto Generates Key', 'spam-master' ) ); ?></p>
									<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Full Functionality', 'spam-master' ) ); ?></p>
									<p><button type="submit" name="generate_free_spam_master_license" id="generate_free_spam_master_license" href="#" class="btn-spammaster orange roundedspam"><?php echo esc_attr( __( 'Generate Key', 'spam-master' ) ); ?></button></p>
								</div>
							</fieldset>
						</form>
					</div>
					<div style="display: inline-block;">
						<div class="spam-master-card spam-master-pro-card">
							<div class="spam-master-overlay"></div>
							<div class="spam-master-circle">
								<span class="dashicons dashicons-database-add spam-master-admin-f70g"></span>
							</div>
							<p><?php echo esc_attr( __( 'Business Server Cluster', 'spam-master' ) ); ?></p>
							<p><?php echo esc_attr( __( 'Premium RBL Server Connection', 'spam-master' ) ); ?></p>
							<p><?php echo esc_attr( __( '24/7 Support', 'spam-master' ) ); ?></p>
							<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Full Functionality', 'spam-master' ) ); ?></p>
							<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" class="btn-spammaster green roundedspam"><?php echo esc_attr( __( 'Buy Pro Key', 'spam-master' ) ); ?></a></p>
						</div>
					</div>
				</div>
			</td>
			<td></td>
		</tr>
	</tbody>
</table>
<div class="spam-master-pad-table"></div>
		<?php
	}
}
