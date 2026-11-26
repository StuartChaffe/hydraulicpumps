<?php
/**
 * Before & After: before and after photos of a repaired pump. Drag the handle
 * (or use the arrow keys) to compare; the Before and After buttons slide it
 * all the way across. The pump's details sit underneath. More than one pump
 * makes a carousel with dots (Swiper). See src/js/components/before-after.js.
 * In the editor, the dots show one pump at a time (no carousel).
 *
 * @var array $block      The block settings.
 * @var bool  $is_preview True in the editor.
 */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead

$slides = get_field('slides');
$images = get_template_directory_uri() . '/assets/images/';

if ( ! $slides ) :
	if ( is_admin() ) : ?>
		<section class="block before-after"><div class="container"><p>Add a pump with before and after photos.</p></div></section>
	<?php endif;
	return;
endif;

$is_preview  = ! empty( $is_preview );
$is_carousel = count( $slides ) > 1;
// Editor: one pump at a time, picked with the dots (radio buttons, so it
// works without JavaScript; see before-after.scss)
$editor_tabs = $is_preview && $is_carousel;
$enquire     = get_field('enquire') ?: array(
	'url'    => get_permalink( get_page_by_path( 'book-a-repair' ) ) ?: home_url( '/book-a-repair/' ),
	'title'  => __( 'Enquire about a similar repair', 'origin' ),
	'target' => '',
);
?>
<section class="block before-after<?php echo $editor_tabs ? ' before-after--editor' : ''; ?>">
	<div class="container container--content">
		<div class="before-after__carousel<?php echo $is_carousel ? ' swiper' : ''; ?>">
			<div class="before-after__track<?php echo $is_carousel ? ' swiper-wrapper' : ''; ?>">
				<?php foreach ( $slides as $i => $slide ) :
					$pump_id    = $slide['pump'] ?: 0;
					$brands     = $pump_id ? get_the_terms( $pump_id, 'pump_brand' ) : null;
					$brand      = $brands && ! is_wp_error( $brands ) ? $brands[0] : null;
					$logo       = $brand ? get_field( 'logo', $brand ) : null;
					// The (i) panel: the problem, what we did and the result
					$story      = array_filter( array(
						__( 'The problem', 'origin' ) => trim( (string) ( $slide['problem'] ?? '' ) ),
						__( 'What we did', 'origin' ) => trim( (string) ( $slide['what_we_did'] ?? '' ) ),
						__( 'The result', 'origin' )  => trim( (string) ( $slide['result'] ?? '' ) ),
					) );
					$slide_id   = ( $block['id'] ?? 'before-after' ) . '-' . $i;
					$name       = $pump_id ? get_the_title( $pump_id ) : '';
					$specs      = array_filter( array(
						__( 'Manufacturer', 'origin' ) => $brand ? $brand->name : '',
						__( 'Pump type', 'origin' )    => $pump_id ? get_field( 'pump_type', $pump_id ) : '',
						__( 'Model', 'origin' )        => $name,
						__( 'Turnaround', 'origin' )   => $pump_id ? get_field( 'turnaround', $pump_id ) : '',
						__( 'Warranty', 'origin' )     => $slide['warranty'] ?? '',
					) );
					$label = $name ? sprintf( __( '%s before and after', 'origin' ), $name ) : __( 'Before and after', 'origin' );
					?>
					<div class="before-after__slide<?php echo $is_carousel ? ' swiper-slide' : ''; ?>" role="group" aria-label="<?php echo esc_attr( $label ); ?>">

						<div class="before-after__buttons">
							<button class="btn btn--secondary before-after__show" type="button" data-show="before" aria-controls="<?php echo esc_attr( $slide_id ); ?>"><?php esc_html_e( 'Before', 'origin' ); ?></button>
							<button class="btn btn--secondary before-after__show" type="button" data-show="after" aria-controls="<?php echo esc_attr( $slide_id ); ?>"><?php esc_html_e( 'After', 'origin' ); ?></button>
						</div>

						<?php // --position is how much of the before photo shows, from the left ?>
						<div class="before-after__compare" style="--position: 50%">
							<?php echo wp_get_attachment_image( $slide['after'], 'large', false, array( 'class' => 'before-after__image', 'alt' => $name ? sprintf( __( '%s after repair', 'origin' ), $name ) : __( 'After repair', 'origin' ), 'sizes' => '(min-width: 70em) 1100px, 100vw' ) ); ?>
							<?php echo wp_get_attachment_image( $slide['before'], 'large', false, array( 'class' => 'before-after__image before-after__image--before', 'alt' => $name ? sprintf( __( '%s before repair', 'origin' ), $name ) : __( 'Before repair', 'origin' ), 'sizes' => '(min-width: 70em) 1100px, 100vw' ) ); ?>

							<div class="before-after__handle" aria-hidden="true"></div>
							<div class="before-after__knob" aria-hidden="true"></div>

							<?php // The range input does the dragging and keyboard control, over the whole photo ?>
							<input class="before-after__range swiper-no-swiping" id="<?php echo esc_attr( $slide_id ); ?>" type="range" min="0" max="100" step="1" value="50" autocomplete="off" aria-label="<?php esc_attr_e( 'Compare before and after: amount of before photo shown', 'origin' ); ?>" aria-valuetext="<?php esc_attr_e( 'Half before, half after', 'origin' ); ?>">

							<?php if ( $logo || $story ) : ?>
								<div class="before-after__brand">
									<?php if ( $logo ) : ?>
										<div class="before-after__logo"><?php echo wp_get_attachment_image( $logo, 'medium', false, array( 'alt' => $brand->name ) ); ?></div>
									<?php endif; ?>
									<?php if ( $story ) : ?>
										<button class="before-after__info" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $slide_id ); ?>-story">
											<img src="<?php echo esc_url( $images . 'info.svg' ); ?>" width="42" height="42" alt="<?php esc_attr_e( 'About this repair', 'origin' ); ?>">
										</button>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( $story && ! $is_preview ) : // Covers the photo; the close button returns to it. Not in the editor, which drops the hidden attribute ?>
								<div class="before-after__story" id="<?php echo esc_attr( $slide_id ); ?>-story" role="region" aria-label="<?php esc_attr_e( 'About this repair', 'origin' ); ?>" tabindex="-1" hidden>
									<button class="before-after__close" type="button">
										<img src="<?php echo esc_url( $images . 'close.svg' ); ?>" width="42" height="42" alt="<?php esc_attr_e( 'Close', 'origin' ); ?>">
									</button>
									<?php foreach ( $story as $heading => $text ) : ?>
										<h3 class="before-after__story-heading"><?php echo esc_html( $heading ); ?></h3>
										<?php echo wp_kses_post( wpautop( $text ) ); ?>
									<?php endforeach; ?>
									<?php if ( ! empty( $enquire['url'] ) ) : ?>
										<a class="btn before-after__enquire" href="<?php echo esc_url( $enquire['url'] ); ?>"<?php echo ! empty( $enquire['target'] ) ? ' target="' . esc_attr( $enquire['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $enquire['title'] ?: __( 'Enquire about a similar repair', 'origin' ) ); ?></a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( $specs ) : ?>
							<dl class="before-after__specs">
								<?php foreach ( $specs as $term => $value ) : ?>
									<div><dt><?php echo esc_html( $term ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $is_carousel && ! $is_preview ) : ?>
				<div class="before-after__dots"></div>
			<?php elseif ( $editor_tabs ) : // Editor: radio buttons styled as the dots ?>
				<div class="before-after__dots">
					<?php foreach ( $slides as $i => $slide ) : ?>
						<label class="before-after__dot">
							<input type="radio" name="<?php echo esc_attr( ( $block['id'] ?? 'before-after' ) . '-dot' ); ?>"<?php checked( $i, 0 ); ?>>
							<span class="sr-only"><?php echo esc_html( ! empty( $slide['pump'] ) ? get_the_title( $slide['pump'] ) : sprintf( __( 'Pump %d', 'origin' ), $i + 1 ) ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
