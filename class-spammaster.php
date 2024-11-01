<?php
/**
 * Plugin Name: Spam Master
 * Plugin URI: https://www.spammaster.org
 * Version: 7.4.8
 * Author: TechGasp
 * Author URI: https://www.techgasp.com
 * Text Domain: spam-master
 * Description: Spam Master is the Ultimate Spam Protection plugin that blocks new user registrations and post comments with Real Time anti-spam lists.
 * License: GPL2 or later
 *
 * @package Spam Master
 */

/**
 * Copyright 2013 TechGasp  (email : info@techgasp.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SpamMaster' ) ) :

	define( 'SPAM_MASTER_VERSION', '7.4.8' );
	define( 'SPAM_MASTER_NAME', 'Spam Master' );
	define( 'SPAM_MASTER_DOMAIN', 'SpamMaster.org' );

	/**
	 * Main SpamMaster Class.
	 *
	 * @since 6.8.1
	 */
	class SpamMaster {

		/**
		 * Content with quotes.
		 *
		 * @param content $content for content_with_quote.
		 * @return content and quote
		 */
		public static function content_with_quote( $content ) {
			$quote = '<p>' . get_option( 'tsm_quote' ) . '</p>';
			return $content . $quote;
		}

		/**
		 * Spam master links in plugin page.
		 *
		 * @param links       $links for spam_master_links.
		 * @param plugin_file $plugin_file for spam_master_links.
		 *
		 * @return links
		 */
		public static function spam_master_links( $links, $plugin_file ) {
			global $wpdb;

			// Add table & load spam master options.
			if ( is_multisite() ) {
				$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			} else {
				$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			}
			$spamkey   = 'Option';
			$spamtype1 = 'spam_master_type';
			$spamtype2 = 'spam_master_expires';
			$spamtype3 = 'spam_master_protection_total_number';
			$spamtype4 = 'spam_master_block_count';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_type = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
					$spamkey,
					$spamtype1
				)
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_expires = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
					$spamkey,
					$spamtype2
				)
			);
			if ( empty( $spam_master_type ) || 'EMPTY' === $spam_master_type || 'FREE' === $spam_master_type || 'TRIAL' === $spam_master_type ) {
				$spam_master_current_date = current_datetime()->format( 'Y-m-d' );
				if ( empty( $spam_master_expires ) || 'EMPTY' === $spam_master_expires || '0000-00-00 00:00:00' === $spam_master_expires ) {
					$spam_master_expires = '2099-01-01 01:01:01';
				}
				$spam_master_invitation_notice_plus_7 = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
				if ( $spam_master_current_date >= $spam_master_invitation_notice_plus_7 ) {
					$getpro = '<a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" title="' . __( 'GET PRO KEY', 'spam-master' ) . '" target="_blank"><span class="spam-master-admin-red">' . __( 'GET PRO KEY', 'spam-master' ) . '</span></a>';
				} else {
					$getpro = '<a class="spam-master-admin-link-decor" href="https://www.spammaster.org/documentation/" title="' . __( 'Docs', 'spam-master' ) . '" target="_blank">' . __( 'Docs', 'spam-master' ) . '</a>';
				}
			} else {
				$getpro = '<span class="spam-master-admin-green">' . __( 'PRO VERSION', 'spam-master' ) . '</span>';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_protection_total_number = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
					$spamkey,
					$spamtype3
				)
			);
			if ( ! empty( $spam_master_protection_total_number ) ) {
				$format_protection_number = '<span class="spam-master-admin-green"><strong>' . __( 'Spam Master Firewall protects against ', 'spam-master' ) . number_format( $spam_master_protection_total_number ) . ' million threats and growing daily.</strong></span>';
			} else {
				$format_protection_number = '<span class="spam-master-admin-green"><strong>Real-time Firewall.</strong></span>';
			}
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$spam_master_block_count = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
					$spamkey,
					$spamtype4
				)
			);
			if ( ! empty( $spam_master_block_count ) && $spam_master_block_count >= 1 ) {
				$spam_master_collect_controller = new SpamMasterCollectController();
				$is_collect_number              = $spam_master_collect_controller->spammastergetnumber( $spam_master_block_count );
			} else {
				$is_collect_number = false;
			}

			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				if ( is_network_admin() ) {
					$techgasp_plugin_url = network_admin_url( 'options-general.php?page=spam-master' );
				} else {
					$techgasp_plugin_url = admin_url( 'options-general.php?page=spam-master' );
				}
				$links[] = '<a href="' . $techgasp_plugin_url . '">' . __( 'Settings', 'spam-master' ) . '</a>';
				$links[] = $getpro;
				$links[] = $format_protection_number . $is_collect_number;
			}
			return $links;
		}

	}

	add_filter( 'the_content', array( 'SpamMaster', 'content_with_quote' ) );
	add_filter( 'plugin_action_links', array( 'SpamMaster', 'spam_master_links' ), 10, 2 );

endif;

// First time installs add settings wide options.
global $wpdb, $blog_id;

// Hook classes.
require_once WP_PLUGIN_DIR . '/spam-master/includes/controllers/spam-master-classes.php';

// Load signup styles if multi.
add_action( 'signup_extra_fields', 'spam_master_css' );
// Load login styles.
add_action( 'login_enqueue_scripts', 'spam_master_css' );
// Load admin styles.
add_action( 'admin_enqueue_scripts', 'spam_master_css' );
add_action( 'wp_enqueue_scripts', 'spam_master_css' );
/**
 * Load admin styles. Load signup styles. Load login styles.
 *
 * @return void
 */
function spam_master_css() {
	$spam_master_version = constant( 'SPAM_MASTER_VERSION' );
	wp_register_style( 'spam-master', plugins_url( 'css/spam-master.css', __FILE__ ), array(), $spam_master_version );
	wp_enqueue_style( 'spam-master' );
}
// End css.

// Database version.
global $spam_master_keys_db_version;
$spam_master_keys_db_version = '2.4';

/**
 * Database hooks.
 *
 * @return void
 */
function spam_master_keys_db_table() {
	global $wpdb, $blog_id, $spam_master_keys_db_version;
	if ( is_multisite() ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
		foreach ( $blogs as $id ) {
			$techgasp_keys_db_installed_ver = get_blog_option( $id, 'spam_master_keys_db_version' );
			if ( $techgasp_keys_db_installed_ver !== $spam_master_keys_db_version ) {
				$table_name      = $wpdb->get_blog_prefix( $id ) . 'spam_master_keys';
				$charset_collate = $wpdb->get_charset_collate();
				$sql             = "CREATE TABLE $table_name (
						id INT NOT NULL AUTO_INCREMENT,
						time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						spamkey VARCHAR( 64 ) NOT NULL,
						spamtype LONGTEXT NOT NULL,
						spamy LONGTEXT NOT NULL,
						spamvalue LONGTEXT NOT NULL,
						PRIMARY KEY (id)
						) $charset_collate; ";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				update_blog_option( $id, 'spam_master_keys_db_version', $spam_master_keys_db_version );
			}
		}
	} else {
		$techgasp_keys_db_installed_ver = get_option( 'spam_master_keys_db_version' );
		if ( $techgasp_keys_db_installed_ver !== $spam_master_keys_db_version ) {
			$table_name      = $wpdb->prefix . 'spam_master_keys';
			$charset_collate = $wpdb->get_charset_collate();
				$sql         = "CREATE TABLE $table_name (
						id INT NOT NULL AUTO_INCREMENT,
						time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						spamkey VARCHAR( 64 ) NOT NULL,
						spamtype LONGTEXT NOT NULL,
						spamy LONGTEXT NOT NULL,
						spamvalue LONGTEXT NOT NULL,
						PRIMARY KEY (id)
						) $charset_collate; ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
			update_option( 'spam_master_keys_db_version', $spam_master_keys_db_version );
		}
	}
}
register_activation_hook( __FILE__, 'spam_master_keys_db_table' );

