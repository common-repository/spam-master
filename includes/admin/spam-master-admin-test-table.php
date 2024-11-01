<?php
/**
 * Settings page online table.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;
$plugin_master_name   = constant( 'SPAM_MASTER_NAME' );
$plugin_master_domain = constant( 'SPAM_MASTER_DOMAIN' );
?>

<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">
	<thead>
		<tr>
			<th><strong><?php echo esc_attr( __( '&nbsp;Test your forms submission', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tbody>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="Test Email To Be Used In Forms"></span> <?php echo esc_attr( __( 'Test your website spam submission on any form using the email', 'spam-master' ) ); ?> <strong><?php echo esc_attr( __( 'spam_email@example.com', 'spam-master' ) ); ?></strong>.
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>
