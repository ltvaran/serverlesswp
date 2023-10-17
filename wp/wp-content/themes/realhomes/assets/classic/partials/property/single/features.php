<?php
/* Property Features */
$features_terms = get_the_terms( get_the_ID(), 'property-feature' );
if ( ! empty( $features_terms ) ) {
	?>
	<div class="features">
		<?php
		$property_features_title = get_option( 'theme_property_features_title' );
		if ( ! empty( $property_features_title ) ) {
			?><h4 class="title"><?php echo esc_html( $property_features_title ); ?></h4><?php
		}
		$property_features_display = get_option( 'inspiry_property_features_display', 'link' );
		?>
		<ul class="arrow-bullet-list clearfix">
			<?php
			foreach ( $features_terms as $fet_trms ) {
				echo '<li id="rh_property__feature_' . esc_attr( $fet_trms->term_id ) . '">';
				if( 'link' === $property_features_display ) {
					echo '<a href="' . esc_url( get_term_link( $fet_trms->slug, 'property-feature' ) ) . '">' . esc_html( $fet_trms->name ) . '</a>';
				}else{
					echo '<span>' . esc_html( $fet_trms->name ) . '</span>';
				}
				echo '</li>';
			}
			?>
		</ul>
	</div>
	<?php
}

if( inspiry_is_rvr_enabled() ) {

	// RVR - outdoor features
	get_template_part( 'assets/classic/partials/property/single/rvr/outdoor-features' );

	// RVR - optional services
	get_template_part( 'assets/classic/partials/property/single/rvr/optional-services' );

	// RVR - property policies
	get_template_part( 'assets/classic/partials/property/single/rvr/property-policies' );

	// RVR - location surroundings
	get_template_part( 'assets/classic/partials/property/single/rvr/location-surroundings' );

}
?>