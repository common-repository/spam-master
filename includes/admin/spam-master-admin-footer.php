<?php
/**
 * Footer segment.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
$plugin_master_name   = constant( 'SPAM_MASTER_NAME' );
$plugin_master_domain = constant( 'SPAM_MASTER_DOMAIN' );
?>
<div class="spam-master-pad-table"></div>
<p>
<a class="btn-spammaster orange roundedspam" href="https://www.spammaster.org" target="_blank" title="<?php echo esc_attr( $plugin_master_domain ); ?>"><?php echo esc_attr( $plugin_master_domain ); ?></a>
<a class="btn-spammaster orange roundedspam" href="https://www.spammaster.org/documentation/" target="_blank" title="<?php echo esc_attr( $plugin_master_domain ); ?>"><?php echo esc_attr( $plugin_master_name ); ?> <?php echo esc_attr( __( 'Documentation', 'spam-master' ) ); ?></a>
<?php
// Add Table & Load Spam Master Options.
if ( is_multisite() ) {
	$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
}
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_license_key = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_license_key'" ), 0, 64 );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_invitation_free_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_invitation_free_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_invitation_full_notice = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_invitation_full_notice'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_expires = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_expires'" );
?>
<a class="btn-spammaster orange roundedspam" href="mailto:info@spammaster.org?subject=Plugin support&body=<?php echo esc_attr( $spam_license_key ); ?> *** WRITE BELOW THIS LINE ***" target="_blank" title="<?php echo esc_attr( $plugin_master_domain ); ?>"><?php echo esc_attr( $plugin_master_name ); ?> <?php echo esc_attr( __( 'Support', 'spam-master' ) ); ?></a>
<?php
if ( empty( $spam_master_expires ) || 'EMPTY' === $spam_master_expires || '0000-00-00 00:00:00' === $spam_master_expires ) {
	$spam_master_expires = '2099-01-01 01:01:01';
}
$spam_master_current_date                = current_datetime()->format( 'Y-m-d' );
$spam_master_invitation_notice_plus_7    = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
$spam_master_invitation_notice_minus_350 = gmdate( 'Y-m-d', strtotime( '-333 days', strtotime( $spam_master_expires ) ) );

if ( 'VALID' === $spam_master_status ) {
	if ( 'FREE' === $spam_master_type ) {
		if ( $spam_master_current_date >= $spam_master_invitation_notice_plus_7 && '1' !== $spam_master_invitation_free_notice ) {
			?>
<a class="btn-spammaster green roundedspam" href="https://wordpress.org/plugins/spam-master/" target="_blank" title="<?php echo esc_attr( __( 'Rate Us on Wordpress.org', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Rate us', 'spam-master' ) ); ?> <span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span></a>
			<?php
		}
	}
	if ( 'FULL' === $spam_master_type ) {
		if ( $spam_master_current_date >= $spam_master_invitation_notice_minus_350 && '1' !== $spam_master_invitation_full_notice ) {
			?>
<a class="btn-spammaster green roundedspam" href="https://wordpress.org/plugins/spam-master/" target="_blank" title="<?php echo esc_attr( __( 'Rate Us on Wordpress.org', 'spam-master' ) ); ?>"><?php echo esc_attr( __( 'Rate us', 'spam-master' ) ); ?> <span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span></a>
			<?php
		}
	}
}
?>
</p>
