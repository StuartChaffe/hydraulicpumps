<?php
/**
 * Pump finder: logs the searches that found nothing, so HPUK can see which
 * units people are asking about. Pumps → Not found lists them, most searched
 * first, with a link to add one as a pump. Adding a pump clears its entry.
 *
 * Stored in {prefix}hp_pump_misses: one row per search key (the term in
 * capitals with spaces and punctuation removed), counting repeat searches.
 */

const HP_PUMP_MISSES_DB_VERSION = '1';

function hp_pump_misses_table() {
	global $wpdb;
	return $wpdb->prefix . 'hp_pump_misses';
}

/**
 * Create the table the first time (and if its structure ever changes).
 */
add_action( 'admin_init', 'hp_pump_misses_install' );
add_action( 'template_redirect', 'hp_pump_misses_install' );
function hp_pump_misses_install() {
	if ( get_option( 'hp_pump_misses_db_version' ) === HP_PUMP_MISSES_DB_VERSION ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( 'CREATE TABLE ' . hp_pump_misses_table() . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		search_key varchar(191) NOT NULL,
		term varchar(191) NOT NULL,
		count int(10) unsigned NOT NULL DEFAULT 1,
		first_searched datetime NOT NULL,
		last_searched datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY search_key (search_key),
		KEY count (count)
	) {$wpdb->get_charset_collate()};" );
	update_option( 'hp_pump_misses_db_version', HP_PUMP_MISSES_DB_VERSION );
}

/**
 * Log a search that found nothing. Skips logged-in editors (testing) and bots.
 */
function hp_pump_finder_log_miss( $term ) {
	$key = hp_pump_search_key( $term );
	if ( strlen( $key ) < 2 || current_user_can( 'edit_posts' ) ) {
		return;
	}
	$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	if ( $agent === '' || preg_match( '/bot|crawl|spider|slurp|preview|monitor/i', $agent ) ) {
		return;
	}

	global $wpdb;
	$now = current_time( 'mysql' );
	$wpdb->query( $wpdb->prepare(
		'INSERT INTO ' . hp_pump_misses_table() . ' (search_key, term, count, first_searched, last_searched)
		VALUES (%s, %s, 1, %s, %s)
		ON DUPLICATE KEY UPDATE count = count + 1, term = VALUES(term), last_searched = VALUES(last_searched)',
		substr( $key, 0, 191 ),
		mb_substr( $term, 0, 191 ),
		$now,
		$now
	) );
}

/**
 * A pump has been added (or renamed): it's no longer missing.
 */
add_action( 'save_post_pump', function ( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || $post->post_status !== 'publish' ) {
		return;
	}
	global $wpdb;
	$wpdb->delete( hp_pump_misses_table(), array( 'search_key' => hp_pump_search_key( $post->post_title ) ) );
}, 20, 2 );

/**
 * Pumps → Not found
 */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'edit.php?post_type=pump',
		__( 'Pumps not found', 'origin' ),
		__( 'Not found', 'origin' ),
		'edit_posts',
		'pump-misses',
		'hp_pump_misses_page'
	);
} );

/**
 * Delete one entry, or clear them all.
 */
add_action( 'admin_post_hp_pump_misses_delete', function () {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'Sorry, you can’t do that.', 'origin' ) );
	}
	check_admin_referer( 'hp_pump_misses_delete' );
	global $wpdb;
	$id = absint( $_POST['id'] ?? 0 );
	if ( $id ) {
		$wpdb->delete( hp_pump_misses_table(), array( 'id' => $id ) );
	} elseif ( ! empty( $_POST['all'] ) ) {
		$wpdb->query( 'TRUNCATE TABLE ' . hp_pump_misses_table() );
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=pump&page=pump-misses&deleted=1' ) );
	exit;
} );

function hp_pump_misses_page() {
	global $wpdb;
	$table    = hp_pump_misses_table();
	$per_page = 50;
	$paged    = max( 1, absint( $_GET['paged'] ?? 1 ) );
	$orderby  = ( $_GET['orderby'] ?? '' ) === 'last_searched' ? 'last_searched' : 'count';
	$total    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	$rows     = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM {$table} ORDER BY {$orderby} DESC, last_searched DESC LIMIT %d OFFSET %d",
		$per_page,
		( $paged - 1 ) * $per_page
	) );
	$base     = admin_url( 'edit.php?post_type=pump&page=pump-misses' );
	$sort_url = fn( $by ) => esc_url( add_query_arg( 'orderby', $by, $base ) );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Pumps not found', 'origin' ); ?></h1>
		<p><?php esc_html_e( 'Pump finder searches that found nothing, most searched first. Adding a pump with that name removes it from this list. Searches by logged-in editors aren’t logged.', 'origin' ); ?></p>

		<?php if ( isset( $_GET['deleted'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Deleted.', 'origin' ); ?></p></div>
		<?php endif; ?>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Searched for', 'origin' ); ?></th>
					<th scope="col" class="<?php echo $orderby === 'count' ? 'sorted desc' : 'sortable desc'; ?>" style="width:9rem"><a href="<?php echo $sort_url( 'count' ); ?>"><span><?php esc_html_e( 'Searches', 'origin' ); ?></span></a></th>
					<th scope="col" class="<?php echo $orderby === 'last_searched' ? 'sorted desc' : 'sortable desc'; ?>" style="width:13rem"><a href="<?php echo $sort_url( 'last_searched' ); ?>"><span><?php esc_html_e( 'Last searched', 'origin' ); ?></span></a></th>
					<th scope="col" style="width:13rem"><?php esc_html_e( 'First searched', 'origin' ); ?></th>
					<th scope="col" style="width:14rem"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'origin' ); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! $rows ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'Nothing yet.', 'origin' ); ?></td></tr>
				<?php endif; ?>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $row->term ); ?></strong></td>
						<td><?php echo esc_html( number_format_i18n( $row->count ) ); ?></td>
						<td><?php echo esc_html( mysql2date( 'j M Y, H:i', $row->last_searched ) ); ?></td>
						<td><?php echo esc_html( mysql2date( 'j M Y', $row->first_searched ) ); ?></td>
						<td>
							<a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'post_type' => 'pump', 'post_title' => rawurlencode( strtoupper( $row->term ) ) ), admin_url( 'post-new.php' ) ) ); ?>"><?php esc_html_e( 'Add as a pump', 'origin' ); ?></a>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
								<?php wp_nonce_field( 'hp_pump_misses_delete' ); ?>
								<input type="hidden" name="action" value="hp_pump_misses_delete">
								<input type="hidden" name="id" value="<?php echo esc_attr( $row->id ); ?>">
								<button type="submit" class="button-link button-link-delete"><?php esc_html_e( 'Delete', 'origin' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		$pages = (int) ceil( $total / $per_page );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">' . paginate_links( array(
				'base'    => add_query_arg( 'paged', '%#%', add_query_arg( 'orderby', $orderby, $base ) ),
				'format'  => '',
				'current' => $paged,
				'total'   => $pages,
			) ) . '</div></div>';
		}
		?>

		<?php if ( $rows ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem" onsubmit="return confirm('<?php echo esc_js( __( 'Clear the whole list? This can’t be undone.', 'origin' ) ); ?>');">
				<?php wp_nonce_field( 'hp_pump_misses_delete' ); ?>
				<input type="hidden" name="action" value="hp_pump_misses_delete">
				<input type="hidden" name="all" value="1">
				<button type="submit" class="button"><?php esc_html_e( 'Clear the list', 'origin' ); ?></button>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
