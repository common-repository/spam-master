<?php
/**
 * Email controller
 *
 * @package Spam Master
 */

/**
 * Main Email class.
 *
 * @since 6.0.0
 */
class SpamMasterEmailController {

	/**
	 * Variable spammail.
	 *
	 * @var spammail $spammail
	 **/
	protected $spammail;

	/**
	 * Spam master cron alert.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastercronalert( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_cron_alert_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cron_alert_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_cron_alert_date_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cron_alert_date_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_cron_alert_date_admin_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_cron_alert_date_admin_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_cron_alert_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_cron_alert_date_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_cron_alert_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_cron_alert_date_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			if ( '1' !== $spam_master_cron_alert_date_admin_notice ) {
				$data_spam3  = array( 'spamvalue' => '1' );
				$where_spam3 = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_cron_alert_date_admin_notice',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam3, $where_spam3 );
				$email_s_p = ', stats@spammaster.org';
			} else {
				$email_s_p = false;
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_cron_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_cron_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Cron Not Running';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master detected issues with your website cron.</p>
<p>Your blog: ' . $blogname . '</p>
<p>This as nothing to do with our plugin but the native WordPress Cron is required for many important website / WordPress tasks. Can you check if your WordPress cron is running correctly?</p>
<p>In case you trouble we can give you a hand troubleshooting the issue.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email . $email_s_p;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_cron_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Cron Alert';
			$spamvalue                  = 'Cron Alert email notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master alert.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasteralert( $spammail ) {
		global $wpdb, $blog_id;

		// Set time for everything.
		$spam_master_time = current_datetime()->format( 'Y-m-d H:i:s' );

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}

		$spamkey    = 'Option';
		$spamtype40 = 'spam_master_emails_alert_date';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_emails_alert_date = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype40
			)
		);
		$spamtype41 = 'spam_master_emails_alert_notice';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_emails_alert_notice = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype41
			)
		);

		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_daily_email_plus1   = gmdate( 'Y-m-d', strtotime( '+1 days', strtotime( $spam_master_emails_alert_date ) ) );
		if ( $spam_master_emails_current_date >= $spam_master_daily_email_plus1 && '1' !== $spam_master_emails_alert_notice ) {

			// Get date.
			$spam_master_emails_current_email_date = current_datetime()->format( 'Y-m-d' );
			// Set date option.
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_email_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_alert_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			// Set notice option.
			$data_spam  = array( 'spamvalue' => '1' );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_alert_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level_p_text = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level_p_text'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

			if ( 'ALERT_0' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '0';
			}
			if ( 'ALERT_1' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '1';
			}
			if ( 'ALERT_2' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '2';
			}
			if ( 'VALID' === $spam_master_status ) {
				$spam_master_warning           = false;
				$spam_master_warning_signature = '<p>All is good.</p>';
			}
			if ( 'MALFUNCTION_1' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 1, please update Spam Master to the latest version</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( 'MALFUNCTION_2' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 2, use a single key per site. Create more Free Connections Keys online.</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( $spam_master_block_count <= '10' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>good, less than 10 since beginning of time</b></li>';
			}
			if ( $spam_master_block_count >= '11' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>' . number_format( $spam_master_block_count ) . ' since beginning of time</b></li>';
			}
			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

				if ( ! function_exists( 'spam_master_send_daily_report_html' ) ) {
					/**
					 * Spam master html email.
					 *
					 * @return string
					 */
					function spam_master_send_daily_report_html() {
						return 'text/html';
					}
					add_filter( 'wp_mail_content_type', 'spam_master_send_daily_report_html' );
				}
				// Email Subject Title Header.
				$spam_master_subject_title = 'Daily Report';
				$spam_master_html          = '<!DOCTYPE html><html>';
				$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
				$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
				$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
				$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
				// Email Content.
				$spam_master_table_content        = '<p>Spam Master Daily Alert Level Report for ' . $blogname . '</p>
<ul>
' . $spam_master_warning . '
<li>Alert Level: <b>' . $spam_master_alert_level_deconstructed . '</b></li>
<li>Spam Probability: <b>' . $spam_master_alert_level_p_text . '%</b></li>
<li>Protected Against: <b>' . number_format( $spam_master_protection_total_number ) . ' threats</b></li>' .
				$spam_master_block_count_result . '
