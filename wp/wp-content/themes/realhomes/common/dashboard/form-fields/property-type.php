<?php
/**
 * Field: Property Type
 *
 * @since    3.0.0
 */
?>
<p>
    <label for="type"><?php esc_html_e( 'Type', 'framework' ); ?></label>
    <select name="type[]" id="type" class="inspiry_select_picker_trigger show-tick"
            data-size="5"
            data-actions-box="true"
		<?php
		$inspiry_search_form_multiselect_types = get_option( 'inspiry_search_form_multiselect_types', 'yes' );
		if ( 'yes' == $inspiry_search_form_multiselect_types ) {
		?>
            data-selected-text-format="count > 2"
            multiple="multiple"
            data-count-selected-text="{0} <?php esc_attr_e( ' Types Selected ', 'framework' ); ?>">
		<?php
		}
		?>
        title="<?php esc_attr_e( 'None', 'framework' ); ?>"
		<?php
		if ( realhomes_dashboard_edit_property() ) {
			global $target_property;
			edit_form_hierarchical_options( $target_property->ID, 'property-type' );
		} else {
			if ( 'no' == $inspiry_search_form_multiselect_types ) {
				?>
                <option selected="selected" value="-1"><?php esc_html_e( 'None', 'framework' ); ?></option>
				<?php
			}
			/**
			 * Property Type Terms
			 */
			$property_types_terms = get_terms( array(
					'taxonomy'   => 'property-type',
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => false,
					'parent'     => 0,
				)
			);
			generate_id_based_hirarchical_options( 'property-type', $property_types_terms, - 1 );
		}
		?>
    </select>
</p>