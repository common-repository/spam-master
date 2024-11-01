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
 * Main admin menu table class.
 *
 * @since 4.0.1
 */
class SpamMasterAdminMenuTableController extends WP_List_Table {

	/**
	 * Display function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function display() {
		global $wpdb, $blog_id;

		$plugin_master_name = constant( 'SPAM_MASTER_NAME' );

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_buffer = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys}" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );

		// Display buffer size or protection total number.
		if ( $spam_master_block_count >= $spam_master_buffer ) {
			$protection_text       = 'Spam Master protected you with <strong><span class="spam-master-admin-red spam-master-top-admin-shadow-offline">' . number_format( $spam_master_block_count ) . '</span></strong> dangerous blocks.';
			$protection_text_small = 'Firewall Triggers <strong><span class="spam-master-admin-red spam-master-top-admin-shadow-offline">' . number_format( $spam_master_block_count ) . '</span></strong> dangerous blocks';
		} else {
			$protection_text       = 'Spam Master buffer contains <strong><span class="spam-master-admin-red spam-master-top-admin-shadow-offline">' . number_format( $spam_master_buffer ) . '</span></strong> entries.';
			$protection_text_small = 'Firewall Buffer <strong><span class="spam-master-admin-red spam-master-top-admin-shadow-offline">' . number_format( $spam_master_buffer ) . '</span></strong> entries';
		}

		// Prepare Settings bubble.
		$spam_master_about_bubble = false;
		if ( empty( $spam_master_status ) || 'EMPTY' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-yellow" title="Please click Generate Key."><span>1</span></span>';
		}
		if ( empty( $spam_master_status ) || 'INACTIVE' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-yellow" title="Please click Generate Key."><span>1</span></span>';
		}
		if ( 'VALID' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-green" title="Congratulations, your connection is Optimal and your website is protected against millions of threats."><span>0</span></span>';
		}
		if ( 'MALFUNCTION_1' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-orange" title="Please update Spam Master to the latest version."><span>1</span></span>';
		}
		if ( 'MALFUNCTION_2' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-orange" title="Your key is being used in several websites. Please use 1 key per website. Go online to get more keys."><span>1</span></span>';
		}
		if ( 'MALFUNCTION_3' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble" title="Malfunction 3 detected. Please get in touch with Spam Master support."><span>1</span></span>';
		}
		if ( 'MALFUNCTION_4' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-orangina" title="Spam Master was not able to generate a connection key. Not to worry, get a free connection key at www.spammaster.org."><span>1</span></span>';
		}
		if ( 'MALFUNCTION_5' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-orangina" title="Spam Master was not able to generate a connection key because the daily limit of free keys was exceeded. Please try again tomorrow or get pro key."><span>1</span></span>';
		}
		if ( 'MALFUNCTION_6' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble spam-master-top-admin-orangina" title="This Key is assign to another website please go to spammaster.org to verify your keys and or add a new key."><span>1</span></span>';
		}
		if ( 'DISCONNECTED' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble"><span>1</span></span>';
		}
		if ( 'EXPIRED' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble" title="Your key is expired, please renew or get a new key at www.spammaster.org."><span>1</span></span>';
		}
		if ( 'UNSTABLE' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble" title="Spam Master free service is unstable, we apologize for that. Please check the RBL service status at www.spammaster.org."><span>1</span></span>';
		}
		if ( 'HIGH_VOLUME' === $spam_master_status ) {
			$spam_master_settings_bubble = '<span class="spam-master-top-admin-bar-bubble" title="Spam Master detected an High Volume of spam checks from your website using a free key. Please consider a Pro Connection Key."><span>1</span></span>';
		}

		// Get menu.
		if ( isset( $_GET['sm'] ) ) {
			$selected_menu = wp_kses_post( wp_unslash( $_GET['sm'] ) );
		} else {
			$selected_menu = 'settings';
		}
		// Prepare menu.
		if ( 'settings' === $selected_menu ) {
			$selected_menu_bold_sett = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Settings', 'spam-master' ) . '</span></strong>';
			$selected_active_sett    = 'active';
			$spam_nonce              = wp_create_nonce( 'spam-master-options-settings' );
		} else {
			$selected_menu_bold_sett = __( 'Settings', 'spam-master' );
			$selected_active_sett    = false;
		}
		if ( 'tools' === $selected_menu ) {
			$selected_menu_bold_tools = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Protection Tools', 'spam-master' ) . '</span></strong>';
			$selected_active_tools    = 'active';
			$spam_nonce               = wp_create_nonce( 'spam-master-options-tools' );
		} else {
			$selected_menu_bold_tools = __( 'Protection Tools', 'spam-master' );
			$selected_active_tools    = false;
		}
		if ( 'buffer' === $selected_menu ) {
			$selected_menu_bold_buffer = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Buffer', 'spam-master' ) . '</span></strong>';
			$selected_active_buffer    = 'active';
			$spam_nonce                = wp_create_nonce( 'spam-master-options-buffer' );
		} else {
			$selected_menu_bold_buffer = __( 'Buffer', 'spam-master' );
			$selected_active_buffer    = false;
		}
		if ( 'white' === $selected_menu ) {
			$selected_menu_bold_white = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Whitelist', 'spam-master' ) . '</span></strong>';
			$selected_active_white    = 'active';
			$spam_nonce               = wp_create_nonce( 'spam-master-options-white' );
		} else {
			$selected_menu_bold_white = __( 'Whitelist', 'spam-master' );
			$selected_active_white    = false;
		}
		if ( 'logs' === $selected_menu ) {
			$selected_menu_bold_logs = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Firewall Logs', 'spam-master' ) . '</span></strong>';
			$selected_active_logs    = 'active';
			$spam_nonce              = wp_create_nonce( 'spam-master-options-logs' );
		} else {
			$selected_menu_bold_logs = __( 'Firewall Logs', 'spam-master' );
			$selected_active_logs    = false;
		}
		if ( 'help' === $selected_menu ) {
			$selected_menu_bold_help = '<strong><span class="spam-master-top-admin-shadow-offline">' . __( 'Help', 'spam-master' ) . '</span></strong>';
			$selected_active_help    = 'active';
			$spam_nonce              = wp_create_nonce( 'spam-master-options-help' );
		} else {
			$selected_menu_bold_help = __( 'Help', 'spam-master' );
			$selected_active_help    = false;
		}
		$selected_menu_bold_docs = '<span class="dashicons dashicons-editor-help spam-master-admin-blue"></span> <strong><span class="spam-master-top-admin-shadow-green">' . __( 'Docs', 'spam-master' ) . '</span></strong>';
		if ( 'INACTIVE' === $spam_master_status ) {
			if ( ! class_exists( 'SpamMasterAdminTableInactiveController' ) ) {
				require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintableinactivecontroller.php';
			}
			// Prepare Table of elements.
			$wp_list_table = new SpamMasterAdminTableInactiveController();
			// Table of elements.
			$wp_list_table->display();

		} else {
			$selected_allowed = array(
				'span'   => array(
					'class' => array(),
				),
				'strong' => array(),
			);
			?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">

	<tbody>
		<tr class="spam-master-menu-table-bk">
			<td>
				<div class=" spam-master-menu-table">
					<a class="tabmenu <?php echo esc_attr( $selected_active_sett ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=settings" ><?php echo wp_kses( $selected_menu_bold_sett, $selected_allowed ); ?></a>
					<a class="tabmenu <?php echo esc_attr( $selected_active_tools ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=tools" ><?php echo wp_kses( $selected_menu_bold_tools, $selected_allowed ); ?></a>
					<a class="tabmenu <?php echo esc_attr( $selected_active_buffer ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=buffer" ><?php echo wp_kses( $selected_menu_bold_buffer, $selected_allowed ); ?></a>
					<a class="tabmenu <?php echo esc_attr( $selected_active_white ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=white" ><?php echo wp_kses( $selected_menu_bold_white, $selected_allowed ); ?></a>
					<a class="tabmenu <?php echo esc_attr( $selected_active_logs ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=logs" ><?php echo wp_kses( $selected_menu_bold_logs, $selected_allowed ); ?></a>
					<a class="tabmenu <?php echo esc_attr( $selected_active_help ); ?>" href="<?php echo esc_url( admin_url() ); ?>options-general.php?page=spam-master.php&sm=help" ><?php echo wp_kses( $selected_menu_bold_help, $selected_allowed ); ?></a>
					<a class="tabmenu spam-master-admin-float-r" href="https://www.spammaster.org/documentation/" target="_blank" ><?php echo wp_kses( $selected_menu_bold_docs, $selected_allowed ); ?></a>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>

			<?php
			if ( 'settings' === $selected_menu ) {
				wp_verify_nonce( $spam_nonce, 'spam-master-options-settings' );
				?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<tbody>
		<tr class="spam-master-menu-table-bk">
			<td>
				<div class="spam-master-menu-table spam-master-center">
					<p><span class="dashicons dashicons-heart spam-master-admin-f48 spam-master-admin-red spam-master-top-admin-shadow-offline spam-master-middle"></span> <span class="spam-master-middle"><?php echo esc_attr( __( 'Spam Master real-time firewall scanning is ', 'spam-master' ) ); ?> <strong><span class="spam-master-top-admin-shadow-green"><?php echo esc_attr( __( 'On, ', 'spam-master' ) ); ?></span></strong> <?php echo esc_attr( __( 'you are protected against ', 'spam-master' ) ); ?><strong><span class="spam-master-top-admin-shadow-offline"><?php echo esc_attr( number_format( $spam_master_protection_total_number ) ); ?></span></strong> <?php echo esc_attr( __( ' million threats and growing daily.', 'spam-master' ) ); ?><span class="dashicons dashicons-shield spam-master-admin-f48 spam-master-admin-green spam-master-top-admin-shadow-orangina spam-master-middle"></span> <?php echo $protection_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> </span></p>
				</div>
			</td>
		</tr>
		<tr class="spam-master-menu-table-bk">
			<td>
				<div class="spam-master-menu-table">
					<?php echo esc_attr( __( 'Protection:', 'spam-master' ) ); ?> 
					<span class="dashicons dashicons-yes-alt spam-master-admin-green spam-master-top-admin-shadow-offline" title="Spam Master Info"></span> <?php echo esc_attr( __( 'Registration Forms', 'spam-master' ) ); ?> 
					<span class="dashicons dashicons-yes-alt spam-master-admin-green spam-master-top-admin-shadow-offline" title="Spam Master Info"></span> <?php echo esc_attr( __( 'Login Forms', 'spam-master' ) ); ?> 
					<span class="dashicons dashicons-yes-alt spam-master-admin-green spam-master-top-admin-shadow-offline" title="Spam Master Info"></span> <?php echo esc_attr( __( 'Comment Forms', 'spam-master' ) ); ?> 
					<span class="dashicons dashicons-yes-alt spam-master-admin-green spam-master-top-admin-shadow-offline" title="Spam Master Info"></span> <?php echo esc_attr( __( 'Contact Forms', 'spam-master' ) ); ?>
					<span class="dashicons dashicons-yes-alt spam-master-admin-green spam-master-top-admin-shadow-offline" title="Spam Master Info"></span> <?php echo esc_attr( __( 'E-commerce Forms', 'spam-master' ) ); ?>.
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>

				<?php
				// Load status table.
				require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-status-table.php';
			}
			if ( 'tools' === $selected_menu ) {
				wp_verify_nonce( $spam_nonce, 'spam-master-options-tools' );
				// Load tools table.
				require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-tools-table.php';
			}

			if ( 'white' === $selected_menu ) {
				// Process data insertion.
				if ( isset( $_POST['insert_spam_master_white'] ) ) {

					check_admin_referer( 'nonce_spam_master_whitelist' );

					if ( ! empty( $_POST['spam_master_white'] ) ) {
						$spam_master_white = sanitize_text_field( wp_unslash( $_POST['spam_master_white'] ) );
						// Needs ip and email validation.
						if ( filter_var( $spam_master_white, FILTER_VALIDATE_IP ) || filter_var( $spam_master_white, FILTER_VALIDATE_EMAIL ) ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$spam_master_white_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamtype = 'Cache' AND spamy = %s", $spam_master_white ) );
							if ( ! isset( $spam_master_white_exists ) ) {
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
								$wpdb->insert(
									$spam_master_keys,
									array(
										'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
										'spamkey'   => 'White',
										'spamtype'  => 'Cache',
										'spamy'     => $spam_master_white,
										'spamvalue' => '12M',
									)
								);
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$spam_master_buffer_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'Buffer' AND spamtype = 'Cache' AND spamy = %s", $spam_master_white ) );
								if ( isset( $spam_master_buffer_exists ) ) {
									// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
									$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE id = %s", $spam_master_buffer_exists ) );
									$spam_master_is_buffer = 'YES';
								} else {
									$spam_master_is_buffer = 'NO';
								}
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$spam_master_db_protection_hash = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
								$spam_master_report_buffer      = array(
									'spam_license_key'  => $spam_license_key,
									'spam_master_db'    => $spam_master_db_protection_hash,
									'spam_master_white' => $spam_master_white,
									'spam_master_is_buffer' => $spam_master_is_buffer,
								);
								$spam_master_white_url          = 'https://www.spammaster.org/core/white/get_white.php';
								$response                       = wp_remote_post(
									$spam_master_white_url,
									array(
										'method'  => 'POST',
										'timeout' => 90,
										'body'    => $spam_master_report_buffer,
									)
								);
								?>
								<div class="notice notice-success is-dismissible">
								<p><?php echo esc_attr( __( 'Whitelist saved.', 'spam-master' ) ); ?></p>
								</div>
								<?php
							} else {
								?>
								<div class="notice notice-error is-dismissible">
								<p><?php echo esc_attr( __( 'ERROR: white entry already exists.', 'spam-master' ) ); ?></p>
								</div>
								<?php
							}
						} else {
							?>
							<div class="notice notice-error is-dismissible">
							<p><?php echo esc_attr( __( 'ERROR: could not validate the email nor validate the ip.', 'spam-master' ) ); ?></p>
							</div>
							<?php
						}
					} else {
						?>
						<div class="notice notice-error is-dismissible">
						<p><?php echo esc_attr( __( 'ERROR: empty, please write an email or ip.', 'spam-master' ) ); ?></p>
						</div>
						<?php
					}
				}
				// Page header with count.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_total_white = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'White'" );
				?>
<form method="post" width='1'>
<fieldset class="options">
				<?php $sec_nonce = wp_nonce_field( 'nonce_spam_master_whitelist' ); ?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_attr( __( 'Whitelist Size:', 'spam-master' ) ); ?> <span class="spam-master-admin-green spam-master-top-admin-shadow-offline"><?php echo esc_attr( $spam_master_total_white ); ?></span></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th>
				<button type="submit" name="insert_spam_master_white" id="insert_spam_master_white" class="btn-spammaster blue roundedspam" href="#" title="<?php echo esc_attr( __( 'Save Email or Ip', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Save Email or Ip', 'spam-master' ) ); ?></button>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="Info"></span> <?php echo esc_attr( __( 'Spam Buffer Whitelisting excludes spam checks from safe Emails or Ips. Whitelisting also automatically deletes any Spam Buffer entry.', 'spam-master' ) ); ?>
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<input class="spam-master-100" id="spam_master_white" name="spam_master_white" placeholder="<?php echo esc_attr( __( 'Insert frequent users ips or emails to exempt them from spam checks.', 'spam-master' ) ); ?>" type="text" value="">
			</td>
		</tr>
	</tbody>
</table>
</fieldset>
</form>

<div class="spam-master-pad-table"></div>

				<?php
				if ( ( is_user_logged_in() ) && ( current_user_can( 'administrator' ) ) ) {
					if ( isset( $_GET['action'] ) && 'delete-white' === $_GET['action'] ) {
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
						$iddle = wp_unslash( $_GET['iddle'] );
						// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
						$white_ip   = wp_unslash( $_GET['white_ip'] );
						$time_nonce = current_time( 'H' );
						check_admin_referer( "delete-white{$iddle}{$white_ip}{$time_nonce}" );

						// Add Table & Load Spam Master Options.
						if ( is_multisite() ) {
							$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
						} else {
							$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
						}
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$spam_master_white_exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $spam_master_keys WHERE spamkey = 'White' AND spamy = %s", $white_ip ) );
						if ( isset( $spam_master_white_exists ) ) {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
							$wpdb->query( $wpdb->prepare( "DELETE FROM $spam_master_keys WHERE id = %s AND spamkey = 'White'", $spam_master_white_exists ) );
						}
					}
				}

				if ( ! class_exists( 'SpamMasterAdminTableWhiteController' ) ) {
					require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablewhitecontroller.php';
				}
				// Prepare Table of elements.
				$wplisttable = new SpamMasterAdminTableWhiteController();
				$wplisttable->prepare_items();
				if ( isset( $_REQUEST['page'] ) ) {
					$spam_page_full  = admin_url( 'options-general.php?page=' ) . wp_kses_post( wp_unslash( $_REQUEST['page'] ) ) . '&sm=white';
					$spam_page_short = wp_kses_post( wp_unslash( $_REQUEST['page'] ) );
				} else {
					$spam_page_full  = false;
					$spam_page_short = false;
				}
				?>
<form action="<?php echo esc_url( $spam_page_full ); ?>" method="post" name="wplisttable">
<input type="hidden" name="page" value="<?php echo esc_attr( $spam_page_short ); ?>" />
				<?php
				$wplisttable->spam_url_scheme_start();
				$wplisttable->search_box( 'search', '-search-input' );
				// Table of elements.
				$wplisttable->display();
				$wplisttable->spam_url_scheme_stop();
				?>
</form>
				<?php
			}
			if ( 'buffer' === $selected_menu ) {
				// Page header with count.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$spam_master_total_buffer = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
				?>
<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_attr( __( 'Buffer Size:', 'spam-master' ) ); ?> <span class="spam-master-admin-green spam-master-top-admin-shadow-offline"><?php echo esc_attr( $spam_master_total_buffer ); ?></span></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr></tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="Info"></span> <?php echo esc_attr( __( 'Spam Master Buffer greatly reduces server resources like cpu, memory and bandwidth by doing fast local machine checks. Also prevents major attacks like flooding, DoS , etc. via Spam Master Firewall.', 'spam-master' ) ); ?>
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="Info"></span> <?php echo esc_attr( __( 'You can use whitelisting to locally delete individual buffer entries. Spam Master Buffers for 6 months, older buffer entries are automatically deleted via weekly cron to keep your website clean and fast.', 'spam-master' ) ); ?>
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>

				<?php
				if ( ! class_exists( 'SpamMasterAdminTableBufferController' ) ) {
					require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablebuffercontroller.php';
				}
				// Prepare Table of elements.
				$wplisttable = new SpamMasterAdminTableBufferController();
				$wplisttable->prepare_items();
				if ( isset( $_REQUEST['page'] ) ) {
					$spam_page_full  = admin_url( 'options-general.php?page=' ) . wp_kses_post( wp_unslash( $_REQUEST['page'] ) ) . '&sm=buffer';
					$spam_page_short = wp_kses_post( wp_unslash( $_REQUEST['page'] ) );
				} else {
					$spam_page_full  = false;
					$spam_page_short = false;
				}
				?>
<form action="<?php echo esc_url( $spam_page_full ); ?>" method="post" name="wplisttable">
<input type="hidden" name="page" value="<?php echo esc_attr( $spam_page_short ); ?>" />
				<?php
				$wplisttable->spam_url_scheme_start();
				$wplisttable->search_box( 'search', '-search-input' );
				// Table of elements.
				$wplisttable->display();
				$wplisttable->spam_url_scheme_stop();
				?>
</form>
				<?php
			}
			if ( 'logs' === $selected_menu ) {
				wp_verify_nonce( $spam_nonce, 'spam-master-options-logs' );
				// Prepare pop up.
				add_thickbox();
				if ( empty( $spam_master_type ) || 'INACTIVE' === $spam_master_type || 'FREE' === $spam_master_type ) {
					?>
<table class="wp-list-table widefat fixed table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th colspan="4"><strong><?php echo esc_html( __( 'Firewall Logs', 'spam-master' ) ); ?></strong> <a class="btn-spammaster small green roundedspam" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_html( __( 'Premium Server Connection for peanuts', 'spam-master' ) ); ?>"><?php echo esc_html( __( 'Upgrade to Pro', 'spam-master' ) ); ?></a> <strong><?php echo $protection_text_small; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="spam-master-read-font" colspan="4">
					<?php echo esc_html( __( 'Thank you for using Spam Master one of the top 5 world-wide, real-time spam and exploits databases with more than ', 'spam-master' ) ) . esc_html( number_format( $spam_master_protection_total_number ) ) . esc_html( __( ' million threats and growing daily by the hundreds. We want to let you know that you can have for free extensive detailed logging at our website ', 'spam-master' ) ) . '<a href="https://www.spammaster.org" title="' . esc_html( __( 'www.spammaster.org', 'spam-master' ) ) . '" target="_blank">' . esc_html( __( 'www.spammaster.org', 'spam-master' ) ) . '</a>' . esc_html( __( ' if you register and login with your license attached email ', 'spam-master' ) ) . esc_html( $spam_master_attached ) . esc_html( __( ', but if you want to quick check the logs here and support Spam Master project at the same time please consider a ', 'spam-master' ) ) . '<a href="https://www.techgasp.com/downloads/spam-master-license/" title="' . esc_html( __( 'PRO KEY', 'spam-master' ) ) . '" target="_blank">' . esc_html( __( 'PRO KEY', 'spam-master' ) ) . '</a>' . esc_html( __( ' it costs peanuts per year.', 'spam-master' ) ); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<a class="thickbox" title="<?php echo esc_attr( __( 'Firewall Logs', 'spam-master' ) ); ?>" href="#TB_inline?&width=866&height=448&inlineId=firewalllogs" class="thickbox">
					<img class="spam-master-50 spam-master-middle spam-master-center-img spam-master-admin-logo" src="<?php echo esc_attr( plugins_url( 'spam-master/images/spammaster.svg' ) ); ?>" alt="<?php echo esc_attr( $plugin_master_name ); ?>" align="center" />
				</a>
			</td>
			<td colspan="2">
				<a class="thickbox" title="<?php echo esc_attr( __( 'Firewall Logs Detailed', 'spam-master' ) ); ?>" href="#TB_inline?&width=866&height=448&inlineId=firewalllogsdetailed" class="thickbox">
					<img class="spam-master-100 spam-master-middle spam-master-center-img spam-master-admin-logo" src="<?php echo esc_attr( plugins_url( 'spam-master/images/spam-master-logs-detailed.png' ) ); ?>" alt="<?php echo esc_attr( $plugin_master_name ); ?>" align="center" />
				</a>
			</td>
		</tr>
		<tr>
			<td class="spam-master-read-font spam-master-top-admin-yellow" colspan="4"></td>
		</tr>
		<tr>
			<td class="spam-master-read-font spam-master-top-admin-yellow" colspan="4">
				<strong><?php echo esc_html( __( 'Spam Master Pro is blazing fast plus awesome features:', 'spam-master' ) ); ?></strong>
			</td>
		</tr> 
		<tr>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'All protection features, no extra addons', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'Connection to Business RBL Server Cluster', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'For heavy duty websites and e-commerce', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'Real-Time Firewall Management', 'spam-master' ) ); ?>
			</td>
		</tr>
		<tr>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'Central Management for all websites', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( 'Private, ticketed email support', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-saved spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <?php echo esc_html( __( '1 year PRO key with full protection', 'spam-master' ) ); ?>
			</td>
			<td class="spam-master-read-font spam-master-top-admin-yellow">
				<span class="dashicons dashicons-thumbs-up spam-master-admin-green spam-master-top-admin-shadow-blue"></span> <a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_html( __( 'Premium Server Connection for peanuts', 'spam-master' ) ); ?>"><strong><?php echo esc_html( __( 'Upgrade to Pro', 'spam-master' ) ); ?></strong></a>
			</td>
		</tr>
		<tr>
			<td class="spam-master-read-font spam-master-top-admin-yellow" colspan="4"></td>
		</tr>
	</tbody>
</table>

<div id="firewalllogs" style="display:none;">
	<img class="spam-master-50 spam-master-middle spam-master-center-img spam-master-admin-logo" src="<?php echo esc_attr( plugins_url( 'spam-master/images/spammaster.svg' ) ); ?>" alt="<?php echo esc_attr( $plugin_master_name ); ?>" align="center" />
</div>
<div id="firewalllogsdetailed" style="display:none;">
	<img class="spam-master-100 spam-master-middle spam-master-center-img spam-master-admin-logo" src="<?php echo esc_attr( plugins_url( 'spam-master/images/spam-master-logs-detailed.png' ) ); ?>" alt="<?php echo esc_attr( $plugin_master_name ); ?>" align="center" />
</div>
					<?php
				}
				if ( 'FULL' === $spam_master_type ) {
					if ( ! class_exists( 'SpamMasterAdminTableLogsController' ) ) {
						require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablelogscontroller.php';
					}
					// Prepare Table of elements.
					$wplisttable = new SpamMasterAdminTableLogsController();
					$wplisttable->prepare_items();
					if ( isset( $_REQUEST['page'] ) ) {
						$spam_page_full  = admin_url( 'options-general.php?page=' ) . wp_kses_post( wp_unslash( $_REQUEST['page'] ) ) . '&sm=logs';
						$spam_page_short = wp_kses_post( wp_unslash( $_REQUEST['page'] ) );
					} else {
						$spam_page_full  = false;
						$spam_page_short = false;
					}
					?>
<form action="<?php echo esc_url( $spam_page_full ); ?>" method="post" name="wplisttable">
<input type="hidden" name="page" value="<?php echo esc_attr( $spam_page_short ); ?>" />
					<?php
					$wplisttable->spam_url_scheme_start();
					$wplisttable->search_box( 'search', '-search-input' );
					// Table of elements.
					$wplisttable->display();
					$wplisttable->spam_url_scheme_stop();
					?>
</form>
					<?php
				}
			}
			if ( 'help' === $selected_menu ) {
				wp_verify_nonce( $spam_nonce, 'spam-master-options-help' );

				// Load tests table.
				require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-test-table.php';

				// Load integrations table.
				// require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-integrations-table.php';.

				// Load online firewall and stats table.
				require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-online-table.php';

				// Load offer table.
				require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-offer-table.php';
			}
			?>
<div class="spam-master-pad-table"></div>
			<?php
		}
	}

}
?>
