<?php
/**
 * Pump Finder: the search bar and, after a search (?pump=...), the answer.
 *
 * Yes: a tick and heading, the pump (brand and name) and its estimated
 * turnaround, then the numbered next steps and a booking button.
 * No: a cross and heading, what they searched for and what to do next.
 * In the editor both answers show, labelled, with an example pump.
 *
 * @var array $block      The block settings.
 * @var bool  $is_preview True in the editor.
 */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead

$is_preview = ! empty( $is_preview );
$field      = fn( $name ) => origin_block_field( $block, $name, 'field_pump_finder_' . $name );
$term       = $is_preview ? '' : hp_pump_finder_term();
$pump       = $term !== '' ? hp_pump_finder_search( $term ) : null;
$images     = get_template_directory_uri() . '/assets/images/';

// $brand: the pump's Brand term, or null
$yes = function ( $name, $brand, $turnaround ) use ( $field, $images ) {
	$brand_name = $brand ? $brand->name : '';
	$steps  = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $field('steps') ) ) );
	$button = get_field('button') ?: array(
		'url'    => get_permalink( get_page_by_path( 'book-a-repair' ) ) ?: home_url( '/book-a-repair/' ),
		'title'  => __( 'Book my repair', 'origin' ),
		'target' => '',
	);
	$button_url = add_query_arg( HP_PUMP_QUERY_VAR, rawurlencode( $name ), $button['url'] ); // So the booking knows which pump
	?>
	<div class="pump-result pump-result--yes">
		<div class="pump-result__top">
			<div class="pump-result__answer">
				<img class="pump-result__icon" src="<?php echo esc_url( $images . 'tick.svg' ); ?>" width="54" height="48" alt="">
				<h2 class="pump-result__heading"><?php echo esc_html( $field('found_title') ); ?></h2>
			</div>

			<div class="pump-result__pump">
				<p class="pump-result__name">
					<?php if ( $brand_name ) : ?><?php echo esc_html( $brand_name ); ?> <?php endif; ?><strong><?php echo esc_html( $name ); ?></strong>
				</p>
				<?php if ( $turnaround ) : ?>
					<div class="pump-result__turnaround">
						<img src="<?php echo esc_url( $images . 'calendar.svg' ); ?>" width="46" height="48" alt="">
						<p><?php esc_html_e( 'Estimated turnaround', 'origin' ); ?><br><strong><?php echo esc_html( $turnaround ); ?></strong></p>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $steps || $button ) : ?>
			<div class="pump-result__next">
				<?php if ( $heading = $field('steps_heading') ) : ?>
					<h3 class="pump-result__next-heading"><?php echo esc_html( $heading ); ?></h3>
				<?php endif; ?>

				<?php if ( $steps ) : ?>
					<ol class="pump-result__steps" role="list">
						<?php foreach ( $steps as $step ) : ?>
							<li><span><?php echo esc_html( $step ); ?></span></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>

				<a class="btn" href="<?php echo esc_url( $button_url ); ?>"<?php echo ! empty( $button['target'] ) ? ' target="' . esc_attr( $button['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $button['title'] ?: __( 'Book my repair', 'origin' ) ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<?php
};

$no = function ( $name ) use ( $field, $images ) {
	?>
	<div class="pump-result pump-result--no">
		<div class="pump-result__top">
			<div class="pump-result__answer">
				<img class="pump-result__icon" src="<?php echo esc_url( $images . 'cross.svg' ); ?>" width="54" height="56" alt="">
				<h2 class="pump-result__heading"><?php echo esc_html( $field('not_found_title') ); ?></h2>
			</div>

			<div class="pump-result__pump">
				<p class="pump-result__name"><?php esc_html_e( 'You searched for', 'origin' ); ?> <strong><?php echo esc_html( $name ); ?></strong></p>
				<?php if ( $help = $field('not_found_help') ) : ?>
					<div class="pump-result__help"><?php echo wp_kses_post( wpautop( $help ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
};
?>
<section class="block pump-finder">
	<div class="container container--content">
		<?php get_template_part( 'partials/pump-search', null, array(
			'placeholder' => $field('placeholder'),
			'value'       => $term,
			'id'          => 'pump-finder-search',
			'class'       => 'pump-search--large',
			'is_target'   => ! $is_preview,
		) ); ?>

		<?php // The answer. On this page, searches swap it in place (src/js/components/pump-search.js), so it's a live region ?>
		<div class="pump-finder__results" aria-live="polite">
		<?php if ( $is_preview ) : // Editor: both answers, labelled, with an example pump ?>
			<p class="pump-finder__label"><?php esc_html_e( 'When we have the pump', 'origin' ); ?></p>
			<?php $yes( 'A10VSO28DFR', (object) array( 'name' => 'Bosch Rexroth' ), '5–7 working days' ); ?>
			<p class="pump-finder__label"><?php esc_html_e( 'When we don’t', 'origin' ); ?></p>
			<?php $no( 'XYZ123' ); ?>

		<?php elseif ( $pump ) :
			$brands = get_the_terms( $pump, 'pump_brand' );
			$yes( get_the_title( $pump ), $brands && ! is_wp_error( $brands ) ? $brands[0] : null, get_field( 'turnaround', $pump->ID ) );

		elseif ( $term !== '' ) :
			hp_pump_finder_log_miss( $term ); // Pumps → Not found
			$no( $term );
		endif; ?>
		</div>
	</div>
</section>
