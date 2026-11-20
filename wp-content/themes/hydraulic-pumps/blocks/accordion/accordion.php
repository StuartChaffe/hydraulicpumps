<?php
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$allowed_blocks = array( 'core/heading', 'core/paragraph' );
$accordion      = get_field('accordion_items');

if ( $accordion ) : ?>
<section class="block accordion">
	<div class="container container--content">
		<?php if ( is_admin() || ! empty( $content ) ) : ?><?php echo '<InnerBlocks allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '" />'; ?><?php endif; ?>
		<div class="accordion__inner">
			<?php foreach ( $accordion as $item ) : ?>
			<details class="accordion__item" id="<?php echo esc_attr( sanitize_title( $item['accordion_title'] ) ); ?>">
				<summary class="accordion__handle">
					<h3 class="accordion__title"><?php echo esc_html( $item['accordion_title'] ); ?></h3>
					<span class="accordion__icon" aria-hidden="true"></span>
				</summary>
				<div class="accordion__body content">
					<?php echo $item['accordion_body']; ?>
				</div>
			</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( ! is_admin() ) :
	$faq_entities = array_map( function( $item ) {
		return array(
			'@type'          => 'Question',
			'name'           => $item['accordion_title'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['accordion_body'] ),
			),
		);
	}, $accordion );
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faq_entities,
	);
?>
<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
<?php endif; ?>

<?php elseif ( is_admin() ) : ?>
<section class="block accordion">
	<div class="container container--content"><p>Add some accordion items.</p></div>
</section>
<?php endif; ?>
