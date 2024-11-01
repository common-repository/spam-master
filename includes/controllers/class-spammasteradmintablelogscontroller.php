<?php
/**
 * Menu table WP_List_Table based.
 *
 * @package Spam Master
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Main logs display class.
 *
 * @since 4.0.1
 */
class SpamMasterAdminTableLogsController extends WP_List_Table {

	/**
	 * Construct function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'singular_form',
				'plural'   => 'plural_form',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Get columns function.
	 *
	 * @package Spam Master
	 *
	 * @return array
	 */
	public function get_columns() {
		$spam_icon_id      = '<span class="dashicons dashicons-superhero-alt spam-master-middle" title="' . __( 'Firewall Action', 'spam-master' ) . '"></span>';
		$spam_icon_message = '<span class="dashicons dashicons-format-status spam-master-middle" title="' . __( 'Message', 'spam-master' ) . '"></span>';
		$columns           = array(
			'id'        => $spam_icon_id,
			'time'      => __( 'Date' ),
			'spamy'     => __( 'Ip / Email ' ),
			'spamkey'   => __( 'Key' ),
			'spamtype'  => $spam_icon_message,
			'spamvalue' => '',
		);
		return $columns;
	}

	/**
	 * Get hidden columns function.
	 *
	 * @package Spam Master
	 *
	 * @return array
	 */
	public function get_hidden_columns() {
		// Setup Hidden columns and return them.
		return array();
	}

