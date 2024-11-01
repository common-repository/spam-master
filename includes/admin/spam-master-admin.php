<?php
/**
 * Settings page.
 *
 * @package Spam Master
 */

/**
 * Settings menu.
 *
 * @return add_options_page
 */
function spam_master_settings_menu() {
	return add_options_page(
		__( 'Spam Master', 'spam-master' ),
		__( 'Spam Master', 'spam-master' ),
		'manage_options',
		'spam-master.php',
		'spam_master_admin'
	);
}

// Hook Menu.
if ( is_multisite() ) {
	add_action( 'admin_menu', 'spam_master_settings_menu' );
} else {
	add_action( 'admin_menu', 'spam_master_settings_menu' );
}

/**
 * Menu display.
 *
 * @return void
 */
function spam_master_admin() {
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
	?>
<div class="wrap">
	<h1 class="spam-master-hidden"></h1>
	<?php
	$spam_master_invitation_controller = new SpamMasterInvitationController();
	$is_invited                        = $spam_master_invitation_controller->spammasterinvitation();
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $is_invited;

	if ( ! class_exists( 'SpamMasterAdminMenuTableController' ) ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradminmenutablecontroller.php';
	}
	// Prepare Table of elements.
	$wp_list_table = new SpamMasterAdminMenuTableController();
	// Table of elements.
	$wp_list_table->display();

	// Footer.
	require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-footer.php';
	?>
</div>
	<?php
}