</ul>
' . $spam_master_warning_signature . '
<p>The daily alert level report email can be turned off in www.spammaster.org Licenses page, Tools, Configure button. Make sure to login or register with your attached email ' . $spam_master_attached . '.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
				$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
				$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
				$spam_master_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
				$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
				// send email.
				$from    = $admin_email;
				$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
				$subject = $spam_master_subject_title;
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				$message = $spam_master_html .
							$spam_master_header .
							$spam_master_body .
							$spam_master_table_header .
							$spam_master_table_body .
							$spam_master_table_content .
							$spam_master_table_content_close .
							$spam_master_table_footer_start .
							$spam_master_table_footer_content .
							$spam_master_email_close;

				wp_mail( $to, $subject, $message, $headers );

				// Reset content-type to avoid conflicts.
				remove_filter( 'wp_mail_content_type', 'spam_master_send_daily_report_html' );

				// Log InUp Controller.
				$remote_ip                  = $spam_master_ip;
				$blog_threat_email          = 'localhost';
				$remote_referer             = 'localhost';
				$dest_url                   = 'localhost';
				$remote_agent               = 'localhost';
				$spamuser                   = array( 'ID' => 'none' );
				$spamuser_a                 = wp_json_encode( $spamuser );
				$spamtype                   = 'Daily Stats';
				$spamvalue                  = 'Daily Email successfully sent.';
				$cache                      = '4H';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
			}
		}

	}

	/**
	 * Spam master alert 3.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasteralert3( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}

		$spamkey    = 'Option';
		$spamtype40 = 'spam_master_emails_alert_date';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_emails_alert_date = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype40
			)
		);
		$spamtype41 = 'spam_master_emails_alert_notice';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_emails_alert_notice = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype41
			)
		);

		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_daily_email_plus1   = gmdate( 'Y-m-d', strtotime( '+1 days', strtotime( $spam_master_emails_alert_date ) ) );
		if ( $spam_master_emails_current_date >= $spam_master_daily_email_plus1 && '1' !== $spam_master_emails_alert_notice ) {

			// Get date.
			$spam_master_emails_current_email_date = current_datetime()->format( 'Y-m-d' );
			// Set date option.
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_email_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_alert_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			// Set notice option.
			$data_spam  = array( 'spamvalue' => '1' );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_alert_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

			if ( 'VALID' === $spam_master_status ) {
				$spam_master_warning           = false;
				$spam_master_warning_signature = '<p>All is good.</p>';
			}
			if ( 'MALFUNCTION_1' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 1, please update Spam Master to the latest version</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( 'MALFUNCTION_2' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 2, use a single key per site. Create more Free Connections Keys online.</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( $spam_master_block_count <= '10' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>good, less than 10 since beginning of time</b></li>';
			}
			if ( $spam_master_block_count >= '11' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>' . number_format( $spam_master_block_count ) . ' since beginning of time</b></li>';
			}
			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

				/**
				 * Spam master html email.
				 *
				 * @return string
				 */
				function spam_master_send_alert_3_report_html() {
					return 'text/html';
				}
				add_filter( 'wp_mail_content_type', 'spam_master_send_alert_3_report_html' );
				// Email Subject Title Header.
				$spam_master_subject_title = 'Alert 3 Detected!!!';
				$spam_master_html          = '<!DOCTYPE html><html>';
				$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
				$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
				$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #C70404; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
				$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
				// Email Content.
				$spam_master_table_content        = '<p>Warning!!! Spam Master Alert 3 detected for ' . $blogname . '.</p>
<p>Spam Master is temporarily locking down your website until the threat level drops. Take a look at our documentation to apply more security measures or get in touch with TechGasp via support ticket, we personally monitor all alert levels 3 and will gladly help you.</p>
<ul>
' . $spam_master_warning . '
<li>Alert Level: <b>3</b></li>
<li>Spam Probability: <b>' . $spam_master_alert_level_p_text . '%</b></li>
<li>Protected Against: <b>' . number_format( $spam_master_protection_total_number ) . ' threats</b></li>
' . $spam_master_block_count_result . '
</ul>
' . $spam_master_warning_signature . '
<p>The Alert 3 email will automatically stop when your website alert level drops to safer levels. You can also turn off the alert level 3 daily email in www.spammaster.org Licenses page, Tools, Configure button. Make sure to login or register with your attached email ' . $spam_master_attached . '.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
				$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
				$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
				$spam_master_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
				$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
				// send email.
				$from    = $admin_email;
				$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
				$subject = $spam_master_subject_title;
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				$message = $spam_master_html .
							$spam_master_header .
							$spam_master_body .
							$spam_master_table_header .
							$spam_master_table_body .
							$spam_master_table_content .
							$spam_master_table_content_close .
							$spam_master_table_footer_start .
							$spam_master_table_footer_content .
							$spam_master_email_close;

				wp_mail( $to, $subject, $message, $headers );

				// Reset content-type to avoid conflicts.
				remove_filter( 'wp_mail_content_type', 'spam_master_send_alert_3_report_html' );

				// Log InUp Controller.
				$remote_ip                  = $spam_master_ip;
				$blog_threat_email          = 'localhost';
				$remote_referer             = 'localhost';
				$dest_url                   = 'localhost';
				$remote_agent               = 'localhost';
				$spamuser                   = array( 'ID' => 'none' );
				$spamuser_a                 = wp_json_encode( $spamuser );
				$spamtype                   = 'Alert 3';
				$spamvalue                  = 'Alert 3 Email successfully sent.';
				$cache                      = '1D';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
			}
		}

	}

	/**
	 * Spam master auto free.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterautofree( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_lic_hash = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		/**
		 * Spam master html email.
		 *
		 * @return string
		 */
		function spam_master_send_user_notice_free_created_html() {
			return 'text/html';
		}
		add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_free_created_html' );
		// Email Subject Title Header.
		$spam_master_subject_title = 'Congratulations!!!';
		$spam_master_html          = '<!DOCTYPE html><html>';
		$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
		$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
		$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
		$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
		// Email Content.
		$spam_master_table_content        = '<p>Congratulations, ' . $blogname . ' is now protected by Spam Master against millions of threats.</p>
