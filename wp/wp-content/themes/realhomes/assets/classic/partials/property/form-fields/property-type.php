<div class="form-option property-type-field-wrapper">
	<label for="type"><?php esc_html_e('Type', 'framework'); ?></label>
    <span class="selectwrap">
        <select name="type[]"
                id="type"
                class="inspiry_select_picker_trigger show-tick"
                title="<?php esc_attr_e( 'None', 'framework' ); ?>"
                <?php
                $inspiry_search_form_multiselect_types = get_option( 'inspiry_search_form_multiselect_types', 'yes' );

                if ( 'yes' == $inspiry_search_form_multiselect_types ) {
	                ?>
                    multiple = "multiple"
                    data-count-selected-text="{0} <?php esc_attr_e( ' Types Selected ', 'framework' ); ?>"
                    data-selected-text-format="count > 2"
	                <?php
                }
                ?>
        >
	        <?php
            if (inspiry_is_edit_property()) {
                global $target_property;
                edit_form_hierarchical_options($target_property->ID, 'property-type');
            } else {
	            if ( 'no' == $inspiry_search_form_multiselect_types ) {
		            ?>
                    <option selected="selected" value="-1"><?php esc_html_e( 'None', 'framework' ); ?></option>
		            <?php
	            }
                /*
                 * Property Type Terms
                 */
                $property_types_terms = get_terms(array(
                        'taxonomy'   => "property-type",
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => false,
                        'parent'     => 0
                    )
                );
                generate_id_based_hirarchical_options("property-type", $property_types_terms, - 1);
            }
            ?>
        </select>
    </span>
</div>