/**
 * Upgrade activate to.
 *
 * @return void
 */
function spam_master_activate_upgrade() {
	global $wpdb, $spam_master_keys_db_version;

	if ( is_multisite() ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
		foreach ( $blogs as $id ) {
			$spam_master_keys_db_installed_ver = get_blog_option( $id, 'spam_master_keys_db_version' );

			$spam_master_upgrade_to_6      = get_blog_option( $id, 'spam_master_upgrade_to_6' );
			$spam_master_upgrade_to_6_6_0  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_0' );
			$spam_master_upgrade_to_6_6_1  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_1' );
			$spam_master_upgrade_to_6_6_2  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_2' );
			$spam_master_upgrade_to_6_6_3  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_3' );
			$spam_master_upgrade_to_6_6_5  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_5' );
			$spam_master_upgrade_to_6_6_6  = get_blog_option( $id, 'spam_master_upgrade_to_6_6_6' );
			$spam_master_upgrade_to_6_6_19 = get_blog_option( $id, 'spam_master_upgrade_to_6_6_19' );
			$spam_master_upgrade_to_6_7_0  = get_blog_option( $id, 'spam_master_upgrade_to_6_7_0' );
			$spam_master_upgrade_to_6_7_2  = get_blog_option( $id, 'spam_master_upgrade_to_6_7_2' );
			$spam_master_upgrade_to_6_7_6  = get_blog_option( $id, 'spam_master_upgrade_to_6_7_6' );
			$spam_master_upgrade_to_6_8_5  = get_blog_option( $id, 'spam_master_upgrade_to_6_8_5' );
			$spam_master_upgrade_to_6_8_6  = get_blog_option( $id, 'spam_master_upgrade_to_6_8_6' );
			$spam_master_upgrade_to_6_8_7  = get_blog_option( $id, 'spam_master_upgrade_to_6_8_7' );
			$spam_master_upgrade_to_6_9_8  = get_blog_option( $id, 'spam_master_upgrade_to_6_9_8' );
			$spam_master_upgrade_to_7_1_1  = get_blog_option( $id, 'spam_master_upgrade_to_7_1_1' );
			$spam_master_upgrade_to_7_1_2  = get_blog_option( $id, 'spam_master_upgrade_to_7_1_2' );
			$spam_master_upgrade_to_7_2_7  = get_blog_option( $id, 'spam_master_upgrade_to_7_2_7' );
			$spam_master_upgrade_to_7_2_8  = get_blog_option( $id, 'spam_master_upgrade_to_7_2_8' );
			$spam_master_upgrade_to_7_2_9  = get_blog_option( $id, 'spam_master_upgrade_to_7_2_9' );
			$spam_master_upgrade_to_7_3_1  = get_blog_option( $id, 'spam_master_upgrade_to_7_3_1' );
			$spam_master_upgrade_to_7_3_2  = get_blog_option( $id, 'spam_master_upgrade_to_7_3_2' );
			$spam_master_upgrade_to_7_3_6  = get_blog_option( $id, 'spam_master_upgrade_to_7_3_6' );
			$spam_master_upgrade_to_7_3_7  = get_blog_option( $id, 'spam_master_upgrade_to_7_3_7' );
			$spam_master_upgrade_to_7_4_0  = get_blog_option( $id, 'spam_master_upgrade_to_7_4_0' );
			$spam_master_upgrade_to_7_4_1  = get_blog_option( $id, 'spam_master_upgrade_to_7_4_1' );
			$spam_master_upgrade_to_7_4_5  = get_blog_option( $id, 'spam_master_upgrade_to_7_4_5' );
			$spam_master_upgrade_to_7_4_6  = get_blog_option( $id, 'spam_master_upgrade_to_7_4_6' );

			$spam_master_connection = get_blog_option( $id, 'spam_master_connection' );
		}
	} else {
		$spam_master_keys_db_installed_ver = get_option( 'spam_master_keys_db_version' );

		$spam_master_upgrade_to_6      = get_option( 'spam_master_upgrade_to_6' );
		$spam_master_upgrade_to_6_6_0  = get_option( 'spam_master_upgrade_to_6_6_0' );
		$spam_master_upgrade_to_6_6_1  = get_option( 'spam_master_upgrade_to_6_6_1' );
		$spam_master_upgrade_to_6_6_2  = get_option( 'spam_master_upgrade_to_6_6_2' );
		$spam_master_upgrade_to_6_6_3  = get_option( 'spam_master_upgrade_to_6_6_3' );
		$spam_master_upgrade_to_6_6_5  = get_option( 'spam_master_upgrade_to_6_6_5' );
		$spam_master_upgrade_to_6_6_6  = get_option( 'spam_master_upgrade_to_6_6_6' );
		$spam_master_upgrade_to_6_6_19 = get_option( 'spam_master_upgrade_to_6_6_19' );
		$spam_master_upgrade_to_6_7_0  = get_option( 'spam_master_upgrade_to_6_7_0' );
		$spam_master_upgrade_to_6_7_2  = get_option( 'spam_master_upgrade_to_6_7_2' );
		$spam_master_upgrade_to_6_7_6  = get_option( 'spam_master_upgrade_to_6_7_6' );
		$spam_master_upgrade_to_6_8_5  = get_option( 'spam_master_upgrade_to_6_8_5' );
		$spam_master_upgrade_to_6_8_6  = get_option( 'spam_master_upgrade_to_6_8_6' );
		$spam_master_upgrade_to_6_8_7  = get_option( 'spam_master_upgrade_to_6_8_7' );
		$spam_master_upgrade_to_6_9_8  = get_option( 'spam_master_upgrade_to_6_9_8' );
		$spam_master_upgrade_to_7_1_1  = get_option( 'spam_master_upgrade_to_7_1_1' );
		$spam_master_upgrade_to_7_1_2  = get_option( 'spam_master_upgrade_to_7_1_2' );
		$spam_master_upgrade_to_7_2_7  = get_option( 'spam_master_upgrade_to_7_2_7' );
		$spam_master_upgrade_to_7_2_8  = get_option( 'spam_master_upgrade_to_7_2_8' );
		$spam_master_upgrade_to_7_2_9  = get_option( 'spam_master_upgrade_to_7_2_9' );
		$spam_master_upgrade_to_7_3_1  = get_option( 'spam_master_upgrade_to_7_3_1' );
		$spam_master_upgrade_to_7_3_2  = get_option( 'spam_master_upgrade_to_7_3_2' );
		$spam_master_upgrade_to_7_3_6  = get_option( 'spam_master_upgrade_to_7_3_6' );
		$spam_master_upgrade_to_7_3_7  = get_option( 'spam_master_upgrade_to_7_3_7' );
		$spam_master_upgrade_to_7_4_0  = get_option( 'spam_master_upgrade_to_7_4_0' );
		$spam_master_upgrade_to_7_4_1  = get_option( 'spam_master_upgrade_to_7_4_1' );
		$spam_master_upgrade_to_7_4_5  = get_option( 'spam_master_upgrade_to_7_4_5' );
		$spam_master_upgrade_to_7_4_6  = get_option( 'spam_master_upgrade_to_7_4_6' );

		$spam_master_connection = get_option( 'spam_master_connection' );
	}
	// Databases.
	if ( $spam_master_keys_db_installed_ver !== $spam_master_keys_db_version ) {
		spam_master_keys_db_table();
	}
	// File to upgrade legacy versions inferior to 6.
	if ( '1' !== $spam_master_upgrade_to_6 ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6.php';
	}
	// Files to upgrade to current latest stable.
	if ( '1' !== $spam_master_upgrade_to_6_6_0 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-0.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_1 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-1.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_2 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-2.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_3 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-3.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_5 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-5.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_6 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-6.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_6_19 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-6-19.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_7_0 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-7-0.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_7_2 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-7-2.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_7_6 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-7-6.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_8_5 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-8-5.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_8_6 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-8-6.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_8_7 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-8-7.php';
	}
	if ( '1' !== $spam_master_upgrade_to_6_9_8 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-6-9-8.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_1_1 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-1-1.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_1_2 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-1-2.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_2_7 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-2-7.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_2_8 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-2-8.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_2_9 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-2-9.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_3_1 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-3-1.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_3_2 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-3-2.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_3_6 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-3-6.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_3_7 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-3-7.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_4_0 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-4-0.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_4_1 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-4-1.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_4_5 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-4-5.php';
	}
	if ( '1' !== $spam_master_upgrade_to_7_4_6 && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/upgrade/spam-master-admin-upgrade-to-7-4-6.php';
	}

	// Hook connection.
	if ( ! isset( $spam_master_connection ) || empty( $spam_master_connection ) && '2.4' === $spam_master_keys_db_installed_ver ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-connection-sender.php';
	}
}
add_action( 'plugins_loaded', 'spam_master_activate_upgrade' );

