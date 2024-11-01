<?php
/**
 * Settings page offer table.
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
			<th><strong><?php echo esc_attr( __( '&nbsp;Supercharged hosting, maintenance or retainer', 'spam-master' ) ); ?></strong></th>
		</tr>
	</thead>

	<tfoot>
		<tr></tr>
	</tfoot>

	<tbody>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="TechGasp Retainer"></span> <?php echo esc_attr( __( 'We offer 100% managed hosting on Google Cloud GCE our Amazon EC2, let us host and manage all complex tasks while your website enjoys lightning fast speeds', 'spam-master' ) ); ?>.
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="TechGasp Retainer"></span> <?php echo esc_attr( __( 'Do you need server maintenance for updating, backing up, monitoring your web server. Our team of system administrators are experienced with Amazon AWS EC2, Google Cloud Platform Compute Engine, Kinsta, Cloudflare, Cloudways and many other hosting providers', 'spam-master' ) ); ?>.
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="TechGasp Retainer"></span> <?php echo esc_attr( __( 'Do you need a retainer with continuous full-stack website development without a huge technical department of skilled programmers. We will ensure that your WordPress uses the best coding standards, optimized for SEO, speed and security', 'spam-master' ) ); ?>.
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<span class="dashicons dashicons-info" title="TechGasp Retainer"></span> <?php echo esc_attr( __( 'Enjoy any package with 25% discount using coupon ', 'spam-master' ) ); ?> <strong><?php echo esc_attr( __( 'WPMASTER325', 'spam-master' ) ); ?></strong> <a href="https://www.techgasp.com" title="<?php echo esc_attr( __( 'https://www.techgasp.com', 'spam-master' ) ); ?>" target="_blank"><?php echo esc_attr( __( 'https://www.techgasp.com', 'spam-master' ) ); ?></a>.
			</td>
		</tr>
	</tbody>
</table>

<div class="spam-master-pad-table"></div>
