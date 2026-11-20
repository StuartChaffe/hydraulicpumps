<?php
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$logos = get_field('logos');

if ( $logos ) : ?>
<section class="block logos">
	<div class="container container--content">
		<ul class="logos__list" role="list">
			<?php foreach ( $logos as $logo ) : ?>
				<?php if ( empty( $logo['image'] ) ) continue; ?>
				<?php
				// Alt from the field, falling back to the media library alt
				$alt = $logo['alt'] ?: get_post_meta( $logo['image'], '_wp_attachment_image_alt', true );
				?>
				<li class="logos__item">
					<?php echo wp_get_attachment_image( $logo['image'], 'medium', false, array( 'class' => 'logos__image', 'alt' => $alt, 'sizes' => '200px' ) ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php elseif ( is_admin() ) : ?>
<section class="block logos">
	<div class="container container--content"><p>Add some logos.</p></div>
</section>
<?php endif; ?>
