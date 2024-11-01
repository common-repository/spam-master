<?php
/**
 * Invitation controller
 *
 * @package Spam Master
 */

/**
 * Main invitation class.
 *
 * @since 6.0.0
 */
class SpamMasterInvitationController {

	/**
	 * Variable spammasterdateshort.
	 *
	 * @var spammasterdateshort $spammasterdateshort
	 **/
	protected $spammasterdateshort;

	/**
	 * Spam master invitation.
	 *
	 * @return html
	 */
	public function spammasterinvitation() {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_expires = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_expires'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_invitation_free_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_invitation_free_notice'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_invitation_full_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_invitation_full_notice'" );

		$spam_master_current_date = current_datetime()->format( 'Y-m-d' );
		if ( empty( $spam_master_expires ) || 'EMPTY' === $spam_master_expires || '0000-00-00 00:00:00' === $spam_master_expires ) {
			$spam_master_expires = '2099-01-01 01:01:01';
		}
		$spam_master_invitation_notice_plus_7    = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
		$spam_master_invitation_notice_plus_15   = gmdate( 'Y-m-d', strtotime( '+31 days', strtotime( $spam_master_expires ) ) );
		$spam_master_invitation_notice_minus_333 = gmdate( 'Y-m-d', strtotime( '-333 days', strtotime( $spam_master_expires ) ) );
		if ( isset( $_SERVER['REQUEST_SCHEME'] ) && isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$path = esc_url_raw( wp_unslash( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) );
		} else {
			$path = false;
		}
		$current_url = wp_nonce_url( $path, 'spammasterdisnonce' );
		if ( empty( $spam_master_type ) || 'EMPTY' === $spam_master_type ) {
			$noact = true;
		}
		if ( 'FREE' === $spam_master_type && 'VALID' === $spam_master_status ) {
			if ( $spam_master_current_date >= $spam_master_invitation_notice_plus_7 && '1' !== $spam_master_invitation_free_notice ) {
				return '<table class="wp-list-table widefat fixed " cellspacing="0">
<thead>
<tr class="spam-master-top-admin-green">
<th>
<span class="dashicons dashicons-admin-post"></span> ' . __( 'If you haven\'t done so, Please Rate', 'spam_master' ) . ' <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="' . __( 'Let us know what you think, we value your input.', 'spam_master' ) . '" target="_blank"><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span></a> ' . __( 'on', 'spam_master' ) . ' <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="' . __( 'Spread the Love.', 'spam_master' ) . '" target="_blank"><strong>' . __( 'Wordpress.org', 'spam_master' ) . '</strong></a> ' . __( 'to help us spread the word', 'spam_master' ) . '. 
<a class="spam-master-admin-link-decor" href="' . esc_url( $current_url . '&spammasterdisfr=1', 'spammasterdisnonce' ) . '"><span class="dashicons dashicons-dismiss spam-master-top-admin-f-red spam-master-top-admin-shadow-orange spam-master-admin-float-r" title="' . __( 'Dismiss', 'spam_master' ) . '"></span></a>
</th>
</tr>
</thead>
</table>';
			}
			if ( $spam_master_current_date >= $spam_master_invitation_notice_plus_15 ) {
				return '<table class="wp-list-table widefat fixed " cellspacing="0">
<thead>
<tr class="spam-master-top-admin-yellow">
<th>
<span class="dashicons dashicons-admin-post"></span> ' . __( 'Thank you for using Spam Master. Please consider upgrading to a', 'spam_master' ) . ' <a href="https://www.techgasp.com/downloads/spam-master-license/" title="' . __( 'it costs peanuts per year', 'spam_master' ) . '" target="_blank"><span class="dashicons dashicons-info-outline"></span> ' . __( 'Pro Key', 'spam_master' ) . '</a> ' . __( 'for a huge connection boost to our Premium RBL Server Clusters, it costs peanuts per year.', 'spam_master' ) . '
</th>
</tr>
</thead>
</table>';
			}
		}
		if ( 'FULL' === $spam_master_type && 'VALID' === $spam_master_status ) {
			if ( $spam_master_current_date >= $spam_master_invitation_notice_minus_333 && '1' !== $spam_master_invitation_full_notice ) {
				return '<table class="wp-list-table widefat fixed " cellspacing="0">
<thead>
<tr class="spam-master-top-admin-green">
<th>
<span class="dashicons dashicons-admin-post"></span> ' . __( 'If you haven\'t done so, Please Rate', 'spam_master' ) . ' <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="' . __( 'Let us know what you think, we value your input.', 'spam_master' ) . '" target="_blank"><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span></a> ' . __( 'on', 'spam_master' ) . ' <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="' . __( 'Spread the Love.', 'spam_master' ) . '" target="_blank"><strong>' . __( 'Wordpress.org', 'spam_master' ) . '</strong></a> ' . __( 'to help us spread the word', 'spam_master' ) . '. 
<a class="spam-master-admin-link-decor" href="' . esc_url( $current_url . '&spammasterdisfu=1', 'spammasterdisnonce' ) . '"><span class="dashicons dashicons-dismiss spam-master-top-admin-f-red spam-master-top-admin-shadow-orange spam-master-admin-float-r" title="' . __( 'Dismiss', 'spam_master' ) . '"></span></a>
</th>
</tr>
</thead>
</table>';
			}
		}
	}

	/**
	 * Spam master date check.
	 *
	 * @param spammasterdateshort $spammasterdateshort for mail.
	 *
	 * @return void
	 */
	public function spammasterdatecheck( $spammasterdateshort ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spamsend = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not'" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spamsenddate = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_disc_not_date'" );

		if ( 'FREE' === $spam_master_type ) {
			$spammasterdiscdate = $spammasterdateshort;
			$spamdesc           = false;
			$spamdescper        = false;
			$spamcode           = false;
			if ( '2024-01-01' === $spammasterdateshort ) {
				$spamdesc    = 'New Year';
				$spamdescper = '25%';
				$spamcode    = 'NEWYPRO25';
			}
			if ( '2024-02-14' === $spammasterdateshort ) {
				$spamdesc    = 'Valentines Day';
				$spamdescper = '25%';
				$spamcode    = 'VALENTINEPRO25';
			}
			if ( '2024-03-19' === $spammasterdateshort ) {
				$spamdesc    = 'Spring';
				$spamdescper = '25%';
				$spamcode    = 'SPRINGRO25';
			}
			if ( '2024-06-20' === $spammasterdateshort ) {
				$spamdesc    = 'Summer';
				$spamdescper = '25%';
				$spamcode    = 'SUMMERPRO25';
			}
			if ( '2024-08-01' === $spammasterdateshort ) {
				$spamdesc    = 'August';
				$spamdescper = '25%';
				$spamcode    = 'AUGUSTPRO25';
			}
			if ( '2024-10-31' === $spammasterdateshort ) {
				$spamdesc    = 'Halloween';
				$spamdescper = '25%';
				$spamcode    = 'HALLOWEENPRO25';
			}
			if ( '2024-11-29' === $spammasterdateshort ) {
				$spamdesc    = 'Black Friday';
				$spamdescper = '20%';
				$spamcode    = 'BLACKPRO20';
			}
			if ( '2024-12-02' === $spammasterdateshort ) {
				$spamdesc    = 'Cyber Monday';
				$spamdescper = '25%';
				$spamcode    = 'CYBERPRO25';
			}
			if ( '2024-12-25' === $spammasterdateshort ) {
				$spamdesc    = 'Christmas';
				$spamdescper = '20%';
				$spamcode    = 'XMASPRO20';
			}
			if ( '0' === $spamsend && $spammasterdiscdate !== $spamsenddate && ! empty( $spamdesc ) && ! empty( $spamdescper ) && ! empty( $spamcode ) ) {
				// Call email controller.
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_email                     = $spam_master_email_controller->spammasterdiscnotify( $spammasterdiscdate, $spamdesc, $spamdescper, $spamcode, $spamsend );
			}
		}
	}

	/**
	 * Spam master admin notices.
	 *
	 * @param spammasterdateshort $spammasterdateshort for notices.
	 *
	 * @return var
	 */
	public function spammasteradminnot( $spammasterdateshort ) {
		global $wpdb, $blog_id;

		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );

		$is_invitation = false;
		if ( 'FREE' === $spam_master_type ) {
			if ( '2024-01-01' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					 ' . esc_attr( __( 'Thank you for using Spam Master Free Version. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . '<strong>' . esc_attr( __( 'New Year 25% DISCOUNT CODE: NEWYPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>.' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					 </p>
					</div>';
			}
			if ( '2024-02-14' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free Version. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Valentine\'s Day 25% DISCOUNT CODE: VALENTINEPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-03-19' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Spring Day 25% DISCOUNT CODE: SPRINGRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-06-20' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Summer 25% DISCOUNT CODE: SUMMERPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-08-01' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'August 25% DISCOUNT CODE: AUGUSTPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-10-31' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Halloween 25% DISCOUNT CODE: HALLOWEENPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-11-29' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Black Friday 20% DISCOUNT CODE: BLACKPRO20', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-12-02' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Cyber Monday 25% DISCOUNT CODE: CYBERPRO25', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
			if ( '2024-12-25' === $spammasterdateshort ) {
				$is_invitation = '<div class="notice notice-success">
					<p>
					<span class="dashicons dashicons-sticky spam-master-admin-green"></span> 
					' . esc_attr( __( 'Thank you for using Spam Master Free. If are enjoying the protection you can quickly get a PRO key with a ', 'spam-master' ) ) . ' <strong>' . esc_attr( __( 'Christmas 20% DISCOUNT CODE: XMASPRO20', 'spam-master' ) ) . '</strong> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '"><em>' . esc_attr( __( 'get pro key', 'spam-master' ) ) . '</em></a>. ' . esc_attr( __( 'Grab it Now... The offer is only valid today and this pop up will auto hide, if you decide to get it please insert your new PRO license key in the plugin', 'spam-master' ) ) . ' <a class="spam-master-admin-link-decor" href="' . esc_attr( admin_url( 'options-general.php?page=spam-master' ) ) . '" title="' . esc_attr( __( 'Settings', 'spam-master' ) ) . '"><strong><em>' . esc_attr( __( 'Settings', 'spam-master' ) ) . '</strong></em></a> ' . esc_attr( __( 'page in order to connect to our Business Class Servers and enjoy bombastic scan speeds.', 'spam-master' ) ) . '
					</p>
					</div>';
			}
		}
		return $is_invitation;
	}

}

