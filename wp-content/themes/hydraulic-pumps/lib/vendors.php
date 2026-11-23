<?php

/**
 * Gravity Forms: submit buttons as <button class="btn">, so they match the
 * site's red buttons, arrow included (an <input> can't have ::after).
 */
add_filter( 'gform_submit_button', function ( $button, $form ) {
	$text = $form['button']['text'] ?? '';
	return sprintf(
		'<button type="submit" class="gform_button btn gform-theme-no-framework" id="gform_submit_button_%d">%s</button>',
		absint( $form['id'] ),
		esc_html( $text !== '' ? $text : __( 'Submit', 'origin' ) )
	);
}, 10, 2 );