<ul>
<li>Connection Key: <b>' . $spam_master_lic_hash . '</b></li>
<li>Protected Against: <b>' . number_format( $spam_master_protection_total_number ) . ' threats</b></li>
</ul>
<p>Enjoy.</p>
<p>SpamMaster Team</p>';
		$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
		$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
		$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org/documentation/" target="_blank">Help & Documentation</a></p>';
		$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
		// send email.
		$from    = $admin_email;
		$to      = $admin_email;
		$subject = $spam_master_subject_title;
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$message = $spam_master_html .
					$spam_master_header .
					$spam_master_body .
					$spam_master_table_header .
					$spam_master_table_body .
					$spam_master_table_content .
					$spam_master_table_content_close .
					$spam_master_table_footer_start .
					$spam_master_table_footer_content .
					$spam_master_email_close;
		wp_mail( $to, $subject, $message, $headers );
		// Reset content-type to avoid conflicts.
		remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_free_created_html' );

		// Log InUp Controller.
		$remote_ip                  = $spam_master_ip;
		$blog_threat_email          = 'localhost';
		$remote_referer             = 'localhost';
		$dest_url                   = 'localhost';
		$remote_agent               = 'localhost';
		$spamuser                   = array( 'ID' => 'none' );
		$spamuser_a                 = wp_json_encode( $spamuser );
		$spamtype                   = 'Key Email';
		$spamvalue                  = 'Key Email Sent to ' . $admin_email;
		$cache                      = '1D';
		$spam_master_log_controller = new SpamMasterLogController();
		$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );

	}

	/**
	 * Spam master weekly report.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterweeklyreport( $spammail ) {
		global $wpdb, $blog_id;

		// Set time for everything.
		$spam_master_time = current_datetime()->format( 'Y-m-d H:i:s' );
		// only get the last 1 day of threats/ips.
		$spam_master_time_week = gmdate( 'Y-m-d H:i:s', strtotime( $spam_master_time . '-7 days' ) );

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
			$table_prefix     = $wpdb->base_prefix;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_user_registrations = $wpdb->get_var( "SELECT COUNT(umeta_id) FROM {$table_prefix}usermeta WHERE meta_key='primary_blog' AND meta_value={$blog_id}" );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_user_registrations = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users" );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_weekly_email_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_email_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_alert_level_p_text = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level_p_text'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_firewall_on = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_firewall_on'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_honeypot_timetrap = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_learning_active = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_learning_active'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_total_logging_count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys}" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// set date.
		$spam_master_emails_current_email_date = current_datetime()->format( 'Y-m-d' );
		// only run if dates !=.
		if ( $spam_master_emails_current_email_date !== $spam_master_emails_weekly_email_date ) {
			// Set date option.
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_email_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_weekly_email_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			if ( ! empty( $spam_master_alert_level ) ) {
				$spam_master_alert_level_deconstructed = '0';
			}
			if ( 'ALERT_0' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '0';
			}
			if ( 'ALERT_1' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '1';
			}
			if ( 'ALERT_2' === $spam_master_alert_level ) {
				$spam_master_alert_level_deconstructed = '2';
			}
			if ( 'VALID' === $spam_master_status ) {
				$spam_master_warning           = false;
				$spam_master_warning_signature = '<p>All is good.</p>';
			}
			if ( 'MALFUNCTION_1' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 1, please update Spam Master to the latest version</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( 'MALFUNCTION_2' === $spam_master_status ) {
				$spam_master_warning           = '<li>Warnings: <b>Malfunction 2, use a single key per site. Create more Free Connections Keys online.</b></li>';
				$spam_master_warning_signature = '<p>Please correct the warnings.</p>';
			}
			if ( $spam_master_block_count <= '10' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>good, less than 10 since beginning of time</b></li>';
			}
			if ( $spam_master_block_count >= '11' ) {
				$spam_master_block_count_result = '<li>Total Blocks: <b>' . number_format( $spam_master_block_count ) . ' since beginning of time</b></li>';
			}
			if ( 'true' === $spam_master_firewall_on ) {
				$spam_master_firewall_on_result = 'Online';
			} else {
				$spam_master_firewall_on_result = 'Offline';
			}
			if ( 'true' === $spam_master_learning_active ) {
				$spam_master_learning_active_result = 'Online';
			} else {
				$spam_master_learning_active_result = 'Offline';
			}
			// All Cache.
			if ( empty( $spam_master_total_logging_count ) || '0' === $spam_master_total_logging_count ) {
				$spam_master_total_logging_count_text = 'Good no entries';
			} else {
				$spam_master_total_logging_count_text = number_format( $spam_master_total_logging_count );
			}

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

				/**
				 * Spam master html email.
				 *
				 * @return string
				 */
				function spam_master_send_weekly_report_html() {
					return 'text/html';
				}
				add_filter( 'wp_mail_content_type', 'spam_master_send_weekly_report_html' );
				// Email Subject Title Header.
				$spam_master_subject_title = 'Weekly Report';
				$spam_master_html          = '<!DOCTYPE html><html>';
				$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
				$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
				$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
				$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
				// Email Content.
				$spam_master_table_content        = '<p>Spam Master Weekly Report for ' . $blogname . '</p>
