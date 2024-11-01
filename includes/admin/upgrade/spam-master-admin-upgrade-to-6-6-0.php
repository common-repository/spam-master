<?php
/**
 * Update script.
 *
 * @package Spam Master
 */

global $wpdb, $blog_id;

if ( is_multisite() ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs;" );
	foreach ( $blogs as $idb ) {
		// Delete legacy tables.
		$table_threats_del = $wpdb->get_blog_prefix( $idb ) . 'spam_master_threats';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS $table_threats_del" );
		$table_white_del = $wpdb->get_blog_prefix( $idb ) . 'spam_master_white';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS $table_white_del" );
		$table_bots_del = $wpdb->get_blog_prefix( $idb ) . 'spam_master_bots';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS $table_bots_del" );
		$table_keys_trunc = $wpdb->get_blog_prefix( $idb ) . 'spam_master_keys';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "TRUNCATE TABLE $table_keys_trunc" );
		// Import if any.
		$spam_master_type = get_blog_option( $idb, 'spam_master_type' );
		if ( empty( $spam_master_type ) ) {
			$spam_master_type                    = 'EMPTY';
			$spam_master_status                  = 'INACTIVE';
			$spam_master_attached                = 'EMPTY';
			$spam_master_expires                 = 'EMPTY';
			$spam_license_key                    = '';
			$spam_master_protection_total_number = '0';
			$spam_master_alert_level             = '';
			$spam_master_alert_level_date        = '';
			$spam_master_alert_level_p_text      = '';
		}
		if ( 'TRIAL' === $spam_master_type ) {
			$spam_master_type                    = 'EMPTY';
			$spam_master_status                  = 'INACTIVE';
			$spam_master_attached                = 'EMPTY';
			$spam_master_expires                 = 'EMPTY';
			$spam_license_key                    = '';
			$spam_master_protection_total_number = '0';
			$spam_master_alert_level             = '';
			$spam_master_alert_level_date        = '';
			$spam_master_alert_level_p_text      = '';
		}
		if ( 'FREE' === $spam_master_type ) {
			$spam_master_type                    = 'FREE';
			$spam_master_status                  = get_blog_option( $idb, 'spam_master_status' );
			$spam_master_attached                = get_blog_option( $idb, 'spam_master_attached' );
			$spam_master_expires                 = get_blog_option( $idb, 'spam_master_expires' );
			$spam_license_key                    = get_blog_option( $idb, 'spam_license_key' );
			$spam_master_protection_total_number = get_blog_option( $idb, 'spam_master_protection_total_number' );
			$spam_master_alert_level             = get_blog_option( $idb, 'spam_master_alert_level' );
			$spam_master_alert_level_date        = get_blog_option( $idb, 'spam_master_alert_level_date' );
			$spam_master_alert_level_p_text      = get_blog_option( $idb, 'spam_master_alert_level_p_text' );
		}
		if ( 'FULL' === $spam_master_type ) {
			$spam_master_type                    = 'FULL';
			$spam_master_status                  = get_blog_option( $idb, 'spam_master_status' );
			$spam_master_attached                = get_blog_option( $idb, 'spam_master_attached' );
			$spam_master_expires                 = get_blog_option( $idb, 'spam_master_expires' );
			$spam_license_key                    = get_blog_option( $idb, 'spam_license_key' );
			$spam_master_protection_total_number = get_blog_option( $idb, 'spam_master_protection_total_number' );
			$spam_master_alert_level             = get_blog_option( $idb, 'spam_master_alert_level' );
			$spam_master_alert_level_date        = get_blog_option( $idb, 'spam_master_alert_level_date' );
			$spam_master_alert_level_p_text      = get_blog_option( $idb, 'spam_master_alert_level_p_text' );
		}
		// Static Site Options.
		$web_address     = get_site_url();
		$address_unclean = $web_address;
		$address_long    = preg_replace( '#^https?://#', '', $address_unclean );
		$address         = substr( $address_long, 0, 256 );
		if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
			$blog_server_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ), 0, 48 );
		}
		// if empty ip.
		if ( empty( $blog_server_ip ) || '0' === $blog_server_ip ) {
			if ( isset( $_SERVER['SERVER_NAME'] ) ) {
				$blog_server_ip = 'I ' . substr( gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ), 0, 48 );
			} else {
				$blog_server_ip = 'I 000';
			}
		}
		// Create db protection hash.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
		$spam_master_db_protection_hash = substr( md5( uniqid( mt_rand(), true ) ), 0, 64 );
		if ( empty( $spam_master_db_protection_hash ) ) {
			$spam_master_db_protection_hash = 'md5-' . gmdate( 'YmdHis' );
		}
		// Install plugin options.
		$spam_master_keys = $wpdb->get_blog_prefix( $idb ) . 'spam_master_keys';
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_block_count',
				'spamy'     => 'localhost',
				'spamvalue' => '1',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_type',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_type,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_status',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_status,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_attached',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_attached,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_expires',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_expires,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_license_key',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_license_key,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_address',
				'spamy'     => 'localhost',
				'spamvalue' => $address,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_ip',
				'spamy'     => 'localhost',
				'spamvalue' => $blog_server_ip,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_license_key_old',
				'spamy'     => 'localhost',
				'spamvalue' => '',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_protection_total_number',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_protection_total_number,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_alert_level',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_alert_level,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_alert_level_date',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_alert_level_date,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_alert_level_p_text',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_alert_level_p_text,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_firewall_on',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_message',
				'spamy'     => 'localhost',
				'spamvalue' => ': Email, Domain, or Ip banned.',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_learning_active',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_cache_proxie',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_amp_check_fun',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_comment_strict_on',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_comments_clean',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_text_override',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'comment_russian_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_russian_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'comment_chinese_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_chinese_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'comment_asian_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_asian_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'comment_arabic_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_arabic_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'comment_spam_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'contact_spam_char',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$spam_master_russian_char_array         = array( 'д', 'и', 'ж', 'Ч', 'Б' );
		$spam_master_russian_char_array_implode = implode( "\n", $spam_master_russian_char_array );
		$spam_master_chinese_char_array         = array( '的', '是', '一', '不', '了', '人', '我', '在', '有', '他', '这', '为', '你', '出', '就', '那', '要', '自', '她', '于', '木', '作', '工', '程', '裝', '潢', '統', '包', '室', '內', '設', '計', '家', '谩', '膷', '艡', '铆', '茅', '眉' );
		$spam_master_chinese_char_array_implode = implode( "\n", $spam_master_chinese_char_array );
		$spam_master_asian_char_array           = array( 'ョ', 'プ', 'て', 'い', 'ン', 'が', 'る', 'ノ', '。', 'ト', 'ự', 'ữ', 'ắ', 'ủ', 'ă', 'ả', 'ạ', 'ơ', 'ố', 'ộ', 'ư', '부', '스', '타', '빗' );
		$spam_master_asian_char_array_implode   = implode( "\n", $spam_master_asian_char_array );
		$spam_master_arabic_char_array          = array( 'أ', 'ن', 'ا', 'ح', 'ب', 'ه', 'ل', 'ا', 'ي', 'ة', 'إ', 'أ', 'و', 'هَ', 'ج' );
		$spam_master_arabic_char_array_implode  = implode( "\n", $spam_master_arabic_char_array );
		$spam_master_spam_char_array            = array( 'ɑ', 'ɑ', 'Ь', 'Ᏼ', 'ƅ', 'Ⲥ', 'Ԁ', 'ԁ', 'Ɗ', 'Ꭰ', 'ɗ', 'ｅ', 'ｅ', 'Ꮐ', 'Ꮋ', 'һ', 'ߋ', 'օ', 'ⲟ', 'Ⲣ', 'ⲣ', 'Ꮲ', 'Ꭱ', 'ｒ', 'Ꮪ', 'Ⴝ', 'Ꭲ', 'Ƭ', 'ᥙ', 'ҝ', 'ⲭ', 'ｚ', 'Ꮤ', 'ѡ', 'ʏ', 'ʏ', 'ү', 'ү', 'Ⲩ', 'қ', 'ҝ', '᧐', '…', '・' );
		$spam_master_spam_char_array_implode    = implode( "\n", $spam_master_spam_char_array );
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_russian_char_set',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_russian_char_array_implode,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_chinese_char_set',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_chinese_char_array_implode,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_asian_char_set',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_asian_char_array_implode,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_arabic_char_set',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_arabic_char_array_implode,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_spam_char_set',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_spam_char_array_implode,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_signature_registration',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_signature_login',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_signature_comments',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_signature_email',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_heads_up',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_statistics',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_firewall',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_dashboard_status',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_dashboard_statistics',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_widget_top_menu_firewall',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_auto_update',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_shortcodes_total_count',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_extra_email',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_extra_email_list',
				'spamy'     => 'localhost',
				'spamvalue' => '',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_alert_email',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_weekly_email',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_weekly_stats',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_alert_3_email',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_honeypot_timetrap',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_honeypot_timetrap_speed',
				'spamy'     => 'localhost',
				'spamvalue' => '5',
			)
		);
		// Are integrations present?
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_integrations_contact_form_7',
					'spamy'     => 'localhost',
					'spamvalue' => 'true',
				)
			);
		} else {
			$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_integrations_contact_form_7',
					'spamy'     => 'localhost',
					'spamvalue' => 'false',
				)
			);
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_integrations_woocommerce',
					'spamy'     => 'localhost',
					'spamvalue' => 'true',
				)
			);
		} else {
			$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$spam_master_keys,
				array(
					'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
					'spamkey'   => 'Option',
					'spamtype'  => 'spam_master_integrations_woocommerce',
					'spamy'     => 'localhost',
					'spamvalue' => 'false',
				)
			);
		}
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_invitation_full_wide_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_invitation_free_wide_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_invitation_free_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_invitation_full_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_free_unstable',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_unstable_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_weekly_email_date',
				'spamy'     => 'localhost',
				'spamvalue' => '1970-01-01',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_emails_weekly_stats_date',
				'spamy'     => 'localhost',
				'spamvalue' => '1970-01-01',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_free_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_full_notice',
				'spamy'     => 'localhost',
				'spamvalue' => '0',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_full_expired',
				'spamy'     => 'localhost',
				'spamvalue' => '1970-01-01',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_new_options',
				'spamy'     => 'localhost',
				'spamvalue' => '1',
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_db_protection_hash',
				'spamy'     => 'localhost',
				'spamvalue' => $spam_master_db_protection_hash,
			)
		);
		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_white_empath',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
		// 6.2.2.
		delete_blog_option( $idb, 'comment_russian_char_set' );
		delete_blog_option( $idb, 'comment_chinese_char_set' );
		delete_blog_option( $idb, 'comment_asian_char_set' );
		delete_blog_option( $idb, 'comment_arabic_char_set' );
		delete_blog_option( $idb, 'comment_spam_char_set' );
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_2_2' );
		// 6.3.1.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_2_3' );
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_2_4' );
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_3_1' );
		// 6.3.2.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_3_2' );
		// 6.3.3a.
		delete_blog_option( $idb, 'spam_master_logs_firewall_clean' );
		delete_blog_option( $idb, 'spam_master_logs_registration_clean' );
		delete_blog_option( $idb, 'spam_master_logs_comment_clean', '90' );
		delete_blog_option( $idb, 'spam_master_logs_contact_form_7_clean' );
		delete_blog_option( $idb, 'spam_master_logs_woocommerce_clean' );
		delete_blog_option( $idb, 'spam_master_logs_system_clean' );
		delete_blog_option( $idb, 'spam_master_logs_honeypot_clean' );
		delete_blog_option( $idb, 'spam_master_logs_recaptcha_clean' );
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_3_3a' );
		// 6.3.4.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_3_4' );
		// 6.3.7.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_3_7' );
		// 6.5.1.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_5_1' );
		// 6.5.2.
		delete_blog_option( $idb, 'spam_master_upgrade_to_6_5_2' );
		// 6.6.0.
		delete_blog_option( $idb, 'spam_master_threats_db_version' );
		delete_blog_option( $idb, 'spam_master_white_db_version' );
		delete_blog_option( $idb, 'spam_master_bots_db_version' );
		delete_blog_option( $idb, 'spam_master_message' );
		delete_blog_option( $idb, 'spam_master_learning_active' );
		delete_blog_option( $idb, 'spam_master_cache_proxie' );
		delete_blog_option( $idb, 'spam_master_amp_check_fun' );
		delete_blog_option( $idb, 'spam_master_recaptcha_ampoff' );
		delete_blog_option( $idb, 'spam_master_recaptcha_preview' );
		delete_blog_option( $idb, 'spam_master_comment_strict_on' );
		delete_blog_option( $idb, 'spam_master_comments_clean' );
		delete_blog_option( $idb, 'spam_master_russian_char_set' );
		delete_blog_option( $idb, 'spam_master_chinese_char_set' );
		delete_blog_option( $idb, 'spam_master_asian_char_set' );
		delete_blog_option( $idb, 'spam_master_arabic_char_set' );
		delete_blog_option( $idb, 'spam_master_spam_char_set' );
		delete_blog_option( $idb, 'comment_russian_char' );
		delete_blog_option( $idb, 'contact_russian_char' );
		delete_blog_option( $idb, 'comment_chinese_char' );
		delete_blog_option( $idb, 'contact_chinese_char' );
		delete_blog_option( $idb, 'comment_asian_char' );
		delete_blog_option( $idb, 'contact_asian_char' );
		delete_blog_option( $idb, 'comment_arabic_char' );
		delete_blog_option( $idb, 'contact_arabic_char' );
		delete_blog_option( $idb, 'comment_spam_char' );
		delete_blog_option( $idb, 'contact_spam_char' );
		delete_blog_option( $idb, 'spam_master_signature_registration' );
		delete_blog_option( $idb, 'spam_master_signature_login' );
		delete_blog_option( $idb, 'spam_master_signature_comments' );
		delete_blog_option( $idb, 'spam_master_signature_email' );
		delete_blog_option( $idb, 'spam_master_block_count' );
		delete_blog_option( $idb, 'spam_master_widget_heads_up' );
		delete_blog_option( $idb, 'spam_master_widget_statistics' );
		delete_blog_option( $idb, 'spam_master_widget_firewall' );
		delete_blog_option( $idb, 'spam_master_widget_dashboard_status' );
		delete_blog_option( $idb, 'spam_master_widget_dashboard_statistics' );
		delete_blog_option( $idb, 'spam_master_widget_top_menu_firewall' );
		delete_blog_option( $idb, 'spam_master_auto_update' );
		delete_blog_option( $idb, 'spam_master_shortcodes_total_count' );
		delete_blog_option( $idb, 'spam_master_emails_alert_email' );
		delete_blog_option( $idb, 'spam_master_emails_weekly_email' );
		delete_blog_option( $idb, 'spam_master_emails_weekly_stats' );
		delete_blog_option( $idb, 'spam_master_emails_alert_3_email' );
		delete_blog_option( $idb, 'spam_master_firewall_on' );
		delete_blog_option( $idb, 'spam_master_honeypot_timetrap' );
		delete_blog_option( $idb, 'spam_master_honeypot_timetrap_speed' );
		delete_blog_option( $idb, 'spam_master_comment_website_field' );
		delete_blog_option( $idb, 'spam_master_recaptcha_version' );
		delete_blog_option( $idb, 'spam_master_recaptcha_public_key' );
		delete_blog_option( $idb, 'spam_master_recaptcha_secret_key' );
		delete_blog_option( $idb, 'spam_master_recaptcha_theme' );
		delete_blog_option( $idb, 'spam_master_recaptcha_registration' );
		delete_blog_option( $idb, 'spam_master_recaptcha_login' );
		delete_blog_option( $idb, 'spam_master_recaptcha_comments' );
		delete_blog_option( $idb, 'spam_master_integrations_contact_form_7' );
		delete_blog_option( $idb, 'contact_text_override' );
		delete_blog_option( $idb, 'spam_master_integrations_woocommerce' );
		delete_blog_option( $idb, 'spam_master_invitation_full_wide_notice' );
		delete_blog_option( $idb, 'spam_master_invitation_free_wide_notice' );
		delete_blog_option( $idb, 'spam_master_invitation_free_notice' );
		delete_blog_option( $idb, 'spam_master_invitation_full_notice' );
		delete_blog_option( $idb, 'spam_master_free_unstable' );
		delete_blog_option( $idb, 'spam_master_unstable_notice' );
		delete_blog_option( $idb, 'spam_license_key_old_code' );
		delete_blog_option( $idb, 'spam_master_emails_weekly_email_date' );
		delete_blog_option( $idb, 'spam_master_free_notice' );
		delete_blog_option( $idb, 'spam_master_emails_extra_email' );
		delete_blog_option( $idb, 'spam_master_emails_extra_email_list' );
		delete_blog_option( $idb, 'spam_master_full_expired' );
		// Can be deleted now.
		delete_blog_option( $idb, 'spam_master_type' );
		delete_blog_option( $idb, 'spam_master_status' );
		delete_blog_option( $idb, 'spam_master_attached' );
		delete_blog_option( $idb, 'spam_master_expires' );
		delete_blog_option( $idb, 'spam_license_key' );
		delete_blog_option( $idb, 'spam_master_protection_total_number' );
		delete_blog_option( $idb, 'spam_master_alert_level' );
		delete_blog_option( $idb, 'spam_master_alert_level_date' );
		delete_blog_option( $idb, 'spam_master_alert_level_p_text' );
		delete_blog_option( $idb, 'spam_master_full_notice' );
		// Update.
		update_blog_option( $idb, 'spam_master_upgrade_to_6_6_0', '1' );
	}
} else {
	// Delete legacy tables.
	$table_threats_del = $wpdb->prefix . 'spam_master_threats';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS $table_threats_del" );
	$table_white_del = $wpdb->prefix . 'spam_master_white';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS $table_white_del" );
	$table_bots_del = $wpdb->prefix . 'spam_master_bots';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS $table_bots_del" );
	$table_keys_trunc = $wpdb->prefix . 'spam_master_keys';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( "TRUNCATE TABLE $table_keys_trunc" );
	// Import if any.
	$spam_master_type = get_option( 'spam_master_type' );
	if ( ! isset( $spam_master_type ) || empty( $spam_master_type ) ) {
		$spam_master_type                    = 'EMPTY';
		$spam_master_status                  = 'INACTIVE';
		$spam_master_attached                = 'EMPTY';
		$spam_master_expires                 = 'EMPTY';
		$spam_license_key                    = '';
		$spam_master_protection_total_number = '0';
		$spam_master_alert_level             = '';
		$spam_master_alert_level_date        = '';
		$spam_master_alert_level_p_text      = '';
	}
	if ( 'TRIAL' === $spam_master_type ) {
		$spam_master_type                    = 'EMPTY';
		$spam_master_status                  = 'INACTIVE';
		$spam_master_attached                = 'EMPTY';
		$spam_master_expires                 = 'EMPTY';
		$spam_license_key                    = '';
		$spam_master_protection_total_number = '0';
		$spam_master_alert_level             = '';
		$spam_master_alert_level_date        = '';
		$spam_master_alert_level_p_text      = '';
	}
	if ( 'FREE' === $spam_master_type ) {
		$spam_master_type                    = 'FREE';
		$spam_master_status                  = get_option( 'spam_master_status' );
		$spam_master_attached                = get_option( 'spam_master_attached' );
		$spam_master_expires                 = get_option( 'spam_master_expires' );
		$spam_license_key                    = get_option( 'spam_license_key' );
		$spam_master_protection_total_number = get_option( 'spam_master_protection_total_number' );
		$spam_master_alert_level             = get_option( 'spam_master_alert_level' );
		$spam_master_alert_level_date        = get_option( 'spam_master_alert_level_date' );
		$spam_master_alert_level_p_text      = get_option( 'spam_master_alert_level_p_text' );
	}
	if ( 'FULL' === $spam_master_type ) {
		$spam_master_type                    = 'FULL';
		$spam_master_status                  = get_option( 'spam_master_status' );
		$spam_master_attached                = get_option( 'spam_master_attached' );
		$spam_master_expires                 = get_option( 'spam_master_expires' );
		$spam_license_key                    = get_option( 'spam_license_key' );
		$spam_master_protection_total_number = get_option( 'spam_master_protection_total_number' );
		$spam_master_alert_level             = get_option( 'spam_master_alert_level' );
		$spam_master_alert_level_date        = get_option( 'spam_master_alert_level_date' );
		$spam_master_alert_level_p_text      = get_option( 'spam_master_alert_level_p_text' );
	}
	// Static Site Options.
	$web_address     = get_site_url();
	$address_unclean = $web_address;
	$address_long    = preg_replace( '#^https?://#', '', $address_unclean );
	$address         = substr( $address_long, 0, 256 );
	if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
		$blog_server_ip = substr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ), 0, 48 );
	}
	// if empty ip.
	if ( empty( $blog_server_ip ) || '0' === $blog_server_ip ) {
		if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$blog_server_ip = 'I ' . substr( gethostbyname( esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ), 0, 48 );
		} else {
			$blog_server_ip = 'I 000';
		}
	}
	// Create db protection hash.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_mt_rand
	$spam_master_db_protection_hash = substr( md5( uniqid( mt_rand(), true ) ), 0, 64 );
	if ( empty( $spam_master_db_protection_hash ) ) {
		$spam_master_db_protection_hash = 'md5-' . gmdate( 'YmdHis' );
	}
	// Install plugin options.
	$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_block_count',
			'spamy'     => 'localhost',
			'spamvalue' => '1',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_type',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_type,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_status',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_status,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_attached',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_attached,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_expires',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_expires,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_license_key',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_license_key,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_address',
			'spamy'     => 'localhost',
			'spamvalue' => $address,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_ip',
			'spamy'     => 'localhost',
			'spamvalue' => $blog_server_ip,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_license_key_old',
			'spamy'     => 'localhost',
			'spamvalue' => '',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_protection_total_number',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_protection_total_number,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_alert_level',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_alert_level,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_alert_level_date',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_alert_level_date,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_alert_level_p_text',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_alert_level_p_text,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_firewall_on',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_message',
			'spamy'     => 'localhost',
			'spamvalue' => ': Email, Domain, or Ip banned.',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_learning_active',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_cache_proxie',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_amp_check_fun',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_comment_strict_on',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_comments_clean',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_text_override',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'comment_russian_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_russian_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'comment_chinese_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_chinese_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'comment_asian_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_asian_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'comment_arabic_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_arabic_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'comment_spam_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'contact_spam_char',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	$spam_master_russian_char_array         = array( 'д', 'и', 'ж', 'Ч', 'Б' );
	$spam_master_russian_char_array_implode = implode( "\n", $spam_master_russian_char_array );
	$spam_master_chinese_char_array         = array( '的', '是', '一', '不', '了', '人', '我', '在', '有', '他', '这', '为', '你', '出', '就', '那', '要', '自', '她', '于', '木', '作', '工', '程', '裝', '潢', '統', '包', '室', '內', '設', '計', '家', '谩', '膷', '艡', '铆', '茅', '眉' );
	$spam_master_chinese_char_array_implode = implode( "\n", $spam_master_chinese_char_array );
	$spam_master_asian_char_array           = array( 'ョ', 'プ', 'て', 'い', 'ン', 'が', 'る', 'ノ', '。', 'ト', 'ự', 'ữ', 'ắ', 'ủ', 'ă', 'ả', 'ạ', 'ơ', 'ố', 'ộ', 'ư', '부', '스', '타', '빗' );
	$spam_master_asian_char_array_implode   = implode( "\n", $spam_master_asian_char_array );
	$spam_master_arabic_char_array          = array( 'أ', 'ن', 'ا', 'ح', 'ب', 'ه', 'ل', 'ا', 'ي', 'ة', 'إ', 'أ', 'و', 'هَ', 'ج' );
	$spam_master_arabic_char_array_implode  = implode( "\n", $spam_master_arabic_char_array );
	$spam_master_spam_char_array            = array( 'ɑ', 'ɑ', 'Ь', 'Ᏼ', 'ƅ', 'Ⲥ', 'Ԁ', 'ԁ', 'Ɗ', 'Ꭰ', 'ɗ', 'ｅ', 'ｅ', 'Ꮐ', 'Ꮋ', 'һ', 'ߋ', 'օ', 'ⲟ', 'Ⲣ', 'ⲣ', 'Ꮲ', 'Ꭱ', 'ｒ', 'Ꮪ', 'Ⴝ', 'Ꭲ', 'Ƭ', 'ᥙ', 'ҝ', 'ⲭ', 'ｚ', 'Ꮤ', 'ѡ', 'ʏ', 'ʏ', 'ү', 'ү', 'Ⲩ', 'қ', 'ҝ', '᧐', '…', '・' );
	$spam_master_spam_char_array_implode    = implode( "\n", $spam_master_spam_char_array );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_russian_char_set',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_russian_char_array_implode,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_chinese_char_set',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_chinese_char_array_implode,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_asian_char_set',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_asian_char_array_implode,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_arabic_char_set',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_arabic_char_array_implode,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_spam_char_set',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_spam_char_array_implode,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_signature_registration',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_signature_login',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_signature_comments',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_signature_email',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_heads_up',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_statistics',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_firewall',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_dashboard_status',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_dashboard_statistics',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_widget_top_menu_firewall',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_auto_update',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_shortcodes_total_count',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_extra_email',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_extra_email_list',
			'spamy'     => 'localhost',
			'spamvalue' => '',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_alert_email',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_weekly_email',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_weekly_stats',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_alert_3_email',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_honeypot_timetrap',
			'spamy'     => 'localhost',
			'spamvalue' => 'true',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_honeypot_timetrap_speed',
			'spamy'     => 'localhost',
			'spamvalue' => '5',
		)
	);

	// Are integrations present?
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_integrations_contact_form_7',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
	} else {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_integrations_contact_form_7',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
	}
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_integrations_woocommerce',
				'spamy'     => 'localhost',
				'spamvalue' => 'true',
			)
		);
	} else {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$spam_master_keys,
			array(
				'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
				'spamkey'   => 'Option',
				'spamtype'  => 'spam_master_integrations_woocommerce',
				'spamy'     => 'localhost',
				'spamvalue' => 'false',
			)
		);
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_invitation_full_wide_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_invitation_free_wide_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_invitation_free_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_invitation_full_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_free_unstable',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_unstable_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_weekly_email_date',
			'spamy'     => 'localhost',
			'spamvalue' => '1970-01-01',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_emails_weekly_stats_date',
			'spamy'     => 'localhost',
			'spamvalue' => '1970-01-01',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_free_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_full_notice',
			'spamy'     => 'localhost',
			'spamvalue' => '0',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_full_expired',
			'spamy'     => 'localhost',
			'spamvalue' => '1970-01-01',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_new_options',
			'spamy'     => 'localhost',
			'spamvalue' => '1',
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_db_protection_hash',
			'spamy'     => 'localhost',
			'spamvalue' => $spam_master_db_protection_hash,
		)
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
	$wpdb->insert(
		$spam_master_keys,
		array(
			'time'      => current_datetime()->format( 'Y-m-d H:i:s' ),
			'spamkey'   => 'Option',
			'spamtype'  => 'spam_master_white_empath',
			'spamy'     => 'localhost',
			'spamvalue' => 'false',
		)
	);
	// 6.2.2.
	delete_option( 'comment_russian_char_set' );
	delete_option( 'comment_chinese_char_set' );
	delete_option( 'comment_asian_char_set' );
	delete_option( 'comment_arabic_char_set' );
	delete_option( 'comment_spam_char_set' );
	delete_option( 'spam_master_upgrade_to_6_2_2' );
	// 6.3.1.
	delete_option( 'spam_master_upgrade_to_6_2_3' );
	delete_option( 'spam_master_upgrade_to_6_2_4' );
	delete_option( 'spam_master_upgrade_to_6_3_1' );
	// 6.3.2.
	delete_option( 'spam_master_upgrade_to_6_3_2' );
	// 6.3.3a.
	delete_option( 'spam_master_logs_firewall_clean' );
	delete_option( 'spam_master_logs_registration_clean' );
	delete_option( 'spam_master_logs_comment_clean' );
	delete_option( 'spam_master_logs_contact_form_7_clean' );
	delete_option( 'spam_master_logs_woocommerce_clean' );
	delete_option( 'spam_master_logs_system_clean' );
	delete_option( 'spam_master_logs_honeypot_clean' );
	delete_option( 'spam_master_logs_recaptcha_clean' );
	delete_option( 'spam_master_upgrade_to_6_3_3a' );
	// 6.3.4.
	delete_option( 'spam_master_upgrade_to_6_3_4' );
	// 6.3.7.
	delete_option( 'spam_master_upgrade_to_6_3_7' );
	// 6.5.1.
	delete_option( 'spam_master_upgrade_to_6_5_1' );
	// 6.5.2.
	delete_option( 'spam_master_upgrade_to_6_5_2' );
	// 6.6.0.
	delete_option( 'spam_master_threats_db_version' );
	delete_option( 'spam_master_white_db_version' );
	delete_option( 'spam_master_bots_db_version' );
	delete_option( 'spam_master_message' );
	delete_option( 'spam_master_learning_active' );
	delete_option( 'spam_master_cache_proxie' );
	delete_option( 'spam_master_amp_check_fun' );
	delete_option( 'spam_master_recaptcha_ampoff' );
	delete_option( 'spam_master_recaptcha_preview' );
	delete_option( 'spam_master_comment_strict_on' );
	delete_option( 'spam_master_comments_clean' );
	delete_option( 'spam_master_russian_char_set' );
	delete_option( 'spam_master_chinese_char_set' );
	delete_option( 'spam_master_asian_char_set' );
	delete_option( 'spam_master_arabic_char_set' );
	delete_option( 'spam_master_spam_char_set' );
	delete_option( 'comment_russian_char' );
	delete_option( 'contact_russian_char' );
	delete_option( 'comment_chinese_char' );
	delete_option( 'contact_chinese_char' );
	delete_option( 'comment_asian_char' );
	delete_option( 'contact_asian_char' );
	delete_option( 'comment_arabic_char' );
	delete_option( 'contact_arabic_char' );
	delete_option( 'comment_spam_char' );
	delete_option( 'contact_spam_char' );
	delete_option( 'spam_master_signature_registration' );
	delete_option( 'spam_master_signature_login' );
	delete_option( 'spam_master_signature_comments' );
	delete_option( 'spam_master_signature_email' );
	delete_option( 'spam_master_block_count' );
	delete_option( 'spam_master_widget_heads_up' );
	delete_option( 'spam_master_widget_statistics' );
	delete_option( 'spam_master_widget_firewall' );
	delete_option( 'spam_master_widget_dashboard_status' );
	delete_option( 'spam_master_widget_dashboard_statistics' );
	delete_option( 'spam_master_widget_top_menu_firewall' );
	delete_option( 'spam_master_auto_update' );
	delete_option( 'spam_master_shortcodes_total_count' );
	delete_option( 'spam_master_emails_alert_email' );
	delete_option( 'spam_master_emails_weekly_email' );
	delete_option( 'spam_master_emails_weekly_stats' );
	delete_option( 'spam_master_emails_alert_3_email' );
	delete_option( 'spam_master_firewall_on' );
	delete_option( 'spam_master_honeypot_timetrap' );
	delete_option( 'spam_master_honeypot_timetrap_speed' );
	delete_option( 'spam_master_comment_website_field' );
	delete_option( 'spam_master_recaptcha_version' );
	delete_option( 'spam_master_recaptcha_public_key' );
	delete_option( 'spam_master_recaptcha_secret_key' );
	delete_option( 'spam_master_recaptcha_theme' );
	delete_option( 'spam_master_recaptcha_registration' );
	delete_option( 'spam_master_recaptcha_login' );
	delete_option( 'spam_master_recaptcha_comments' );
	delete_option( 'spam_master_integrations_contact_form_7' );
	delete_option( 'contact_text_override' );
	delete_option( 'spam_master_integrations_woocommerce' );
	delete_option( 'spam_master_invitation_full_wide_notice' );
	delete_option( 'spam_master_invitation_free_wide_notice' );
	delete_option( 'spam_master_invitation_free_notice' );
	delete_option( 'spam_master_invitation_full_notice' );
	delete_option( 'spam_master_free_unstable' );
	delete_option( 'spam_master_unstable_notice' );
	delete_option( 'spam_license_key_old_code' );
	delete_option( 'spam_master_emails_weekly_email_date' );
	delete_option( 'spam_master_free_notice' );
	delete_option( 'spam_master_emails_extra_email' );
	delete_option( 'spam_master_emails_extra_email_list' );
	delete_option( 'spam_master_full_expired' );
	delete_option( 'spam_master_full_notice' );
	// Can be deleted now.
	delete_option( 'spam_master_type' );
	delete_option( 'spam_master_status' );
	delete_option( 'spam_master_attached' );
	delete_option( 'spam_master_expires' );
	delete_option( 'spam_license_key' );
	delete_option( 'spam_master_protection_total_number' );
	delete_option( 'spam_master_alert_level' );
	delete_option( 'spam_master_alert_level_date' );
	delete_option( 'spam_master_alert_level_p_text' );
	// Update.
	update_option( 'spam_master_upgrade_to_6_6_0', '1' );
}

