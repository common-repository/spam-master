<?php
/**
 * Settings page online table.
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
$spam_master_status = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_status'" );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$spam_master_type = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_type'" );
if ( 'VALID' === $spam_master_status || 'MALFUNCTION_1' === $spam_master_status || 'MALFUNCTION_2' === $spam_master_status ) {
	if ( empty( $spam_master_type ) || 'INACTIVE' === $spam_master_type || 'TRIAL' === $spam_master_type || 'FREE' === $spam_master_type ) {
		$is_card         = 'free';
		$is_icon         = 'add';
		$is_button_url   = 'https://www.techgasp.com/downloads/spam-master-license/';
		$is_button_color = 'yellow';
		$is_button_icon  = 'plus-alt';
		$is_button_text  = 'Get Pro Key';
	} else {
		if ( 'FULL' === $spam_master_type ) {
			$is_card         = 'pro';
			$is_icon         = 'view';
			$is_button_url   = '#';
			$is_button_color = 'green';
			$is_button_icon  = 'yes-alt';
			$is_button_text  = 'PRO Included';
		} else {
			$is_card         = 'free';
			$is_icon         = 'add';
			$is_button_url   = 'https://www.techgasp.com/downloads/spam-master-license/';
			$is_button_color = 'yellow';
			$is_button_icon  = 'plus-alt';
			$is_button_text  = 'Get Pro Key';
		}
	}
} else {
	$is_card         = 'free';
	$is_icon         = 'add';
	$is_button_url   = 'https://www.techgasp.com/downloads/spam-master-license/';
	$is_button_color = 'yellow';
	$is_button_icon  = 'plus-alt';
	$is_button_text  = 'Get Pro Key';
}
?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_attr( __( '&nbsp;Integrations', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class="spam-master-text-jcenter">
				<div style="display: inline-block;">
					<div class="spam-master-card spam-master-pro-card">
						<div class="spam-master-overlay"></div>
						<div class="spam-master-circle">
							<span class="dashicons dashicons-database-view spam-master-admin-f70g"></span>
						</div>
						<p><?php echo esc_attr( __( 'All protection features', 'spam-master' ) ); ?></p>
						<p><?php echo esc_attr( __( 'No extra addons', 'spam-master' ) ); ?></p>
						<p><?php echo esc_html( __( 'For small websites and blogs', 'spam-master' ) ); ?></p>
						<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Free RBL Server Cluster', 'spam-master' ) ); ?></p>
						<p><a href="#" class="btn-spammaster green roundedspam"><span class="dashicons dashicons-yes-alt"></span> <?php echo esc_attr( __( 'FREE Included', 'spam-master' ) ); ?></a></p>
					</div>
				</div>
				<div style="display: inline-block;">
					<div class="spam-master-card spam-master-<?php echo esc_attr( $is_card ); ?>-card">
						<div class="spam-master-overlay"></div>
						<div class="spam-master-circle">
							<span class="dashicons dashicons-database-<?php echo esc_attr( $is_icon ); ?> spam-master-admin-f70g"></span>
						</div>
						<p><?php echo esc_attr( __( 'Real-Time Firewall Management', 'spam-master' ) ); ?></p>
						<p><?php echo esc_html( __( '24/7 Support', 'spam-master' ) ); ?></p>
						<p><?php echo esc_attr( __( 'For heavy duty websites', 'spam-master' ) ); ?></p>
						<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Business Server Cluster', 'spam-master' ) ); ?></p>
						<p><a href="<?php echo esc_url( $is_button_url ); ?>" class="btn-spammaster <?php echo esc_attr( $is_button_color ); ?> roundedspam"><span class="dashicons dashicons-<?php echo esc_attr( $is_button_icon ); ?>"></span> <?php echo esc_attr( $is_button_text ); ?></a></p>
					</div>
				</div>
			</td>
		</tr>

	</tbody>
</table>

<div class="spam-master-pad-table"></div>
