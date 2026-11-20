<?php
/**
 * Pump search form: a white bar with the input and a Search button.
 * Used by the pump finder CTA and the Pump Finder block. Suggests matching
 * pump names as you type (src/js/components/pump-search.js).
 *
 * @param array $args {
 *     @type string $placeholder Input placeholder (also the hidden label).
 *     @type string $value       The current search, if any.
 *     @type string $id          Unique id for the input.
 *     @type string $class       Extra class, e.g. pump-search--large (the Pump Finder page's bigger bar).
 *     @type bool   $is_target   True for the Pump Finder block's form: searches land on it (#pump-finder).
 * }
 */
$placeholder = $args['placeholder'] ?? '' ?: __( 'Search pump name or ID', 'origin' );
$value       = $args['value'] ?? '';
$input_id    = $args['id'] ?? wp_unique_id( 'pump-search-' );
$class       = trim( 'pump-search ' . ( $args['class'] ?? '' ) );
$is_target   = ! empty( $args['is_target'] );
?>
<?php // #pump-finder: the results page opens scrolled to its search bar (GET forms keep the action's #) ?>
<form<?php echo $is_target ? ' id="pump-finder"' : ''; ?> class="<?php echo esc_attr( $class ); ?>" role="search" method="get" action="<?php echo esc_url( hp_pump_finder_url() . '#pump-finder' ); ?>" data-suggest="<?php echo esc_url( rest_url( 'hp/v1/pump-suggestions' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $placeholder ); ?></label>
	<input class="pump-search__input" type="search" id="<?php echo esc_attr( $input_id ); ?>" name="<?php echo esc_attr( HP_PUMP_QUERY_VAR ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="<?php echo esc_attr( $input_id ); ?>-list">
	<button class="pump-search__button btn" type="submit"><?php esc_html_e( 'Search', 'origin' ); ?></button>

	<?php // Suggestions as you type (src/js/components/pump-search.js) ?>
	<ul class="pump-search__suggestions" id="<?php echo esc_attr( $input_id ); ?>-list" role="listbox" aria-label="<?php esc_attr_e( 'Matching pumps', 'origin' ); ?>" hidden></ul>
	<p class="pump-search__status sr-only" aria-live="polite"></p>
</form>
