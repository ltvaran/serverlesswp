<?php
/**
 * Field: Featured
 *
 * @since    3.0.0
 * @package RH/modern
 */
?>
<p class="checkbox-field">
    <input id="featured" name="featured" type="checkbox" <?php
	if ( realhomes_dashboard_edit_property() ) {
		global $post_meta_data;
		if ( isset( $post_meta_data['REAL_HOMES_featured'] ) && ( 1 == $post_meta_data['REAL_HOMES_featured'][0] ) ) {
			echo 'checked';
		}
	}
	?> />
    <label for="featured"><?php esc_html_e( 'Mark this property as featured property', 'framework' ); ?></label>
</p>