<ul>
' . $spam_master_warning . '
<li>Alert Level: <b>' . $spam_master_alert_level_deconstructed . '</b></li>
<li>Spam Probability: <b>' . $spam_master_alert_level_p_text . '%</b></li>
<li>Protected Against: <b>' . number_format( $spam_master_protection_total_number ) . ' threats</b></li>
<li>Spam Learning: <b>' . $spam_master_learning_active_result . '</b></li>
' . $spam_master_block_count_result . '
<li>Spam Firewall: <b>' . $spam_master_firewall_on_result . '</b></li>
<li>Total Cache Entries: <b>' . $spam_master_total_logging_count_text . '</b></li>
<li>Total Users: <b>' . number_format( $spam_master_user_registrations ) . ' registrations</b></li>
</ul>
<p>The weekly report email can be turned off in www.spammaster.org Licenses page, Tools, Configure button. Make sure to login or register with your attached email ' . $spam_master_attached . '.</p>
<p>See you next week!</p>
<p>SpamMaster Team</p>';
				$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
				$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
				$spam_master_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
				$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
				// send email.
				$from    = $admin_email;
				$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
				$subject = $spam_master_subject_title;
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				$message = $spam_master_html .
							$spam_master_header .
							$spam_master_body .
							$spam_master_table_header .
							$spam_master_table_body .
							$spam_master_table_content .
							$spam_master_table_content_close .
							$spam_master_table_footer_start .
							$spam_master_table_footer_content .
							$spam_master_email_close;
				wp_mail( $to, $subject, $message, $headers );
				// Reset content-type to avoid conflicts.
				remove_filter( 'wp_mail_content_type', 'spam_master_send_weekly_report_html' );

				// Log InUp Controller.
				$remote_ip                  = $spam_master_ip;
				$blog_threat_email          = 'localhost';
				$remote_referer             = 'localhost';
				$dest_url                   = 'localhost';
				$remote_agent               = 'localhost';
				$spamuser                   = array( 'ID' => 'none' );
				$spamuser_a                 = wp_json_encode( $spamuser );
				$spamtype                   = 'Weekly Stats';
				$spamvalue                  = 'Weekly Email successfully sent.';
				$cache                      = '1D';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
			}
		}

	}

	/**
	 * Spam master weekly statistics.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterweeklystatsreport( $spammail ) {
		global $wpdb, $blog_id;

		// Set time for everything.
		$spam_master_time = current_datetime()->format( 'Y-m-d H:i:s' );
		// only get the last 1 day of threats/ips.
		$spam_master_time_week = gmdate( 'Y-m-d H:i:s', strtotime( $spam_master_time . '-7 days' ) );
		$spam_master_version   = constant( 'SPAM_MASTER_VERSION' );
		$email_s_p             = 'stats@spammaster.org';

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$blogname         = get_blog_option( $blog_id, 'blogname' );
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$table_prefix     = $wpdb->base_prefix;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_user_registrations = $wpdb->get_var( "SELECT COUNT(umeta_id) FROM {$table_prefix}usermeta WHERE meta_key='primary_blog' AND meta_value={$blog_id}" );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$blogname         = get_option( 'blogname' );
			$admin_email      = get_option( 'admin_email' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_user_registrations = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users" );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		$spam_master_version = constant( 'SPAM_MASTER_VERSION' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_weekly_stats_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_weekly_stats_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_block_count = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_block_count'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_honeypot_timetrap = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_honeypot_timetrap'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_total_logging_count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys}" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_total_buffer = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'Buffer'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_total_white = $wpdb->get_var( "SELECT COUNT(ID) FROM {$spam_master_keys} WHERE spamkey = 'White'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_total_exempt = $wpdb->get_var( "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype LIKE '%exempt%'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_db = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );

		// set date.
		$spam_master_emails_current_stats_date = current_datetime()->format( 'Y-m-d' );
		// only run if dates !=.
		if ( $spam_master_emails_current_stats_date !== $spam_master_emails_weekly_stats_date ) {
			// Set date option.
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_stats_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_emails_weekly_stats_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {

				/**
				 * Spam master html email.
				 *
				 * @return string
				 */
				function spam_master_send_improve_html() {
					return 'text/html';
				}
				add_filter( 'wp_mail_content_type', 'spam_master_send_improve_html' );
				// Email Subject Title Header.
				$spam_master_stats_subject_title = 'Weekly Report';
				$spam_master_stats_html          = '<!DOCTYPE html><html>';
				$spam_master_stats_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_stats_subject_title . '</title></head>';
				$spam_master_stats_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
				$spam_master_stats_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_stats_subject_title . '
</h1></td></tr></table></td></tr>';
				$spam_master_stats_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
				// Email Content.
				$spam_master_stats_table_content        = '<p>Spam Master Weekly Report for <strong>' . $blogname . '</strong></p>
