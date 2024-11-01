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
$spam_master_attached = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_attached'" );
?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_attr( __( '&nbsp;Firewall & Advanced Statistics', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th>
				<a class="btn-spammaster green roundedspam spam-master-text-center" href="https://www.spammaster.org" target="_blank" title="<?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?>">
					<?php echo esc_attr( __( 'Register & Login: ', 'spam-master' ) ); ?><strong><?php echo esc_attr( $spam_master_attached ); ?></strong>
				</a>
			</th>
		</tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="Spam Master Info"></span> <?php echo esc_attr( __( 'You can interact with your Firewall, Tools & Statistics at', 'spam-master' ) ); ?> <a href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Spam Master Website', 'spam-master' ) ); ?>" target="_blank"><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></a>. <?php echo esc_attr( __( 'Use the email attached to your key to Register & Login', 'spam-master' ) ); ?>. <span class="dashicons dashicons-email-alt" title="Email to use during Registration or Login"></span> <?php echo esc_attr( __( 'Attached Email:', 'spam-master' ) ); ?> <strong><?php echo esc_attr( $spam_master_attached ); ?></strong>. <?php echo esc_attr( __( 'You may also create an account with another email and transfer', 'spam-master' ) ); ?> <span class="dashicons dashicons-randomize" title="Transfer key to another email"></span> <?php echo esc_attr( __( 'the key to it.', 'spam-master' ) ); ?>
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>
