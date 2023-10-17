<?php
/**
 * Property Features Checkboxes
 */

/* all property features terms */
$all_features = get_terms(array( 'taxonomy' => 'property-feature' ));

if ( ! empty( $all_features ) && ! is_wp_error( $all_features ) ) {

	/* features in search query */
	$required_features_slugs = array();
	if (isset($_GET['features'])) {
		$required_features_slugs = $_GET['features'];
	}
    ?>
<div class="more-options-mode-container">
	<div class="more-options-wrapper more-options-wrapper-mode clearfix <?php echo (count($required_features_slugs) > 0)? '': 'collapsed'; ?>">
		<?php
        foreach ($all_features as $feature) {
            ?>
			<div class="option-bar">
				<input type="checkbox"
				       id="feature-<?php echo esc_attr( rawurldecode( $feature->slug ) ); ?>"
				       name="features[]"
				       value="<?php echo esc_attr( rawurldecode( $feature->slug ) ); ?>"
					<?php if ( in_array( rawurldecode( $feature->slug ), $required_features_slugs ) ) {
                echo 'checked';
            } ?> />
				<label for="feature-<?php echo esc_attr( rawurldecode( $feature->slug ) ); ?>"><?php echo ucwords( $feature->name ); ?> <small>(<?php echo esc_html( $feature->count ); ?>)</small></label>
			</div>
			<?php

        } ?>
	</div>
    <span class="open_more_features">
        	<?php
	        $inspiry_search_features_title = get_option( 'inspiry_search_features_title' );
	        if ( $inspiry_search_features_title ) {
		        echo esc_html( $inspiry_search_features_title );
	        } else {
		        esc_html_e( 'Looking for certain features', 'framework' );
	        }
	        ?>
    </span>

</div>
	<?php

}