// Hook admin and settings.
require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin.php';

// Add table & load spam master options.
if ( is_multisite() ) {
	$spam_master_keys_db_installed_ver = get_blog_option( $blog_id, 'spam_master_keys_db_version' );
	$spam_master_keys                  = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
} else {
	$spam_master_keys_db_installed_ver = get_option( 'spam_master_keys_db_version' );
	$spam_master_keys                  = $wpdb->prefix . 'spam_master_keys';
}
// Is it up to date.
if ( $spam_master_keys_db_installed_ver === $spam_master_keys_db_version ) {
	$spamkey   = 'Option';
	$spamtype1 = 'spam_master_status';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_status = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype1
		)
	);
	$spamtype2 = 'spam_master_type';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_type = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype2
		)
	);
	$spamtype3 = 'spam_master_honeypot_timetrap';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_honeypot_timetrap = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype3
		)
	);
	$spamtype4 = 'spam_master_widget_heads_up';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_heads_up = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype4
		)
	);
	$spamtype5 = 'spam_master_widget_statistics';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_statistics = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype5
		)
	);
	$spamtype6 = 'spam_master_widget_firewall';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_firewall = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype6
		)
	);
	$spamtype7 = 'spam_master_widget_dashboard_status';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_dashboard_status = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype7
		)
	);
	$spamtype8 = 'spam_master_widget_dashboard_statistics';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_dashboard_statistics = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype8
		)
	);
	$spamtype9 = 'spam_master_shortcodes_total_count';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_shortcodes_total_count = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype9
		)
	);
	$spamtype10 = 'spam_master_integrations_contact_form_7';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_integrations_contact_form_7 = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype10
		)
	);
	$spamtype12 = 'spam_master_widget_top_menu_firewall';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_widget_top_menu_firewall = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype12
		)
	);
	$spamtype13 = 'spam_master_auto_update';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_auto_update = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype13
		)
	);
	$spamtype14 = 'spam_master_expires';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_expires = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype14
		)
	);
	$spamtype15 = 'spam_master_emails_weekly_email';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_emails_weekly_email = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype15
		)
	);
	$spamtype16 = 'spam_master_trial_expired_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_trial_expired_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype16
		)
	);
	$spamtype17 = 'spam_master_trial_expired_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_trial_expired_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype17
		)
	);
	$spamtype18 = 'spam_master_free_expired_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_free_expired_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype18
		)
	);
	$spamtype19 = 'spam_master_free_expired_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_free_expired_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype19
		)
	);
	$spamtype20 = 'spam_master_full_expired_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_full_expired_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype20
		)
	);
	$spamtype21 = 'spam_master_full_expired_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_full_expired_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype21
		)
	);
	$spamtype22 = 'spam_master_free_rate_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_free_rate_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype22
		)
	);
	$spamtype23 = 'spam_master_full_install_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_full_install_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype23
		)
	);
	$spamtype24 = 'spam_master_free_unstable_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_free_unstable_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype24
		)
	);
	$spamtype25 = 'spam_master_free_unstable_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_free_unstable_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype25
		)
	);
	$spamtype26 = 'spam_master_full_inactive_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_full_inactive_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype26
		)
	);
	$spamtype27 = 'spam_master_full_inactive_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_full_inactive_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype27
		)
	);
	$spamtype28 = 'spam_master_malfunction_1_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_1_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype28
		)
	);
	$spamtype29 = 'spam_master_malfunction_1_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_1_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype29
		)
	);
	$spamtype30 = 'spam_master_malfunction_2_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_2_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype30
		)
	);
	$spamtype31 = 'spam_master_malfunction_2_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_2_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype31
		)
	);
	$spamtype32 = 'spam_master_malfunction_6_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_6_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype32
		)
	);
	$spamtype33 = 'spam_master_malfunction_6_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_6_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype33
		)
	);
	$spamtype33c = 'spam_master_malfunction_8_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_8_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype33c
		)
	);
	$spamtype33d = 'spam_master_malfunction_8_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_malfunction_8_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype33d
		)
	);
	$spamtype34 = 'spam_master_high_volume_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_high_volume_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype34
		)
	);
	$spamtype35 = 'spam_master_high_volume_notice';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_high_volume_notice = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype35
		)
	);
	$spamtype36 = 'spam_master_license_sync_date';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_license_sync_date = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype36
		)
	);
	$spamtype37 = 'spam_master_license_sync_run';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_license_sync_run = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype37
		)
	);
	$spamtype38 = 'spam_master_emails_alert_3_email';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_emails_alert_3_email = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype38
		)
	);
	$spamtype39 = 'spam_master_emails_alert_email';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_emails_alert_email = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype39
		)
	);
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
	$spamtype42 = 'spam_master_firewall_rules';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$spam_master_firewall_rules = $wpdb->get_var(
		$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
			$spamkey,
			$spamtype42
		)
	);

	// Hook learning firewall.
	require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-firewall.php';
	// Hook learning honey.
	if ( 'true' === $spam_master_honeypot_timetrap ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-honeypot.php';
	}
	// Hook learning action.
	require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-action.php';
	// Hook signatures.
	require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-signatures.php';
	// Hook widgets and shortcodes.
	if ( 'true' === $spam_master_widget_heads_up ) {
		$future = false;
	}
	if ( 'true' === $spam_master_widget_statistics ) {
		$future = false;
	}
	if ( 'true' === $spam_master_widget_firewall ) {
		$future = false;
	}
	if ( 'true' === $spam_master_widget_dashboard_status ) {
		$future = false;
	}
	if ( 'true' === $spam_master_widget_dashboard_statistics ) {
		$future = false;
	}
	if ( 'true' === $spam_master_shortcodes_total_count ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-shortcodes.php';
	}
	if ( class_exists( 'WPCF7' ) ) {
		if ( 'true' === $spam_master_integrations_contact_form_7 ) {
			require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-contact-form-7-honey.php';
		}
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-contact-form-7-sig.php';
	}
	/**
	 * Check for wpforms.
	 *
	 * @return void
	 */
	function spam_master_wpforms_loaded() {
		global $wpdb;

		// Add table & load spam master options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_integrations_wpforms = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				'Option',
				'spam_master_integrations_wpforms',
			)
		);
		if ( 'true' === $spam_master_integrations_wpforms ) {
			require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-wpforms-honey.php';
		}
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-wpforms-sig.php';

	}
	add_action( 'wpforms_loaded', 'spam_master_wpforms_loaded' );
	/**
	 * Check for woocommerce.
	 *
	 * @return void
	 */
	function spam_master_woocommerce_loaded() {
		global $wpdb;

		// Add table & load spam master options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_integrations_woocommerce = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				'Option',
				'spam_master_integrations_woocommerce',
			)
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_firewall_rules = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				'Option',
				'spam_master_firewall_rules',
			)
		);
		if ( 'true' === $spam_master_integrations_woocommerce ) {
			require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-woocommerce-honey.php';
		}
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-woocommerce-sig.php';
	}
	add_action( 'woocommerce_loaded', 'spam_master_woocommerce_loaded' );
	/**
	 * Check for buddypress.
	 *
	 * @return void
	 */
	function spam_master_buddypress_loaded() {
		global $wpdb;

		// Add table & load spam master options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_integrations_buddypress = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				'Option',
				'spam_master_integrations_buddypress',
			)
		);
		if ( 'true' === $spam_master_integrations_buddypress ) {
			require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-buddypress-honey.php';
		}
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-buddypress-sig.php';
	}
	add_action( 'bp_include', 'spam_master_buddypress_loaded' );
	require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-widget-top-menu-firewall.php';
	if ( 'true' === $spam_master_auto_update ) {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/protection/spam-master-auto-update.php';
	}

	/**
	 * Admin notices.
	 *
	 * @return void
	 */
	function spam_master_admin_notices() {
		global $wpdb, $blog_id;

		// Add table & load spam master options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$admin_email      = get_blog_option( $blog_id, 'admin_email' );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$admin_email      = get_option( 'admin_email' );
		}
		$spamkey   = 'Option';
		$spamtype1 = 'spam_master_status';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_status = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype1
			)
		);
		$spamtype2 = 'spam_master_type';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_type = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype2
			)
		);
		$spamtype3 = 'spam_master_invitation_full_wide_notice';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_invitation_full_wide_notice = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype3
			)
		);
		$spamtype4 = 'spam_master_invitation_free_wide_notice';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_invitation_free_wide_notice = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype4
			)
		);
		$spamtype5 = 'spam_master_expires';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_expires = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype5
			)
		);
		$spamtype6 = 'spam_master_attached';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_master_attached = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype6
			)
		);
		$spam_master_current_date                = current_datetime()->format( 'Y-m-d' );
		$spam_master_invitation_notice_plus_7    = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
		$spam_master_invitation_notice_minus_333 = gmdate( 'Y-m-d', strtotime( '-333 days', strtotime( $spam_master_expires ) ) );
		// Courtesy Link.
		if ( empty( $spam_master_type ) || 'EMPTY' === $spam_master_type ) {
			/**
			 * Courtesy link.
			 *
			 * @param default $default for screen.
			 *
			 * @return void
			 */
			function spam_master_footer_empty_admin( $default ) {
				$screen = get_current_screen();
				if ( in_array( $screen->id, array( 'settings_page_spam-master' ), true ) ) {
					?>
					<span id="footer-thankyou"><?php echo esc_attr( __( 'Thank you for using', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?>" target="_blank"><?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?></a>. <?php echo esc_attr( __( 'Click Generate Connection Key to automatically start your protection.', 'spam-master' ) ); ?></span>
					<?php
				}
			}
			add_filter( 'admin_footer_text', 'spam_master_footer_empty_admin' );
		}
		if ( 'VALID' === $spam_master_status ) {
			/**
			 * Courtesy link.
			 *
			 * @param default $default for screen.
			 *
			 * @return void
			 */
			function spam_master_footer_courtesy( $default ) {
				$screen = get_current_screen();
				if ( in_array( $screen->id, array( 'settings_page_spam-master' ), true ) ) {
					?>
					<span id="footer-thankyou"><?php echo esc_attr( __( 'Please Rate', 'spam-master' ) ); ?> <strong><?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?></strong> <a class="spam-master-admin-link-decor" href="https://www.wordpress.org/plugins/spam-master/" title="<?php echo esc_attr( __( 'Rate Us on Wordpress.org', 'spam-master' ) ); ?>" target="_blank"><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span></a> <?php echo esc_attr( __( 'on', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.wordpress.org/plugins/spam-master/" title="<?php echo esc_attr( __( 'Rate Us on Wordpress.org', 'spam-master' ) ); ?>" target="_blank"><strong><?php echo esc_attr( __( 'Wordpress.org', 'spam-master' ) ); ?></strong></a> <?php echo esc_attr( __( 'to help us spread the word.', 'spam-master' ) ); ?></span>
					<?php
				}
			}
			add_filter( 'admin_footer_text', 'spam_master_footer_courtesy' );
		}

		// Status valid.
		if ( 'VALID' === $spam_master_status ) {
			$spam_master_screen = get_current_screen();
			if ( 'settings_page_spam-master' !== $spam_master_screen->id ) {
				if ( isset( $_SERVER['REQUEST_SCHEME'] ) && isset( $_SERVER['SERVER_NAME'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
					$path = esc_url_raw( wp_unslash( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ) );
				} else {
					$path = false;
				}
				$current_url = wp_nonce_url( $path, 'spammasterdisnonce' );
				if ( 'FREE' === $spam_master_type ) {
					if ( $spam_master_current_date >= $spam_master_invitation_notice_plus_7 && '1' !== $spam_master_invitation_free_wide_notice ) {
						?>
						<div class="notice notice-success">
						<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Thank you for using Spam Master Free for some time now. We humbly ask you to take a few minutes to let us know what you think and rate us ', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="<?php echo esc_attr( __( 'Let us know what you think, we value your input.', 'spam-master' ) ); ?>" target="_blank"><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span> <?php echo esc_attr( __( 'wordpress.org', 'spam-master' ) ); ?></a> <a class="spam-master-admin-link-decor" href="<?php echo esc_url( $current_url . '&spammasterdisfrwide=1' ); ?>"><span class="dashicons dashicons-dismiss spam-master-top-admin-f-red spam-master-top-admin-shadow-orange spam-master-admin-float-r" title="<?php echo esc_attr( __( 'Dismiss', 'spam-master' ) ); ?>"></span></a></p>
						</div> 
						<?php
					}
					// Disc not codes.
					if ( 'FREE' === $spam_master_type ) {
						$spammasterdateshort               = $spam_master_current_date;
						$spam_master_invitation_controller = new SpamMasterInvitationController();
						$is_invitation                     = $spam_master_invitation_controller->spammasteradminnot( $spammasterdateshort );
						if ( ! empty( $is_invitation ) ) {
							$spam_master_html = array(
								'div'  => array(
									'class' => array(),
								),
								'span' => array(
									'class' => array(),
								),
								'a'    => array(
									'href'   => array(),
									'target' => array(),
								),
							);
							echo wp_kses( $is_invitation, $spam_master_html );
						}
					}
				}
				if ( 'FULL' === $spam_master_type ) {
					if ( $spam_master_current_date >= $spam_master_invitation_notice_minus_333 && '1' !== $spam_master_invitation_full_wide_notice ) {
						?>
						<div class="notice notice-success">
						<p><span class="dashicons dashicons-admin-post"></span> <?php echo esc_attr( __( 'Thank you for using Spam Master Pro for some time now. If you haven\'t done so, we humbly ask you to take a few minutes to let us know what you think and rate us ', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://wordpress.org/plugins/spam-master/" title="<?php echo esc_attr( __( 'Let us know what you think, we value your input.', 'spam-master' ) ); ?>" target="_blank"><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span><span class="dashicons dashicons-star-filled spam-master-top-admin-f-yellow spam-master-top-admin-shadow-orangina"></span> <?php echo esc_attr( __( 'wordpress.org', 'spam-master' ) ); ?></a> <a class="spam-master-admin-link-decor" href="<?php echo esc_url( $current_url . '&spammasterdisfuwide=1' ); ?>"><span class="dashicons dashicons-dismiss spam-master-top-admin-f-red spam-master-top-admin-shadow-orange spam-master-admin-float-r" title="<?php echo esc_attr( __( 'Dismiss', 'spam-master' ) ); ?>"></span></a></p>
						</div>
						<?php
					}
				}
			}
		}
		// Status malfunction 1.
		if ( 'MALFUNCTION_1' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Update Spam Master plugin to latest version!!!', 'spam-master' ) ); ?></strong></p>
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 1. Please Update Spam Master', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'Your Key is Valid and your Protection is Active & Online, not to worry. Please update, upgrade Spam Master to the latest available version in your plugins administrator page. Once Spam Master is up-to-date press RE-SYNCHRONIZE CONNECTION button in Spam Master', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a>.</p>
			<p><strong><?php echo esc_attr( __( 'You can also activate automatic plugin updates in the administrator plugins page.', 'spam-master' ) ); ?></strong></p>
			</div>
			<?php
		}
		// Status malfunction 2.
		if ( 'MALFUNCTION_2' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 2!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'You are still protected but you are using the same license key in more than one website. Your Connection Key might get UNSTABLE or with a MALFUNCTION that will affect all websites. Go to', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?>" target="_blank"><strong><em><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'register or login with', 'spam-master' ) ); ?> <strong><?php echo esc_attr( $spam_master_attached ); ?></strong>, <?php echo esc_attr( __( 'go to the licenses page and detach all websites using this key except for one, create more unique keys to be used by other websites. One key per website.', 'spam-master' ) ); ?> <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status malfunction 3.
		if ( 'MALFUNCTION_3' === $spam_master_status ) {
			?>
			<div class="notice notice-error is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 3', 'spam-master' ) ); ?></strong> <?php echo esc_attr( __( 'Warning!!! Your Key is', 'spam-master' ) ); ?> <strong><em><?php echo esc_attr( __( 'INACTIVE & OFFLINE', 'spam-master' ) ); ?></em></strong>, <?php echo esc_attr( __( 'Malfunction 3 was detected. More about malfunction 3', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org/documentation/" target="_blank" title="<?php echo esc_attr( __( 'more about malfunction 3', 'spam-master' ) ); ?>"><em><?php echo esc_attr( __( 'click here', 'spam-master' ) ); ?></em></a>. <?php echo esc_attr( __( 'Please get in touch with us via', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/support/" target="_blank" title="<?php echo esc_attr( __( 'Support Ticket', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Support Ticket', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'and refer malfunction 3.', 'spam-master' ) ); ?></p>
			</div>
			<?php
			// Update alert levels.
			$data_spam1  = array( 'spamvalue' => '' );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
		}
		// Status malfunction 4.
		if ( 'MALFUNCTION_4' === $spam_master_status ) {
			if ( empty( $admin_email ) ) {
				$admin_email = 'empty';
			}
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 4!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'Not able to automatically Generate a FREE Connection Key on your server, most likely reason:', 'spam-master' ) ); ?>
			<br><?php echo esc_attr( __( 'Your Settings > General > Administration Email Address:', 'spam-master' ) ); ?> <strong><?php echo esc_attr( $admin_email ); ?></strong>. <?php echo esc_attr( __( 'It was either empty or already in use. Not to worry, you can check the connection key in use by this email or get a new connection key at', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Free Connection', 'spam-master' ) ); ?>" target="_blank"><strong><em><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></strong></em></a>.
			<br><?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status malfunction 5.
		if ( 'MALFUNCTION_5' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 5!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'Not able to Generate a FREE Connection Key because the daily limit of free keys was exceeded. Please try again tomorrow or', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_attr( __( 'get pro key', 'spam-master' ) ); ?>"><em><?php echo esc_attr( __( 'get pro key', 'spam-master' ) ); ?></em></a>. <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status malfunction 6.
		if ( 'MALFUNCTION_6' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 6!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'Not able to connect to the online RBL servers with that key. Key already use in another website. Please visit', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Check Keys', 'spam-master' ) ); ?>" target="_blank"><strong><em><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'to check your keys or get a new key.', 'spam-master' ) ); ?> <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status malfunction 7.
		if ( 'MALFUNCTION_7' === $spam_master_status ) {
			if ( empty( $admin_email ) ) {
				$admin_email = 'empty';
			}
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 7!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'Not able to automatically Generate a FREE Connection Key on your server, most likely reason:', 'spam-master' ) ); ?> 
			<br><?php echo esc_attr( __( 'Your Spam Master installed version is out of date, simply update the plugin to the latest version in your plugins page and get a new connection key at', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Free Connection', 'spam-master' ) ); ?>" target="_blank"><strong><em><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></strong></em></a>.
			<br><?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status malfunction 8.
		if ( 'MALFUNCTION_8' === $spam_master_status ) {
			if ( empty( $admin_email ) ) {
				$admin_email = 'empty';
			}
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Malfunction 8!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'We have detected CDN WAF that masks the end users IP addresses with CDN, WAF Ips. In Spam Master Settings -> Protection Tools tab activate CDN. Please read Spam Master online documentation to solve the issue.', 'spam-master' ) ); ?> 
			</div>
			<?php
		}
		// Status unstable.
		if ( 'UNSTABLE' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p><strong><?php echo esc_attr( __( 'Spam Master Free RBL Server connection is Unstable. You are not Protected!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'We apologize for that, there\'s probably a high demand of spam checks in our free servers at this point, please check the RBL server status', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" title="<?php echo esc_attr( __( 'Free Server Cluster Status', 'spam-master' ) ); ?>" target="_blank"><strong><em><?php echo esc_attr( __( 'here', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'and wait 4 to 24 hours in order for the free service auto regain stability. If you want to avoid these issues in the future with 100% up-time spam checks you might want to consider a', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" title="<?php echo esc_attr( __( 'Pro Connection Key', 'spam-master' ) ); ?>" target="_blank"><span class="dashicons dashicons-database-add spam-master-top-admin-f-green"></span> <strong><em><?php echo esc_attr( __( 'Pro Connection Key', 'spam-master' ) ); ?></strong></em></a>... <?php echo esc_attr( __( 'It costs peanuts.', 'spam-master' ) ); ?> <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status high volume.
		if ( 'HIGH_VOLUME' === $spam_master_status ) {
			?>
			<div class="notice notice-warning is-dismissible">
			<p>
				<?php echo esc_attr( __( 'Spam Master detected ', 'spam-master' ) ); ?> 
				<strong><?php echo esc_attr( __( 'High Volume ', 'spam-master' ) ); ?></strong>
				<?php echo esc_attr( __( 'of spam checks from your website and you may be ', 'spam-master' ) ); ?>
				<strong><?php echo esc_attr( __( 'at risk ', 'spam-master' ) ); ?></strong>
				<?php echo esc_attr( __( 'using a free key. You are not Protected!!!  Please wait 4 to 24 hours in order for your free spam checks count decrease or consider a ', 'spam-master' ) ); ?>
				<a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" title="<?php echo esc_attr( __( 'Pro Connection Key', 'spam-master' ) ); ?>" target="_blank"><span class="dashicons dashicons-database-add spam-master-top-admin-f-green"></span> <strong><em><?php echo esc_attr( __( 'Pro Connection Key', 'spam-master' ) ); ?></strong></em></a>... <?php echo esc_attr( __( 'It costs peanuts.', 'spam-master' ) ); ?> <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
		// Status expired.
		if ( 'EXPIRED' === $spam_master_status ) {
			if ( 'FREE' === $spam_master_type ) {
				?>
				<div class="notice notice-error is-dismissible">
				<p><strong><?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?></strong> <?php echo esc_attr( __( 'Warning!!! Your Free Key', 'spam-master' ) ); ?> <strong><em><?php echo esc_attr( __( 'EXPIRED', 'spam-master' ) ); ?></em></strong>. <?php echo esc_attr( __( 'You can get a FREE connection key at', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.spammaster.org" target="_blank" title="<?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?>"><em><?php echo esc_attr( __( 'www.spammaster.org', 'spam-master' ) ); ?></em></a>. <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
				</div>
				<?php
			}
			if ( 'FULL' === $spam_master_type ) {
				?>
				<div class="notice notice-error is-dismissible">
				<p><strong><?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?></strong> <?php echo esc_attr( __( 'Warning!!! Your Key', 'spam-master' ) ); ?><strong><em><?php echo esc_attr( __( 'EXPIRED', 'spam-master' ) ); ?></em></strong>. <?php echo esc_attr( __( 'Hope you have enjoyed 1 year of bombastic spam protection provided by Spam Master. Your website is now unprotected and may be subjected to thousands of spam threats & exploits. Not to worry! If you enjoyed the protection you can quickly get another key, it costs peanuts per year,', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="https://www.techgasp.com/downloads/spam-master-license/" target="_blank" title="<?php echo esc_attr( __( 'get pro key', 'spam-master' ) ); ?>"><em><?php echo esc_attr( __( 'get pro key', 'spam-master' ) ); ?></em></a>. <?php echo esc_attr( __( 'For more info please visit the plugin', 'spam-master' ) ); ?> <a class="spam-master-admin-link-decor" href="<?php echo esc_attr( admin_url( 'options-general.php?page=spam-master' ) ); ?>" title="<?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?>"><strong><em><?php echo esc_attr( __( 'Settings', 'spam-master' ) ); ?></strong></em></a> <?php echo esc_attr( __( 'page.', 'spam-master' ) ); ?></p>
				</div>
				<?php
			}
			// Update alert levels.
			$data_spam1  = array( 'spamvalue' => '' );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
		}
		// Status inactive, no key sent.
		if ( 'INACTIVE' === $spam_master_status ) {
			$spam_master_screen = get_current_screen();
			if ( 'settings_page_spam-master' !== $spam_master_screen->id ) {
				if ( 'TRIAL' === $spam_master_type || 'FREE' === $spam_master_type || 'FULL' === $spam_master_type ) {
					?>
					<div class="notice notice-error is-dismissible">
					<p><strong><?php echo esc_attr( __( 'Spam Master', 'spam-master' ) ); ?></strong> <?php echo esc_attr( __( 'Warning!!! Your Key is', 'spam-master' ) ); ?> <strong><?php echo esc_attr( __( 'INACTIVE & OFFLINE!!!', 'spam-master' ) ); ?></strong>. <?php echo esc_attr( __( 'You haven\'t updated, upgraded Spam Master "for a very long time". Not to worry, please update Spam Master to the latest version and re-activate your connection', 'spam-master' ) ); ?>.</p>
					</div>
					<?php
				}
			}
			// Update alert levels.
			$data_spam1  = array( 'spamvalue' => '' );
			$where_spam1 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam1, $where_spam1 );
			$data_spam2  = array( 'spamvalue' => '' );
			$where_spam2 = array(
				'spamkey'  => 'Option',
				'spamtype' => 'spam_master_alert_level_date',
			);
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update( $spam_master_keys, $data_spam2, $where_spam2 );
		}
		// End function admin notices.
	}
	add_action( 'admin_notices', 'spam_master_admin_notices' );

	/**
	 * Key cron.
	 *
	 * @param schedules $schedules for cron.
	 *
	 * @return schedules
	 */
	function spam_master_key_cron( $schedules ) {
		$schedules['daily'] = array(
			'interval' => 86400,
			'display'  => __( 'Once Daily', 'spam-master' ),
		);
		return $schedules;
	}
	add_filter( 'cron_schedules', 'spam_master_key_cron' );

	/**
	 * Sets the key page.
	 *
	 * @return void
	 */
	function spam_master_key_load_cron() {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-admin-key-sender.php';
	}

	if ( ! wp_next_scheduled( 'spam_master_key_load' ) ) {
		wp_schedule_event( time(), 'daily', 'spam_master_key_load' );
	}
	add_action( 'spam_master_key_load', 'spam_master_key_load_cron' );

	/**
	 * Registers deactivation.
	 *
	 * @return void
	 */
	function spam_master_remove_key_cron_schedule() {
		wp_clear_scheduled_hook( 'spam_master_key_load' );
	}
	register_deactivation_hook( __FILE__, 'spam_master_remove_key_cron_schedule' );

	/**
	 * Tasks cron.
	 *
	 * @param schedules $schedules for cron.
	 *
	 * @return schedules
	 */
	function spam_master_tasks_cron( $schedules ) {
		// Set our 1 hours, units in seconds.
		$schedules['hourly'] = array(
			'interval' => 3600,
			'display'  => 'Once Hourly',
		);
		return $schedules;
	}
	add_filter( 'cron_schedules', 'spam_master_tasks_cron' );

	/**
	 * Sets the updater page.
	 *
	 * @return void
	 */
	function spam_master_tasks_load_cron() {
		require_once WP_PLUGIN_DIR . '/spam-master/includes/admin/spam-master-tasks.php';
	}

	if ( ! wp_next_scheduled( 'spam_master_tasks_load' ) ) {
		wp_schedule_event( time(), 'hourly', 'spam_master_tasks_load' );
	}
	add_action( 'spam_master_tasks_load', 'spam_master_tasks_load_cron' );

	/**
	 * Registers deactivation.
	 *
	 * @return void
	 */
	function spam_master_remove_tasks_cron_schedule() {
		wp_clear_scheduled_hook( 'spam_master_tasks_load' );
	}
	register_deactivation_hook( __FILE__, 'spam_master_remove_tasks_cron_schedule' );

	// Set emails outside admin scope including weekly email cron.
	$spam_master_emails_current_date = current_datetime()->format( 'Y-m-d' );
	if ( 'EXPIRED' === $spam_master_status ) {
		if ( 'TRIAL' === $spam_master_type ) {
			if ( $spam_master_emails_current_date !== $spam_master_trial_expired_date && '1' !== $spam_master_trial_expired_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_trial_exp_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammastertrialexpnotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_trial_exp_notify_load' );

			}
		}
		if ( 'FREE' === $spam_master_type ) {
			if ( $spam_master_emails_current_date !== $spam_master_free_expired_date && '1' !== $spam_master_free_expired_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_free_exp_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterfreeexpnotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_free_exp_notify_load' );

			}
		}
		if ( 'FULL' === $spam_master_type ) {
			if ( $spam_master_emails_current_date !== $spam_master_full_expired_date && '1' !== $spam_master_full_expired_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_full_exp_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterfullexpnotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_full_exp_notify_load' );

			}
		}
	}
	if ( 'INACTIVE' === $spam_master_status ) {
		if ( 'FULL' === $spam_master_type ) {
			if ( $spam_master_emails_current_date >= $spam_master_full_inactive_date && '1' !== $spam_master_full_inactive_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_full_inact_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterfullinactnotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_full_inact_notify_load' );

			}
		}
	}
	if ( 'UNSTABLE' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_free_unstable_date && '1' !== $spam_master_free_unstable_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_unstable_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_email                     = $spam_master_email_controller->spammasterunstablenotify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_unstable_notify_load' );

		}
	}
	if ( 'HIGH_VOLUME' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_high_volume_date && '1' !== $spam_master_high_volume_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_high_volume_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_email                     = $spam_master_email_controller->spammasterhighvolumenotify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_high_volume_notify_load' );

		}
	}
	if ( 'MALFUNCTION_1' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_malfunction_1_date && '1' !== $spam_master_malfunction_1_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_mailfunction1_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_malfunction1              = $spam_master_email_controller->spammastermalfunction1notify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_mailfunction1_notify_load' );

		}
	}
	if ( 'MALFUNCTION_2' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_malfunction_2_date && '1' !== $spam_master_malfunction_2_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_mailfunction2_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_malfunction2              = $spam_master_email_controller->spammastermalfunction2notify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_mailfunction2_notify_load' );

		}
	}
	if ( 'MALFUNCTION_6' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_malfunction_6_date && '1' !== $spam_master_malfunction_6_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_mailfunction6_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_malfunction6              = $spam_master_email_controller->spammastermalfunction6notify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_mailfunction6_notify_load' );

		}
	}
	if ( 'MALFUNCTION_8' === $spam_master_status ) {
		if ( $spam_master_emails_current_date >= $spam_master_malfunction_8_date && '1' !== $spam_master_malfunction_8_notice ) {

			/**
			 * Email function.
			 *
			 * @return void
			 */
			function spam_master_mailfunction8_notify_load() {
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_malfunction8              = $spam_master_email_controller->spammastermalfunction8notify( $spammail );
			}
			add_action( 'wp_loaded', 'spam_master_mailfunction8_notify_load' );

		}
	}
	if ( 'VALID' === $spam_master_status ) {
		if ( 'FULL' === $spam_master_type ) {
			if ( '1' !== $spam_master_full_install_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_full_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterfullnotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_full_notify_load' );
			}
		}
		if ( 'FREE' === $spam_master_type ) {
			$spam_master_free_notice_plus_7 = gmdate( 'Y-m-d', strtotime( '+7 days', strtotime( $spam_master_expires ) ) );
			if ( $spam_master_emails_current_date >= $spam_master_free_notice_plus_7 && '1' !== $spam_master_free_rate_notice ) {

				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_free_notify_load() {
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterfreenotify( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_free_notify_load' );

			}
		}
		// Set 6 days report email if active.
		if ( 'true' === $spam_master_emails_weekly_email ) {
			/**
			 * Cron function.
			 *
			 * @param schedules $schedules for cron.
			 *
			 * @return schedules
			 */
			function spam_master_weekly_report_cron( $schedules ) {
				$schedules['6days'] = array(
					'interval' => 518400,
					'display'  => __( '6 Days', 'spam-master' ),
				);
				return $schedules;
			}
			add_filter( 'cron_schedules', 'spam_master_weekly_report_cron' );

			/**
			 * Cron function.
			 *
			 * @return void
			 */
			function spam_master_weekly_report_load_cron() {
				global $wpdb, $blog_id;

				// Spam email controller.
				$spammail                     = true;
				$spam_master_email_controller = new SpamMasterEmailController();
				$is_email                     = $spam_master_email_controller->spammasterweeklyreport( $spammail );

				// Add table & load spam master options.
				if ( is_multisite() ) {
					$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
				} else {
					$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
				}
				$spamkey   = 'Option';
				$spamtype1 = 'spam_master_emails_weekly_stats';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$spam_master_emails_weekly_stats = $wpdb->get_var(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
						$spamkey,
						$spamtype1
					)
				);
				if ( 'true' === $spam_master_emails_weekly_stats ) {
					// Spam email controller.
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasterweeklystatsreport( $spammail );
				}
			}
			if ( ! wp_next_scheduled( 'spam_master_weekly_report_load' ) ) {
				wp_schedule_event( time(), '6days', 'spam_master_weekly_report_load' );
			}
			add_action( 'spam_master_weekly_report_load', 'spam_master_weekly_report_load_cron' );

			/**
			 * Deactivation function.
			 *
			 * @return void
			 */
			function spam_master_remove_weekly_report_cron_schedule() {
				wp_clear_scheduled_hook( 'spam_master_weekly_report_load' );
			}
			register_deactivation_hook( __FILE__, 'spam_master_remove_weekly_report_cron_schedule' );
		} else {
			/**
			 * Deactivation function.
			 *
			 * @return void
			 */
			function spam_master_remove_weekly_report_cron_schedule() {
				wp_clear_scheduled_hook( 'spam_master_weekly_report_load' );
			}
			register_deactivation_hook( __FILE__, 'spam_master_remove_weekly_report_cron_schedule' );
		}
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$spam_master_alert_level = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
	// Start Alert Emails.
	$spam_master_daily_email_plus1 = gmdate( 'Y-m-d', strtotime( '+1 days', strtotime( $spam_master_emails_alert_date ) ) );
	if ( $spam_master_emails_current_date >= $spam_master_daily_email_plus1 && '1' !== $spam_master_emails_alert_notice ) {

		if ( 'true' === $spam_master_emails_alert_3_email ) {
			if ( 'ALERT_3' === $spam_master_alert_level ) {
				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_daily_email_alert3_load() {
					// Spam Email Controller.
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasteralert3( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_daily_email_alert3_load' );
			}
		}
		if ( 'true' === $spam_master_emails_alert_email ) {
			if ( 'ALERT_2' === $spam_master_alert_level || 'ALERT_1' === $spam_master_alert_level || 'ALERT_0' === $spam_master_alert_level ) {
				/**
				 * Email function.
				 *
				 * @return void
				 */
				function spam_master_daily_email_alert_load() {
					// Spam Email Controller.
					$spammail                     = true;
					$spam_master_email_controller = new SpamMasterEmailController();
					$is_email                     = $spam_master_email_controller->spammasteralert( $spammail );
				}
				add_action( 'wp_loaded', 'spam_master_daily_email_alert_load' );
			}
		}
	}

	/**
	 * Dismiss function.
	 *
	 * @return void
	 */
	function spam_master_dismiss() {
		global $wpdb, $blog_id;

		// Add table & load spam master options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
			$blogname         = substr( get_blog_option( $blog_id, 'blogname' ), 0, 256 );
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
			$blogname         = substr( get_option( 'blogname' ), 0, 256 );
		}
		if ( empty( $blogname ) ) {
			$blogname = 'empty blog name';
		}
		$spamkey   = 'Option';
		$spamtype1 = 'spam_license_key';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$spam_license_key_pre = $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT spamvalue FROM $spam_master_keys WHERE spamkey=%s AND spamtype=%s",
				$spamkey,
				$spamtype1
			)
		);
		$spam_license_key = esc_html( substr( $spam_license_key_pre, 0, 64 ) );
		// Spam collect controller.
		$spam_master_collect_controller = new SpamMasterCollectController();
		$collect_now                    = true;
		$is_collected                   = $spam_master_collect_controller->spammastergetcollect( $collect_now );

		// Spam user controller.
		$spam_master_user_controller = new SpamMasterUserController();
		// Spam initial.
		$spaminitial  = 'plugin';
		$spampreemail = false;
		$is_user      = $spam_master_user_controller->spammastergetuser( $spaminitial, $spampreemail );

		// phpcs:ignore
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'spammasterdisnonce' ) ) {
			if ( isset( $_REQUEST['spammasterdisfr'] ) && '1' === $_REQUEST['spammasterdisfr'] ) {
				$is_type           = 'Invitation Free Local';
				$is_set_local_free = true;
				// Update invitation.
				$data_spam  = array( 'spamvalue' => '1' );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_invitation_free_notice',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			} else {
				$is_set_local_free = false;
			}
			if ( isset( $_REQUEST['spammasterdisfu'] ) && '1' === $_REQUEST['spammasterdisfu'] ) {
				$is_type          = 'Invitation Pro Local';
				$is_set_local_pro = true;
				// Update invitation.
				$data_spam  = array( 'spamvalue' => '1' );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_invitation_full_notice',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			} else {
				$is_set_local_pro = false;
			}
			if ( isset( $_REQUEST['spammasterdisfrwide'] ) && '1' === $_REQUEST['spammasterdisfrwide'] ) {
				$is_type          = 'Invitation Free Wide';
				$is_set_wide_free = true;
				// Update invitation.
				$data_spam  = array( 'spamvalue' => '1' );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_invitation_free_wide_notice',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			} else {
				$is_set_wide_free = false;
			}
			if ( isset( $_REQUEST['spammasterdisfuwide'] ) && '1' === $_REQUEST['spammasterdisfuwide'] ) {
				$is_type         = 'Invitation Pro Wide';
				$is_set_wide_pro = true;
				// Update invitation.
				$data_spam  = array( 'spamvalue' => '1' );
				$where_spam = array(
					'spamkey'  => 'Option',
					'spamtype' => 'spam_master_invitation_full_wide_notice',
				);
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update( $spam_master_keys, $data_spam, $where_spam );
			} else {
				$is_set_wide_pro = false;
			}
		} else {
			$is_set_local_free = false;
			$is_set_local_pro  = false;
			$is_set_wide_free  = false;
			$is_set_wide_pro   = false;
		}
		if ( $is_set_local_free || $is_set_local_pro || $is_set_wide_free || $is_set_wide_pro ) {

			// Log inup controller.
			$spamtype                   = 'Invitation';
			$spamvalue                  = 'Dismissal ' . $is_type;
			$cache                      = '7D';
			$spam_master_log_controller = new SpamMasterLogController();
			$is_log                     = $spam_master_log_controller->spammasterlog( $is_collected['remote_ip'], $is_user['blog_threat_email'], $is_collected['remote_referer'], $is_collected['dest_url'], $is_collected['remote_agent'], $is_user['spamuserA'], $spamtype, $spamvalue, $cache );
			?>
			<div id="message" class="updated">
			<p><?php echo esc_attr( __( 'Invitation Dismissed.', 'spam-master' ) ); ?></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'spam_master_dismiss' );

	// Lets lazy check key.
	$spam_master_current_date        = current_datetime()->format( 'Y-m-d' );
	$spam_master_sync_date_plus_days = gmdate( 'Y-m-d', strtotime( '+2 days', strtotime( $spam_master_license_sync_date ) ) );
	if ( $spam_master_current_date >= $spam_master_sync_date_plus_days && '1' !== $spam_master_license_sync_run ) {

		// Update run notice.
		$data_spam  = array( 'spamvalue' => '1' );
		$where_spam = array(
			'spamkey'  => 'Option',
			'spamtype' => 'spam_master_license_sync_run',
		);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update( $spam_master_keys, $data_spam, $where_spam );

		// Spam key controller.
		$spam_master_key            = true;
		$spam_master_do             = 'LAZY';
		$spam_master_key_controller = new SpamMasterKeyController();
		$is_lazy_key                = $spam_master_key_controller->spammasterkeylazy( $spam_master_key, $spam_master_do );

		if ( 'LAZY' === $is_lazy_key ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$spam_master_alert_level_again = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_alert_level'" );
			// Start Alert Emails.
			if ( 'true' === $spam_master_emails_alert_3_email ) {
				if ( 'ALERT_3' === $spam_master_alert_level_again ) {

					/**
					 * Email function.
					 *
					 * @return void
					 */
					function spam_master_daily_email_alert3_lazy_load() {
						// Spam Email Controller.
						$spammail                     = true;
						$spam_master_email_controller = new SpamMasterEmailController();
						$is_email                     = $spam_master_email_controller->spammasteralert3( $spammail );
					}
					add_action( 'wp_loaded', 'spam_master_daily_email_alert3_lazy_load' );

				}
			}
			if ( 'true' === $spam_master_emails_alert_email ) {
				if ( 'ALERT_2' === $spam_master_alert_level_again || 'ALERT_1' === $spam_master_alert_level_again || 'ALERT_0' === $spam_master_alert_level_again ) {

					/**
					 * Email function.
					 *
					 * @return void
					 */
					function spam_master_daily_email_alert_lazy_load() {
						// Spam Email Controller.
						$spammail                     = true;
						$spam_master_email_controller = new SpamMasterEmailController();
						$is_email                     = $spam_master_email_controller->spammasteralert( $spammail );
					}
					add_action( 'wp_loaded', 'spam_master_daily_email_alert_lazy_load' );

				}
			}
		}
	}
}

/**
 * Adds Spam Master Version to the <head> tag
 *
 * @since 6.6.20
 * @return void
 */
function spam_master_header_generator() {
	$spam_master_name    = constant( 'SPAM_MASTER_NAME' );
	$spam_master_version = constant( 'SPAM_MASTER_VERSION' );
	?>
	<meta name="generator" content="<?php echo esc_attr( $spam_master_name ) . ' ' . esc_attr( $spam_master_version ) . esc_attr( __( ' - Real-time Protection With Firewall Security at spammaster.org.' ) ); ?>" />
	<?php
}
add_action( 'wp_head', 'spam_master_header_generator' );
add_action( 'login_head', 'spam_master_header_generator' );

/**
 * Deactivation hook.
 *
 * @return void
 */
function spam_master_deactivate() {

	// Spam key controller.
	$spam_master_key            = true;
	$spam_master_do             = 'deact';
	$spam_master_key_controller = new SpamMasterKeyController();
	$is_deact_key               = $spam_master_key_controller->spammasterkeydeact( $spam_master_key, $spam_master_do );

	// Spam email controller.
	$spammail                     = true;
	$spam_master_email_controller = new SpamMasterEmailController();
	$is_deact_mail                = $spam_master_email_controller->spammasterdeactemail( $spammail );

}
register_deactivation_hook( __FILE__, 'spam_master_deactivate' );

/**
 * Uninstall hook.
 *
 * @return void
 */
function spam_master_uninstall() {
	require_once WP_PLUGIN_DIR . '/spam-master/uninstall.php';
}
register_uninstall_hook( __FILE__, 'spam_master_uninstall' );
?>