<ul>
<li>Version: <b>' . $spam_master_version . '</b> Status: <b>' . $spam_master_status . '</b></li>
<li>Key: <b>' . $spam_license_key . '</b> Hash: <b>' . $spam_master_db . '</b></li>
<li>Total Users: <b>' . number_format( $spam_master_user_registrations ) . '</b></li>
<li>Total Blocks: <b>' . number_format( $spam_master_block_count ) . '</b></li>
<li>Keys Buffer: <b>' . number_format( $spam_master_total_buffer ) . '</b></li>
<li>Keys White: <b>' . number_format( $spam_master_total_white ) . '</b></li>
<li>Keys Exemptions: <b>' . number_format( $spam_master_total_exempt ) . '</b></li>
<li>Keys Total: <b>' . number_format( $spam_master_total_logging_count ) . '</b></li>
</ul>
<p>SpamMaster Team</p>';
				$spam_master_stats_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
				$spam_master_stats_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
				$spam_master_stats_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
				$spam_master_stats_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
				// send email.
				$from    = $admin_email;
				$to      = $email_s_p;
				$subject = $spam_master_stats_subject_title;
				$headers = array( 'Content-Type: text/html; charset=UTF-8' );
				$message = $spam_master_stats_html .
							$spam_master_stats_header .
							$spam_master_stats_body .
							$spam_master_stats_table_header .
							$spam_master_stats_table_body .
							$spam_master_stats_table_content .
							$spam_master_stats_table_content_close .
							$spam_master_stats_table_footer_start .
							$spam_master_stats_table_footer_content .
							$spam_master_stats_email_close;
				wp_mail( $to, $subject, $message, $headers );
				// Reset content-type to avoid conflicts.
				remove_filter( 'wp_mail_content_type', 'spam_master_send_improve_html' );

				// Log InUp Controller.
				$remote_ip                  = $spam_master_ip;
				$blog_threat_email          = 'localhost';
				$remote_referer             = 'localhost';
				$dest_url                   = 'localhost';
				$remote_agent               = 'localhost';
				$spamuser                   = array( 'ID' => 'none' );
				$spamuser_a                 = wp_json_encode( $spamuser );
				$spamtype                   = 'Weekly Improve';
				$spamvalue                  = 'Weekly Stats Improve Email successfully sent.';
				$cache                      = '1D';
				$spam_master_log_controller = new SpamMasterLogController();
				$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
			}
		}

	}

	/**
	 * Spam master free notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterfreenotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_expires = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_expires'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_free_rate_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_rate_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Plus 7 Days.
		$spam_master_expires_plus_7 = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_expires_plus_7 && '1' !== $spam_master_free_rate_notice ) {
			// Update Notice.
			$data_spam  = array( 'spamvalue' => '1' );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_free_rate_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_full_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_full_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Thank You!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Thank you for using Spam Master in ' . $blogname . ', for 7 days now. Spam Master is one of the highest rated and safest WordPress protection plugins with millions of threats listed in real time blocklist databases... growing daily.</p>
<p>This service is provided totally <strong>FREE</strong>, if you have a few minutes, please share the love on wordpress.org and rate us:</p>
<ul>
<li><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">share the love on WordPress.org</a></li>
</ul>
<p>This is a one time email, Thank you very much.</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_full_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Email Thank You';
			$spamvalue                  = 'Email thank you successfully sent.';
			$cache                      = '3M';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master full notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterfullnotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_install_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_install_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Check Notice.
		if ( '1' !== $spam_master_full_install_notice ) {
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_full_install_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_full_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_full_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Thank You!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Thank you for using Spam Master in ' . $blogname . ', one of the highest rated and safest WordPress protection plugins with millions of threats listed in real time blocklist databases... growing daily.</p>
<p>If you have a few minutes, please share the love on wordpress.org and rate us:</p>
<ul>
<li><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">share the love on WordPress.org</a></li>
</ul>
<p>This is a one time email.</p>
<p>Thank you very much</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.wordpress.org/plugins/spam-master/" target="_blank">Share the love, please rate us on WordPress.org</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_full_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Email Pro Thank You';
			$spamvalue                  = 'License email thank you successfully sent.';
			$cache                      = '3M';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master unstable notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterunstablenotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_free_unstable_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_unstable_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_unstable_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_unstable_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_free_unstable_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_unstable_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_free_unstable_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_unstable_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_unstable_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_unstable_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Unstable Spam Master Connection';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master FREE RBL Server connection is Unstable. You are not Protected!!!</p>
<p>Your blog: ' . $blogname . '</p>
<p>We apologize for that, there\'s probably a high demand of spam check requests to our free servers at this point.</p>
<p>Please check the Free RBL server cluster status <a href="https://www.spammaster.org/rbl-servers-status/" title="Free Server Cluster Status" target="_blank"><strong><em>here</strong></em></a> and wait 4 to 24 hours in order for the free service auto regain stability. If you want to avoid these issues in the future with 100% up-time spam checks you might want to consider a Pro License, it costs peanuts per year and connects to our Premium Business Server Clusters.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank">get full license</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_unstable_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction Unstable';
			$spamvalue                  = 'License email unstable notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master full inact notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterfullinactnotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_inactive_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_inactive_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_inactive_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_inactive_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Plus days.
		$spam_master_emails_plus_days = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_full_inactive_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_full_inactive_notice ) {
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_full_inactive_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_full_inactive_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_inactive_full_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_inactive_full_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'License Inactive!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>' . $blogname . ' is no longer protected by Spam Master against millions of threats because your license is inactive.</p>
<p>Maybe you haven\'t updated, upgraded Spam Master "for a very long time". Not to worry, please update Spam Master to the latest version and re-activate your license.</p>
<p>Also, if you use the same license in more than one website make sure each site uses the latest Spam Master version.</p>
<p>1. Update Spam Master to the latest version in your plugins administrator page in all your websites.</p>
<p>2. Go to Spam Master Settings page and under the license key press the <b>RE-SYNCHRONIZE LICENSE NOW</b> button.</p>
<p>3. If problem persists, please get in touch with our Support.</p>
<p>Thank you very much</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_inactive_full_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction Inactive';
			$spamvalue                  = 'Inactive License email successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master full exp notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterfullexpnotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_full_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_full_expired_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Plus days.
		$spam_master_emails_plus_days = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_full_expired_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_full_expired_notice ) {
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_full_expired_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_full_expired_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_expired_full_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_full_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'License Expired!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>' . $blogname . ' is no longer protected by Spam Master against millions of threats.</p>
<p>Hope you have enjoyed 1 year of bombastic protection. You can quickly get another license and get protected again, it costs peanuts per year.</p>
<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="get full license"><em>get full license</em></a></p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank">get full license</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_full_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction Expired';
			$spamvalue                  = 'License email full notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master free exp notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterfreeexpnotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_free_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_expired_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_free_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_free_expired_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Plus days.
		$spam_master_emails_plus_days = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_free_expired_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_free_expired_notice ) {
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_free_expired_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_free_expired_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_expired_free_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_free_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'License Expired!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>' . $blogname . ' is no longer protected by Spam Master against millions of threats.</p>
<p>Hope you have enjoyed the bombastic protection. You can quickly get another license and get protected again, it costs peanuts per year.</p>
<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="get full license"><em>get full license</em></a></p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank">get full license</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_free_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction Expired';
			$spamvalue                  = 'License email free notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master trial exp notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastertrialexpnotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_trial_expired_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_trial_expired_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_trial_expired_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_trial_expired_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		// Plus days.
		$spam_master_emails_plus_days = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_trial_expired_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_trial_expired_notice ) {
			$data_spam  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_trial_expired_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_trial_expired_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_expired_trial_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_trial_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'License Expired!!!';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>' . $blogname . ' is no longer protected by Spam Master against millions of threats.</p>
<p>Hope you have enjoyed the bombastic protection. You can quickly get another license and get protected again, it costs peanuts per year.</p>
<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="get full license"><em>get full license</em></a></p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank">get full license</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			// send email.
			$from    = $admin_email;
			$to      = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_expired_trial_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction Expired';
			$spamvalue                  = 'License email trial notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master deact email.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterdeactemail( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		$spam_master_version = constant( 'SPAM_MASTER_VERSION' );
		if ( empty( $spam_master_version ) ) {
			$spam_master_version = 'Not found';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );
		if ( empty( $spam_master_ip ) ) {
			$spam_master_ip = 'Not found';
		}
		$blog_url = get_site_url();
		if ( empty( $blog_url ) ) {
			$blog_url = 'your url';
		}
		$email_s_p = 'stats@spammaster.org';
		if ( empty( $admin_email ) ) {
			$admin_email = $email_s_p;
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_deact = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
		if ( empty( $spam_master_deact ) ) {
			$spam_master_deact = 'Not found';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_db = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
		if ( empty( $spam_master_db ) ) {
			$spam_master_db = 'Not found';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
		if ( empty( $spam_master_status ) ) {
			$spam_master_status = 'Not found';
		}
		/**
		 * Spam master html email.
		 *
		 * @return string
		 */
		function spam_master_send_deact_html() {
			return 'text/html';
		}
		add_filter( 'wp_mail_content_type', 'spam_master_send_deact_html' );
		// Email Subject Title Header.
		$spam_master_subject_title = 'Spam Master Deactivation';
		$spam_master_html          = '<!DOCTYPE html><html>';
		$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
		$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
		$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
		$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
		// Email Content.
		$spam_master_table_content        = '
