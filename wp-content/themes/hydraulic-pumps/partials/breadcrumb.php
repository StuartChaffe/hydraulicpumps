<?php
// Breadcrumb bar under the site header. Not on the home page, and each page
// or post can turn it off (Page options > Breadcrumb, on by default).
// The trail comes from origin_breadcrumbs() (lib/breadcrumbs.php), with
// matching BreadcrumbList structured data for search engines.
if ( is_front_page() ) {
	return;
}

// Off for this page? Unset (pages saved before the option existed) counts as on
if ( is_singular() && function_exists( 'get_field' ) && get_field( 'show_breadcrumb', get_queried_object_id() ) === false ) {
	return;
}

$crumbs = origin_breadcrumbs();
if ( count( $crumbs ) < 2 ) {
	return;
}
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'origin' ); ?>">
	<ol class="breadcrumb__list">
		<?php foreach ( $crumbs as $crumb ) : ?>
			<li class="breadcrumb__item">
				<?php if ( $crumb['url'] ) : ?>
					<a class="breadcrumb__link" href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['title'] ); ?></a>
				<?php else : ?>
					<span aria-current="page"><?php echo esc_html( $crumb['title'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
<?php
$schema = array(
	'@context'        => 'https://schema.org',
	'@type'           => 'BreadcrumbList',
	'itemListElement' => array_map( function( $crumb, $i ) {
		$item = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $crumb['title'] );
		if ( $crumb['url'] ) {
			$item['item'] = $crumb['url'];
		}
		return $item;
	}, $crumbs, array_keys( $crumbs ) ),
);
?>
<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