	/**
	 * Get sortable columns function.
	 *
	 * @package Spam Master
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'time'     => array( 'time', false ),
			'spamy'    => array( 'spamy', false ),
			'spamkey'  => array( 'spamkey', false ),
			'spamtype' => array( 'spamtype', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Get records function.
	 *
	 * @package Spam Master
	 *
	 * @param per_page    $per_page to get.
	 * @param page_number $page_number to get.
	 *
	 * @return array
	 */
	public function get_records( $per_page, $page_number ) {
		global $wpdb, $blog_id;
		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		wp_verify_nonce( 'spam-master-options-logs', 'spam-master-options-logs' );
		// Prepare sql.
		$spam_sql = "SELECT * FROM {$spam_master_keys} WHERE spamkey != 'Option'";

		// Prepare open search.
		if ( ! empty( $_REQUEST['s'] ) ) {
			$spam_search = wp_kses_post( wp_unslash( $_REQUEST['s'] ) );
			$search_logs = esc_sql( $spam_search );
			$spam_sql   .= " AND (time LIKE '%" . $search_logs . "%' OR spamkey LIKE '%" . $search_logs . "%' OR spamtype LIKE '%" . $search_logs . "%' OR spamy LIKE '%" . $search_logs . "%' OR spamvalue LIKE '%" . $search_logs . "%')";
		}

		// Prepare order by.
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$spam_orderby = wp_kses_post( wp_unslash( $_REQUEST['orderby'] ) );
			// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
			$spam_sql .= " ORDER BY " . esc_sql( $spam_orderby );
			$spam_sql .= ! empty( wp_kses_post( wp_unslash( $_REQUEST['order'] ) ) ) ? ' ' . esc_sql( wp_kses_post( wp_unslash( $_REQUEST['order'] ) ) ) : ' DESC';
		} else {
			// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
			$spam_sql .= " ORDER BY time";
			if ( isset( $_REQUEST['order'] ) ) {
				$spam_sql .= ! empty( wp_kses_post( wp_unslash( $_REQUEST['order'] ) ) ) ? ' ' . esc_sql( wp_kses_post( wp_unslash( $_REQUEST['order'] ) ) ) : ' DESC';
			} else {
				// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
				$spam_sql .= " DESC";
			}
		}
		$spam_sql .= " LIMIT $per_page";
		// phpcs:ignore Squiz.Strings.DoubleQuoteUsage.NotRequired
		$spam_sql .= " OFFSET " . ( $page_number - 1 ) * $per_page;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_results( $spam_sql, 'ARRAY_A' );
		return $result;
	}

	/**
	 * Url mangle start function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function spam_url_scheme_start() {
		add_filter( 'set_url_scheme', array( $this, 'spam_url_scheme' ), 10, 3 );
	}

	/**
	 * Url mangle stop function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function spam_url_scheme_stop() {
		remove_filter( 'set_url_scheme', array( $this, 'spam_url_scheme' ), 10 );
	}

	/**
	 * Url mangle function.
	 *
	 * @package Spam Master
	 *
	 * @param url         $url to mangle.
	 * @param scheme      $scheme to mangle.
	 * @param orig_scheme $orig_scheme to mangle.
	 *
	 * @return string
	 */
	public function spam_url_scheme( string $url, string $scheme, $orig_scheme ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		wp_verify_nonce( 'spam-master-options-logs', 'spam-master-options-logs' );
		if ( isset( $_REQUEST['s'] ) ) {
			if ( ! empty( $url ) && mb_strpos( $url, '?page=spam-master.php' ) !== false && ! empty( wp_kses_post( wp_unslash( $_REQUEST['s'] ) ) ) ) {
				$url = add_query_arg( 's', rawurlencode( wp_kses_post( wp_unslash( $_REQUEST['s'] ) ) ), $url );
			}
		}
		return( $url );
	}

	/**
	 * Prepare items function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function prepare_items() {
		global $wpdb, $blog_id;
		$this->_column_headers = $this->get_column_info();
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$primary               = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable,
			$primary,
		);
		// Process bulk action.
		$this->process_bulk_action();
		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_logs_display_number = $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_logs_display_number'" );
		$per_page                        = $this->get_items_per_page( 'records_per_page', $spam_master_logs_display_number );
		if ( empty( $per_page ) ) {
			$per_page = '25';
		}
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		$data         = self::get_records( $per_page, $current_page );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$this->items = $data;
	}

	/**
	 * No items function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No records found.', 'spam-master' );
	}

	/**
	 * Display function.
	 *
	 * @package Spam Master
	 *
	 * @return void
	 */
	public function display_rows_or_placeholder() {
		global $wpdb, $blog_id;
		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$spam_master_db_protection_hash = substr( $wpdb->get_var( "SELECT spamvalue FROM {$spam_master_keys} WHERE spamkey = 'Option' AND spamtype = 'spam_master_db_protection_hash'" ), 0, 64 );
		$spam_master_hash               = $spam_master_db_protection_hash;
		// Get the records registered in the prepare_items method.
		$records = $this->items;
		// Get the columns registered in the get_columns and get_sortable_columns methods.
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
		// Loop for each record.
		if ( ! empty( $records ) ) {
			foreach ( $records as $key ) {
				$id           = $key['id'];
				$time         = $key['time'];
				$spamkey      = $key['spamkey'];
				$spamtype     = $key['spamtype'];
				$spamypre     = $key['spamy'];
				$spamvaluepre = $key['spamvalue'];
				if ( 'Cache' === $spamtype ) {
					$spamtype = __( 'Blocked by Spam Master RBL real-time online scan', 'spam-master' );
				}
				if ( 'System' === $spamkey ) {
					$spamkeyid   = '<span class="dashicons dashicons-admin-generic spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Approved by Firewall', 'spam-master' ) . '"></span>';
					$spamkeyname = 'System Approved';
				}
				if ( 'White' === $spamkey ) {
					$spamkeyid   = '<span class="dashicons dashicons-thumbs-up spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Whitelisted by Firewall', 'spam-master' ) . '"></span>';
					$spamkeyname = 'System Approved';
					$spamtype    = __( 'Whitelist Approved', 'spam-master' );
				}
				if ( 'Buffer' === $spamkey ) {
					$spamkeyid   = '<span class="dashicons dashicons-warning spam-master-admin-f32 spam-master-admin-red spam-master-middle" title="' . __( 'Blocked by Firewall', 'spam-master' ) . '"></span>';
					$spamkeyname = 'Firewall Blocked';
					$spamvalue   = '<a class="thickbox" title="' . __( 'Details of ', 'spam-master' ) . esc_attr( $spamypre ) . '" href="#TB_inline?&width=630&height=335&inlineId=' . esc_attr( $id ) . '" class="thickbox"><span class="dashicons dashicons-visibility spam-master-admin-f32 spam-master-admin-blue spam-master-middle" title="' . __( 'View details of ', 'spam-master' ) . esc_attr( $spamypre ) . '"></span></a>';
					if ( filter_var( $spamypre, FILTER_VALIDATE_IP ) ) {
						$spamy     = '<span class="dashicons dashicons-editor-ol spam-master-admin-f32 spam-master-admin-red spam-master-middle" title="' . __( 'Ip Blocked', 'spam-master' ) . '"></span>' . $spamypre;
						$spamy_pop = __( 'Ip ', 'spam-master' ) . $spamypre;
					} else {
						if ( filter_var( $spamypre, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
							$spamy     = '<span class="dashicons dashicons-editor-ol-rtl spam-master-admin-f32 spam-master-admin-red spam-master-middle" title="' . __( 'Ipv6 Blocked', 'spam-master' ) . '"></span>' . $spamypre;
							$spamy_pop = __( 'Ipv6 ', 'spam-master' ) . $spamypre;
						} else {
							if ( filter_var( $spamypre, FILTER_VALIDATE_EMAIL ) ) {
								$spamy     = '<span class="dashicons dashicons-email-alt spam-master-admin-f32 spam-master-admin-red spam-master-middle" title="' . __( 'Email Blocked', 'spam-master' ) . '"></span>' . $spamypre;
								$spamy_pop = __( 'Email ', 'spam-master' ) . $spamypre;
							} else {
								$spamy     = '<span class="dashicons dashicons-admin-site-alt3 spam-master-admin-f32 spam-master-admin-red spam-master-middle" title="' . __( 'Blocked', 'spam-master' ) . '"></span>' . $spamypre;
								$spamy_pop = __( 'Blocked ', 'spam-master' ) . $spamypre;
							}
						}
					}
				} else {
					$spamvalue = false;
					if ( filter_var( $spamypre, FILTER_VALIDATE_IP ) ) {
						$spamy = '<span class="dashicons dashicons-editor-ol spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Ip Approved', 'spam-master' ) . '"></span>' . $spamypre;
					} else {
						if ( filter_var( $spamypre, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
							$spamy = '<span class="dashicons dashicons-editor-ol-rtl spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Ipv6 Approved', 'spam-master' ) . '"></span>' . $spamypre;
						} else {
							if ( filter_var( $spamypre, FILTER_VALIDATE_EMAIL ) ) {
								$spamy = '<span class="dashicons dashicons-email-alt spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Email Approved', 'spam-master' ) . '"></span>' . $spamypre;
							} else {
								$spamy = '<span class="dashicons dashicons-database-view spam-master-admin-f32 spam-master-admin-green spam-master-middle" title="' . __( 'Approved', 'spam-master' ) . '"></span>' . $spamypre;
							}
						}
					}
				}

				$selected_allowed = array(
					'span'   => array(
						'class' => array(),
						'title' => array(),
					),
					'strong' => array(),
					'tr'     => array(
						'class' => array(),
					),
					'a'      => array(
						'href'   => array(),
						'class'  => array(),
						'title'  => array(),
						'target' => array(),
					),
				);

				// Open the line.
				static $row_class = '';
				$row_class        = ( '' === $row_class ? ' class="alternate"' : '' );
				echo '<tr ' . wp_kses( $row_class, $selected_allowed ) . '>';
				foreach ( $columns as $column_name => $column_display_name ) {

					// Style attributes for each col.
					$class      = 'class="' . $column_name . ' column-' . $column_name . '"';
					$style      = '';
					$attributes = '';
					if ( in_array( $column_name, $hidden, true ) ) {
						$style      = ' style="display:none;"';
						$attributes = $style;
					}

					// Display the cell.
					switch ( $column_name ) {
						case 'id':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-id spam-master-middle spam-master-text-center">' . wp_kses( $spamkeyid, $selected_allowed ) . '</td>';
							break;
						case 'time':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-date spam-master-middle">' . esc_html( $time ) . '</td>';
							break;
						case 'spamy':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-spamy spam-master-middle">' . wp_kses( $spamy, $selected_allowed ) . '</td>';
							break;
						case 'spamkey':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-key spam-master-middle">' . esc_html( $spamkeyname ) . '</td>';
							break;
						case 'spamtype':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-type spam-master-middle">' . esc_html( $spamtype ) . '</td>';
							break;
						case 'spamvalue':
							echo '<td class="' . esc_attr( $column_name ) . ' column-' . esc_attr( $column_name ) . ' spam-master-table-column-value spam-master-middle spam-master-text-center">';
							// Prepare pop up.
							?>
<div id="<?php echo esc_attr( $id ); ?>" style="display:none;">
	<table class="wp-list-table widefat fixed striped table-view-list" cellspacing="0">';
			<thead colspan="2">
			<th class="spam-master-middle">
							<?php echo wp_kses( '<strong>' . $spamypre . __( ' Threat Detected ', 'spam-master' ) . '</strong>', $selected_allowed ); ?>
			</th>
		</thead>
		<tfoot colspan="2">
			<th>
				<small><strong><?php echo esc_html( __( 'More options visit ', 'spam-master' ) ) . '<a href="https://www.spammaster.org" target="_blank" title="' . esc_html( __( 'More options visit www.spammaster.org', 'spam-master' ) ) . '">' . esc_html( __( 'www.spammaster.org', 'spam-master' ) ) . '</a>'; ?></strong></small>
			</th>
		</tfoot>
		<tbody>
			<tr class="alternate">
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-shield-alt spam-master-admin-red"></span>' . __( 'Action: ', 'spam-master' ) . $spamkeyname, $selected_allowed ); ?>
				</td>
			</tr>
			<tr>
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-shield"></span>' . __( 'Threat: ', 'spam-master' ) . $spamy_pop, $selected_allowed ); ?>
				</td>
			</tr>
			<tr class="alternate">
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-clock"></span>' . __( 'Date: ', 'spam-master' ) . $time, $selected_allowed ); ?>
				</td>
			</tr>
			<tr>
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-format-status"></span>' . __( 'Message: ', 'spam-master' ) . $spamtype, $selected_allowed ); ?>
				</td>
			</tr>
			<tr class="alternate">
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-database-view"></span>' . __( 'Buffer: ', 'spam-master' ) . $spamvaluepre . __( ' cache level 12 months', 'spam-master' ), $selected_allowed ); ?>
				</td>
			</tr>
			<tr>
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-search"></span>' . __( 'Analysis: ', 'spam-master' ) . '<a href="https://www.spammaster.org/search-threat/?search_spam_threat=' . $spamypre . '&hash=' . $spam_master_hash . '" target="_blank" title="' . __( 'Full threat report and logs', 'spam-master' ) . '">' . __( 'Full threat report and logs', 'spam-master' ) . '</a>', $selected_allowed ); ?>
				</td>
			</tr>
			<tr class="alternate">
				<td>
							<?php echo wp_kses( '<span class="dashicons dashicons-admin-network"></span>' . __( 'Hash: Spam Master security hash ', 'spam-master' ) . wp_hash( $spamypre, 'nouce' ), $selected_allowed ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
							<?php
							echo wp_kses( $spamvalue, $selected_allowed );
							echo '</td>';
							break;
					}
				}
				echo '</tr>';
			}
		} else {
			echo '<tr>';
			echo '<td colspan="6">' . esc_html( 'No records found.' ) . '</td>';
			echo '</tr>';
		}
		echo '</tr>';
	}

	/**
	 * Record count function.
	 *
	 * @package Spam Master
	 *
	 * @return array
	 */
	public static function record_count() {
		global $wpdb, $blog_id;
		// Add Table & Load Spam Master Options.
		if ( is_multisite() ) {
			$spam_master_keys = $wpdb->get_blog_prefix( $blog_id ) . 'spam_master_keys';
		} else {
			$spam_master_keys = $wpdb->prefix . 'spam_master_keys';
		}
		wp_verify_nonce( 'spam-master-options-logs', 'spam-master-options-logs' );
		if ( ! empty( $_REQUEST['s'] ) ) {
			$search_logs = esc_sql( wp_kses_post( wp_unslash( $_REQUEST['s'] ) ) );
			$spam_sql    = "SELECT COUNT(*) FROM {$spam_master_keys}";
			$spam_sql   .= " WHERE spamkey != 'Option' AND (id LIKE '%" . $search_logs . "%' OR time LIKE '%" . $search_logs . "%' OR spamkey LIKE '%" . $search_logs . "%' OR spamtype LIKE '%" . $search_logs . "%' OR spamy LIKE '%" . $search_logs . "%' OR spamvalue LIKE '%" . $search_logs . "%')";
		} else {
			$spam_sql = "SELECT COUNT(*) FROM {$spam_master_keys} WHERE spamkey != 'Option'";
		}
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var( $spam_sql );
	}

	/**
	 * Search box function.
	 *
	 * @package Spam Master
	 *
	 * @param text     $text to get.
	 * @param input_id $input_id to get.
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}
		$input_id = $input_id . '-search-input';
		wp_verify_nonce( 'spam-master-options-logs', 'spam-master-options-logs' );
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( wp_kses_post( wp_unslash( $_REQUEST['orderby'] ) ) ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( wp_kses_post( wp_unslash( $_REQUEST['order'] ) ) ) . '" />';
		}
		if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( wp_kses_post( wp_unslash( $_REQUEST['post_mime_type'] ) ) ) . '" />';
		}
		if ( ! empty( $_REQUEST['detached'] ) ) {
			echo '<input type="hidden" name="detached" value="' . esc_attr( wp_kses_post( wp_unslash( $_REQUEST['detached'] ) ) ) . '" />';
		}
		?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
	<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
		<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
</p>
		<?php
	}

}
