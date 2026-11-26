<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead
// Author Bio: a round photo (or the author's initials on navy) beside a small
// red label, their name and role, a short bio, and their email and phone.
$field = fn( $name ) => origin_block_field( $block, $name, 'field_author_bio_' . $name );
$label = $field('label');
$name  = trim( (string) get_field('name') );
$role  = trim( (string) get_field('role') );
$bio   = get_field('bio');
$photo = get_field('photo');
$email = sanitize_email( (string) get_field('email') );
$phone = trim( (string) get_field('phone') );

if ( ! $name ) :
	if ( is_admin() ) : ?>
		<section class="block author-bio"><div class="container container--content"><p>Add the author’s name in the block settings.</p></div></section>
	<?php endif;
	return;
endif;

// Initials: the first letter of the first and last names, e.g. "MS"
$words    = preg_split( '/\s+/', $name );
$initials = mb_strtoupper( mb_substr( $words[0], 0, 1 ) . ( count( $words ) > 1 ? mb_substr( end( $words ), 0, 1 ) : '' ) );
$contacts = array_filter( array(
	$email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '',
	$phone ? '<a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>' : '',
) );
?>
<section class="block author-bio">
	<div class="container container--content">
		<div class="author-bio__card">
			<div class="author-bio__avatar" aria-hidden="true">
				<?php if ( $photo ) : ?>
					<?php echo wp_get_attachment_image( $photo, 'Square', false, array( 'class' => 'author-bio__photo', 'alt' => '', 'sizes' => '170px' ) ); ?>
				<?php else : ?>
					<span class="author-bio__initials"><?php echo esc_html( $initials ); ?></span>
				<?php endif; ?>
			</div>
			<div class="author-bio__copy">
				<?php if ( $label ) : ?>
					<p class="author-bio__label"><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
				<h2 class="author-bio__name"><?php echo esc_html( $role ? $name . ', ' . $role : $name ); ?></h2>
				<?php if ( $bio ) : ?>
					<p class="author-bio__text"><?php echo wp_kses_post( $bio ); ?></p>
				<?php endif; ?>
				<?php if ( $contacts ) : ?>
					<p class="author-bio__contact"><?php echo implode( '<span class="author-bio__dot" aria-hidden="true">·</span>', $contacts ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
