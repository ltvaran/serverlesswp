<?php
/**
 * Field: Features
 *
 * @since    3.0.0
 * @package RH/modern
 */
?>
<div class="property-features-fields">
    <label><?php esc_html_e( 'Features', 'framework' ); ?></label>
    <ul class="property-features list-unstyled">
		<?php
        // Existing features of a property
		$property_features_ids = array();
		if ( realhomes_dashboard_edit_property() ) {
			global $target_property;
			$features_terms = get_the_terms( $target_property->ID, 'property-feature' );
			if ( ! empty( $features_terms ) && ! is_wp_error( $features_terms ) ) {
				foreach ( $features_terms as $fet_trms ) {
					$property_features_ids[] = $fet_trms->term_id;
				}
			}
		}

		// Property Features Query
		$features_terms = get_terms( array(
				'taxonomy'   => 'property-feature',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
			)
		);

		if ( ! empty( $features_terms ) && ! is_wp_error( $features_terms ) ) {
			$feature_count = 1;
			foreach ( $features_terms as $feature ) {
				echo '<li class="property-features-item checkbox-field">';
				if ( realhomes_dashboard_edit_property() && in_array( $feature->term_id, $property_features_ids ) ) {
					echo '<input type="checkbox" name="features[]" id="feature-' . esc_attr( $feature_count ) . '" value="' . esc_attr( $feature->term_id ) . '" checked />';
				} else {
					echo '<input type="checkbox" name="features[]" id="feature-' . esc_attr( $feature_count ) . '" value="' . esc_attr( $feature->term_id ) . '" />';
				}
				echo '<label for="feature-' . esc_attr( $feature_count ) . '">' . esc_attr( $feature->name ) . '</label>';
				echo '</li>';
				$feature_count ++;
			}
		}
		?>
    </ul>
</div>