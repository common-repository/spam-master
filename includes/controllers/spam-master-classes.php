<?php
/**
 * Load SpamMaster extension classes.
 *
 * @package Spam Master
 */

if ( ! class_exists( 'SpamMasterActionController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteractioncontroller.php';
}
if ( ! class_exists( 'SpamMasterBufferController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterbuffercontroller.php';
}
if ( ! class_exists( 'SpamMasterElusiveController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterelusivecontroller.php';
}
if ( ! class_exists( 'SpamMasterFloodController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterfloodcontroller.php';
}
if ( ! class_exists( 'SpamMasterHAFController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterhafcontroller.php';
}
if ( ! class_exists( 'SpamMasterHoneyController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterhoneycontroller.php';
}
if ( ! class_exists( 'SpamMasterInvitationController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterinvitationcontroller.php';
}
if ( ! class_exists( 'SpamMasterLogController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterlogcontroller.php';
}
if ( ! class_exists( 'SpamMasterWhiteController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterwhitecontroller.php';
}
if ( ! class_exists( 'SpamMasterEmailController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteremailcontroller.php';
}
if ( ! class_exists( 'SpamMasterCollectController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammastercollectcontroller.php';
}
if ( ! class_exists( 'SpamMasterUserController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterusercontroller.php';
}
if ( ! class_exists( 'SpamMasterKeyController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasterkeycontroller.php';
}
if ( ! class_exists( 'SpamMasterAdminMenuTableController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradminmenutablecontroller.php';
}
if ( ! class_exists( 'SpamMasterAdminTableInactiveController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintableinactivecontroller.php';
}
if ( ! class_exists( 'SpamMasterAdminTableLogsController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablelogscontroller.php';
}
if ( ! class_exists( 'SpamMasterAdminTableWhiteController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablewhitecontroller.php';
}
if ( ! class_exists( 'SpamMasterAdminTableBufferController' ) ) {
	require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/class-spammasteradmintablebuffercontroller.php';
}