<ul>
	<li>Blog: ' . $blogname . '</li>
	<li>Url: ' . $blog_url . '</li>
	<li>Ip: ' . $spam_master_ip . '</li>
	<li>Email: ' . $admin_email . '</li>
	<li>Deact: ' . $spam_master_deact . '</li>
	<li>Hash: ' . $spam_master_db . '</li>
	<li>Version: ' . $spam_master_version . '</li>
	<li>Status: ' . $spam_master_status . '</li>
</ul>
<p>Thanks</p>
<p>SpamMaster Team</p>';
		$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
		$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
		$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Spam Master</a></p>';
		$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
		// send email.
		$from    = $admin_email;
		$to      = $email_s_p;
		$subject = $spam_master_subject_title;
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$message = $spam_master_html .
					$spam_master_header .
					$spam_master_body .
					$spam_master_table_header .
					$spam_master_table_body .
					$spam_master_table_content .
					$spam_master_table_content_close .
					$spam_master_table_footer_start .
					$spam_master_table_footer_content .
					$spam_master_email_close;
		wp_mail( $to, $subject, $message, $headers );
		// Reset content-type to avoid conflicts.
		remove_filter( 'wp_mail_content_type', 'spam_master_send_deact_html' );

	}

	/**
	 * Spam master malfunction 1 notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastermalfunction1notify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_1_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_1_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_1_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_1_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_malfunction_1_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_malfunction_1_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_1_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_1_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_malfunction1_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction1_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Malfunction 1';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p><strong>Please Update Spam Master plugin to the latest version.</strong></p>
<p>Your blog: ' . $blogname . '</p>
<p>Your Key is Valid and your Protection is Active & Online, not to worry. Please update, upgrade Spam Master to the latest available version in your plugins administrator page. Once Spam Master is up-to-date press RE-SYNCHRONIZE CONNECTION button in Spam Master settings page.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction1_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction 1';
			$spamvalue                  = 'License email malfunction1 notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master malfunction 2 notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastermalfunction2notify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_2_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_2_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_2_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_2_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_malfunction_2_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_malfunction_2_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_2_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_2_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_malfunction2_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction2_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Malfunction 2';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master Malfunction 2 detected.</p>
<p>You are still protected but you are using the same license key in more than one website.</p>
<p>Your Connection Key might get UNSTABLE or with a MALFUNCTION that will affect all websites. Go to www.spammaster.org licenses page and detach all websites using this key except for one, create more unique keys to be used by other websites. Only use one key per website.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction2_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction 2';
			$spamvalue                  = 'License email malfunction2 notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master malfunction 6 notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastermalfunction6notify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_6_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_6_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_6_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_6_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_malfunction_6_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_malfunction_6_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_6_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_6_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_malfunction6_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction6_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Malfunction 6';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master Malfunction 6 detected.</p>
<p>Spam Master was not able to connect to the online RBL servers with that key.</p>
<p>Key already use in another website. Please visit www.spammaster.org to check your keys and get a new key. Only use one key per website.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction6_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction 6';
			$spamvalue                  = 'License email malfunction6 notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master malfunction 8 notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammastermalfunction8notify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_8_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_8_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_malfunction_8_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_malfunction_8_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_malfunction_8_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_malfunction_8_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_8_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_malfunction_8_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_notice_malfunction8_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction8_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Malfunction 8';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master Malfunction 8 detected.</p>
<p>We have detected CDN WAF that masks the end users IP addresses with CDN, WAF Ips.</p>
<p> Please read Spam Master online documentation to solve the issue.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org" target="_blank">Get Spam Master Support</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_notice_malfunction8_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Malfunction 8';
			$spamvalue                  = 'License email malfunction8 notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master high volume notify.
	 *
	 * @param spammail $spammail for email.
	 *
	 * @return string
	 */
	public function spammasterhighvolumenotify( $spammail ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_emails_extra_email_list = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_emails_extra_email_list'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_high_volume_date = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_high_volume_date'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_high_volume_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_high_volume_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );

		// Get date.
		$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
		$spam_master_emails_plus_days    = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_high_volume_date ) ) );
		// Check Notice.
		if ( $spam_master_emails_current_date >= $spam_master_emails_plus_days && '1' !== $spam_master_high_volume_notice ) {
			$data_spam1  = array( 'spamvalue' => $spam_master_emails_current_date );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_high_volume_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '1' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_high_volume_notice',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );

			// get extra emails.
			if ( 'true' === $spam_master_emails_extra_email ) {
				if ( ! empty( $spam_master_emails_extra_email_list ) ) {
					$spam_master_more_emails = ',' . $spam_master_emails_extra_email_list;
				} else {
					$spam_master_more_emails = '';
				}
			} else {
				$spam_master_more_emails = '';
			}
			// get attached.
			if ( ! empty( $spam_master_attached ) ) {
				if ( $spam_master_attached === $admin_email ) {
					$spam_master_attached_add = '';
				} else {
					$spam_master_attached_add = ',' . $spam_master_attached;
				}
			} else {
				$spam_master_attached_add = '';
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_user_high_volume_unstable_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_user_high_volume_unstable_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Warning! Spam Master High Volume Connection';
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
<tr>
<td>
<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
' . $spam_master_subject_title . '
</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
<tr>
<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
<table border="0" cellpadding="20" cellspacing="0" width="100%">
<tr>
<td valign="top">
<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Spam Master detected an High Volume of spam checks from your website using a free key.</p>
<p>Your blog: ' . $blogname . '</p>
<p>You are not Protected!!! Please wait 4 to 24 hours in order for your free spam checks count decrease or consider a <a href="https://www.techgasp.com/downloads/spam-master-license/" title="Pro License" target="_blank"><strong><em>Pro License</strong></em></a>, it costs peanuts per year and connects to our Premium Business Server Clusters.</p>
<p>Thanks</p>
<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>';
			$spam_master_table_footer_start   = '<tr>
<td align="center" valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
<tr>
<td valign="top">
<table border="0" cellpadding="10" cellspacing="0" width="100%">
<tr>
<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank">get full license</a></p>';
			$spam_master_email_close          = '</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>';
			$from                             = $admin_email;
			$to                               = $admin_email . $spam_master_more_emails . $spam_master_attached_add;
			$subject                          = $spam_master_subject_title;
			$headers                          = array( 'Content-Type: text/html; charset=UTF-8' );
			$message                          = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_user_high_volume_unstable_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'High Volume';
			$spamvalue                  = 'High volume email notice successfully sent.';
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}

	}

	/**
	 * Spam master discount notify.
	 *
	 * @param spammasterdiscdate $spammasterdiscdate for email.
	 * @param spamdesc           $spamdesc for email.
	 * @param spamdescper        $spamdescper for email.
	 * @param spamcode           $spamcode for email.
	 * @param spamsend           $spamsend for email.
	 *
	 * @return string
	 */
	public function spammasterdiscnotify( $spammasterdiscdate, $spamdesc, $spamdescper, $spamcode, $spamsend ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
			$blogname         = get_blog_option( $blog_id, 'blogname' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
			$blogname         = get_option( 'blogname' );

		}
		if ( empty( $blogname ) ) {
			$blogname = 'your blog';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_lic_hash = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_protection_total_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_protection_total_number'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_ip = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_ip'" ), 0, 48 );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spamsenddb = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spamsenddbdatepre = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not_date'" );
		// Current date.
		$spam_master_current_date = current_datetime()->format( 'Y-m-d' );

		if ( $spamsenddbdatepre !== $spammasterdiscdate && '0' === $spamsend && '0' === $spamsenddb ) {
			// Update notification.
			$data_spam  = array( 'spamvalue' => '1' );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_disc_not',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			$data_spam  = array( 'spamvalue' => $spam_master_current_date );
			$where_spam = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_disc_not_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

			// get attached.
			if ( empty( $admin_email ) ) {
				$admin_email = $spam_master_attached;
			}
			if ( empty( $spam_master_attached ) ) {
				$spam_master_attached = $admin_email;
			}

			/**
			 * Spam master html email.
			 *
			 * @return string
			 */
			function spam_master_send_disc_not_html() {
				return 'text/html';
			}
			add_filter( 'wp_mail_content_type', 'spam_master_send_disc_not_html' );
			// Email Subject Title Header.
			$spam_master_subject_title = 'Get it now for ' . $spamdesc;
			$spam_master_html          = '<!DOCTYPE html><html>';
			$spam_master_header        = '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>' . $spam_master_subject_title . '</title></head>';
			$spam_master_body          = '<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f6f6f6; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">';
			$spam_master_table_header  = '<div style="width:100%; -webkit-text-size-adjust:none !important; margin:0; padding: 70px 0 70px 0;">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
	<tr>
	<td align="center" valign="top">
	<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="box-shadow:0 0 0 1px #f3f3f3 !important; border-radius:3px !important; background-color: #ffffff; border: 1px solid #e9e9e9; border-radius:3px !important; padding: 20px;">
	<tr>
	<td align="center" valign="top">
	<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style=" color: #00000; border-top-left-radius:3px !important; border-top-right-radius:3px !important; border-bottom: 0; font-weight:bold; line-height:100%; text-align: center; vertical-align:middle;" bgcolor="#ffffff">
	<tr>
	<td>
	<h1 style="color: #000000; margin:0; padding: 28px 24px; display:block; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:32px; font-weight: 500; line-height: 1.2;">
	' . $spam_master_subject_title . '
	</h1></td></tr></table></td></tr>';
			$spam_master_table_body    = '<tr>
	<td align="center" valign="top">
	<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
	<tr>
	<td valign="top" style="border-radius:3px !important; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif;">
	<table border="0" cellpadding="20" cellspacing="0" width="100%">
	<tr>
	<td valign="top">
	<div style="color: #000000; font-size:14px; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; line-height:150%; text-align:left;">';
			// Email Content.
			$spam_master_table_content        = '<p>Your website, ' . $blogname . ' is protected by Spam Master for some time now against millions of threats with a Free license key.</p>
	<p>If you are enjoying the protection you can quickly get a PRO key with a <strong>' . $spamdesc . ' ' . $spamdescper . ' DISCOUNT CODE: ' . $spamcode . '</strong>.</p>
	<p>Grab it Now... The offer is only valid today, if you decide to get it use the below coupon during <a href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="checkout"><em>checkout</em></a> and afterwards insert your new PRO license key in the plugin settings page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.</p>
	<ul>
	<li>DISCOUNT CODE: <strong>' . $spamcode . '</strong></li>
	</ul>
	<p>Enjoy.</p>
	<p>SpamMaster Team</p>';
			$spam_master_table_content_close  = '</div>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>';
			$spam_master_table_footer_start   = '<tr>
	<td align="center" valign="top">
	<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="border-top:0; -webkit-border-radius:3px;">
	<tr>
	<td valign="top">
	<table border="0" cellpadding="10" cellspacing="0" width="100%">
	<tr>
	<td colspan="2" valign="middle" id="credit" style="border:0; color: #000000; font-family: &apos;Helvetica Neue&apos;, Helvetica, Arial, &apos;Lucida Grande&apos;, sans-serif; font-size:14px; line-height:125%; text-align:center;">';
			$spam_master_table_footer_content = '<p><a href="https://www.spammaster.org/documentation/" target="_blank">Help & Documentation</a></p>';
			$spam_master_email_close          = '</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</div>
	</body>
	</html>';
			// send email.
			$from    = $admin_email;
			$to      = $spam_master_attached;
			$subject = $spam_master_subject_title;
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = $spam_master_html .
						$spam_master_header .
						$spam_master_body .
						$spam_master_table_header .
						$spam_master_table_body .
						$spam_master_table_content .
						$spam_master_table_content_close .
						$spam_master_table_footer_start .
						$spam_master_table_footer_content .
						$spam_master_email_close;
			wp_mail( $to, $subject, $message, $headers );
			// Reset content-type to avoid conflicts.
			remove_filter( 'wp_mail_content_type', 'spam_master_send_disc_not_html' );

			// Log InUp Controller.
			$remote_ip                  = $spam_master_ip;
			$blog_threat_email          = 'localhost';
			$remote_referer             = 'localhost';
			$dest_url                   = 'localhost';
			$remote_agent               = 'localhost';
			$spamuser                   = array( 'ID' => 'none' );
			$spamuser_a                 = wp_json_encode( $spamuser );
			$spamtype                   = 'Key Email';
			$spamvalue                  = 'Discount Email Sent to ' . $admin_email;
			$cache                      = '1D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $remote_ip, $blog_threat_email, $remote_referer, $dest_url, $remote_agent, $spamuser_a, $spamtype, $spamvalue, $cache );
		}
	}

}
