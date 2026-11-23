<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
$items      = get_field('items');
$link_style = in_array( get_field('link_style'), array( 'button', 'card' ), true ) ? get_field('link_style') : 'link';
$is_tile    = get_field('card_style') === 'tile'; // Image tile: image on top, no background

if ( $items ) :
	$items = array_slice( $items, 0, 4 );
	$count = count( $items );
	?>
<section class="block grid grid--cols-<?php echo esc_attr( $count ); ?> grid--link-<?php echo esc_attr( $link_style ); ?><?php echo $is_tile ? ' grid--tile' : ''; ?>" style="--grid-cols: <?php echo esc_attr( $count ); ?>">
	<?php // Up to 3 cards sit within --content-width; 4 cards, and image tiles, use the full container ?>
	<div class="container<?php echo $count <= 3 && ! $is_tile ? ' container--content' : ''; ?>">
		<ul class="grid__list" role="list">
			<?php foreach ( $items as $item ) :
				$background = $is_tile ? null : ( $item['background_image'] ?? null ); // Tiles have no background
				$image      = $item['image'] ?? null;
				$link       = $item['link'] ?? null;
				$has_media  = ! $is_tile && ( $background || $image ); // The card's mask and image layout
				$has_link   = $link && ! empty( $link['url'] );
				$target     = $has_link && ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : '';
				$card_link  = $has_link && $link_style === 'card';
				?>
				<li class="grid__item<?php echo $has_media ? ' grid__item--has-media' : ''; ?><?php echo $card_link ? ' grid__item--linked' : ''; ?>">
					<?php if ( $background ) : ?>
						<?php echo wp_get_attachment_image( $background, 'large', false, array( 'class' => 'grid__background', 'alt' => '', 'sizes' => '(min-width: 50em) 33vw, 100vw' ) ); ?>
					<?php endif; ?>

					<?php if ( $is_tile && $image ) : // Tiles: the image goes on top ?>
						<?php echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'grid__tile-image', 'alt' => '', 'sizes' => '(min-width: 50em) 50vw, 100vw' ) ); ?>
					<?php elseif ( $is_tile ) : // No image: a navy panel in its place ?>
						<div class="grid__tile-image grid__tile-image--empty" aria-hidden="true"></div>
					<?php endif; ?>

					<div class="grid__content">
						<?php if ( ! empty( $item['title'] ) ) : ?>
							<h3 class="grid__title">
								<?php if ( $card_link ) : // Whole card: the title is the link, stretched over the card ?>
									<a class="grid__card-link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target; ?>><?php echo esc_html( $item['title'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $item['title'] ); ?>
								<?php endif; ?>
							</h3>
						<?php elseif ( $card_link ) : // No title, so the link text is for screen readers only ?>
							<a class="grid__card-link" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target; ?>><span class="sr-only"><?php echo esc_html( $link['title'] ?: 'Find out more' ); ?></span></a>
						<?php endif; ?>

						<?php if ( ! empty( $item['content'] ) ) : ?>
							<div class="grid__text"><?php echo wp_kses_post( wpautop( $item['content'] ) ); ?></div>
						<?php endif; ?>

						<?php if ( $has_link && ! $card_link ) : ?>
							<a class="grid__link btn<?php echo $link_style === 'link' ? ' btn--link' : ''; ?>" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $target; ?>><?php echo esc_html( $link['title'] ?: 'Find out more' ); ?></a>
						<?php endif; ?>
					</div>

					<?php if ( $image && ! $is_tile ) : ?>
						<?php echo wp_get_attachment_image( $image, 'medium_large', false, array( 'class' => 'grid__image', 'sizes' => '(min-width: 50em) 35rem, 100vw' ) ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<?php elseif ( is_admin() ) : ?>
<section class="block grid">
	<div class="container"><p>Add some grid items.</p></div>
</section>
<?php endif; ?>
