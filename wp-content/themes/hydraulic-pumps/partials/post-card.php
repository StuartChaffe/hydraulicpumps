<?php
/**
 * Article card (Posts and Featured Posts blocks): image, date (hidden for now), title,
 * excerpt and "Read the article". The title link is stretched over the card
 * so the whole card is clickable.
 *
 * $args:
 *   'post'  WP_Post|int  The post.
 *   'class' string       Extra classes for the card (e.g. MixItUp's).
 */
$card_post = get_post( $args['post'] ?? null );
if ( ! $card_post ) {
	return;
}
$extra = ! empty( $args['class'] ) ? ' ' . $args['class'] : '';
?>
<article class="post-card<?php echo esc_attr( $extra ); ?>">
	<div class="post-card__media">
		<?php if ( has_post_thumbnail( $card_post ) ) : ?>
			<?php echo get_the_post_thumbnail( $card_post, 'Card', array( 'class' => 'post-card__image', 'alt' => '', 'sizes' => '(min-width: 70em) 27rem, (min-width: 50em) 50vw, 100vw' ) ); ?>
		<?php endif; ?>
	</div>
	<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $card_post ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y', $card_post ) ); ?></time>
	<h3 class="post-card__title">
		<a class="post-card__link" href="<?php echo esc_url( get_permalink( $card_post ) ); ?>"><?php echo esc_html( get_the_title( $card_post ) ); ?></a>
	</h3>
	<?php if ( has_excerpt( $card_post ) || $card_post->post_content ) : ?>
		<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $card_post ), 30 ) ); ?></p>
	<?php endif; ?>
	<span class="post-card__more" aria-hidden="true"><?php esc_html_e( 'Read the article', 'origin' ); ?></span>
</article>
