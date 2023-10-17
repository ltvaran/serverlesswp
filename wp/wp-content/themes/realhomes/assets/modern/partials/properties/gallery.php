<?php
/**
 * Gallery
 *
 * @since      3.0.0
 * @package    realhomes
 * @subpackage modern
 */
?>
<section class="rh_section rh_wrap--padding rh_wrap--topPadding">

	<div class="rh_page">

		<?php
		$inspiry_gallery_properties_sorting = get_option( 'inspiry_gallery_properties_sorting', 'hide' );
		if ( 'show' === $inspiry_gallery_properties_sorting ) : ?>
			<div class="rh_page__head">
				<div class="rh_page__controls">
					<?php get_template_part( 'assets/modern/partials/properties/sort-controls' ); ?>
				</div><!-- /.rh_page__controls -->
			</div><!-- /.rh_page__head -->
		<?php endif; ?>

		<div class="rh_page__head">

			<h2 class="rh_page__title">
				<?php $page_title = get_the_title( get_the_ID() ); ?>
				<p class="title"><?php echo esc_html( $page_title ); ?></p>
			</h2><!-- /.rh_page__title -->

			<div id="filter-by" class="rh_page__gallery_filters">
				<a href="#" data-filter="rh_gallery__item" class="active"><?php esc_html_e( 'All', 'framework' ); ?></a><?php
				$status_terms = get_terms(
					array(
						'taxonomy' => 'property-status',
					)
				);

				if ( ! empty( $status_terms ) && is_array( $status_terms ) ) {
					foreach ( $status_terms as $status_term ) {
						echo '<a href="#" data-filter="' . esc_attr( $status_term->slug ) . '" title="' . sprintf( esc_html__( 'View all Properties having %s status', 'framework' ), $status_term->name ) . '">' . esc_html( $status_term->name ) . '</a>';
					}
				}
				?>
			</div><!-- /.rh_page__gallery_filters -->

		</div><!-- /.rh_page__head -->

		<?php
		$get_content_position = get_post_meta( get_the_ID(), 'REAL_HOMES_content_area_above_footer',true );

		if ( $get_content_position !== '1' ) {

			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					?>
                    <div class="rh_content <?php if ( get_the_content() ) {
						echo esc_attr( 'rh_page__content' );
					} ?>">
						<?php the_content(); ?>
                    </div><!-- /.rh_content -->
					<?php
				}
			}
		}
		?>

		<div class="rh_gallery">
			<div class="rh_gallery__wrap isotope">
				<?php
				global $gallery_name;

				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				} elseif ( get_query_var( 'page' ) ) { // if is static front page
					$paged = get_query_var( 'page' );
				}

				// Gallery Query.
				$gallery_listing_args = array(
					'post_type' => 'property',
					'paged'     => $paged,
				);

				/**
				 * Gallery Property Arguments Filter.
				 *
				 * @var array
				 */
				$gallery_listing_args = apply_filters( 'inspiry_gallery_properties_filter', $gallery_listing_args );

				if ( 'show' === $inspiry_gallery_properties_sorting ) {
					$gallery_listing_args = sort_properties( $gallery_listing_args );
				}

				// Gallery Query and Start of Loop.
				$gallery_query = new WP_Query( $gallery_listing_args );
				while ( $gallery_query->have_posts() ) :
					$gallery_query->the_post();

					// Getting list of property status terms.
					$term_list = $gallery_name;
					$terms     = get_the_terms( get_the_ID(), 'property-status' );

					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							$term_list .= ' ';
							$term_list .= $term->slug;
						}
					}

					if ( has_post_thumbnail() ) :
						?>
						<div class="rh_gallery__item isotope-item <?php echo esc_attr( $term_list ); ?>">
							<?php
							$image_id       = get_post_thumbnail_id();
							$full_image_url = wp_get_attachment_url( $image_id );
							global $gallery_image_size;
							$featured_image = wp_get_attachment_image_src( $image_id, $gallery_image_size );
							?>
							<figure>
								<div class="media_container">
									<a class="<?php echo esc_attr( get_lightbox_plugin_class() ); ?> zoom" <?php echo generate_gallery_attribute(); ?> href="<?php echo esc_url( $full_image_url ); ?>" title="<?php the_title_attribute(); ?>">
										<img src="<?php echo esc_url( get_theme_file_uri( INSPIRY_THEME_ASSETS . '/images/icons/icon-zoom.svg' ) ); ?>">
									</a>
									<a class="link" href="<?php the_permalink(); ?>">
										<img src="<?php echo esc_url( get_theme_file_uri( INSPIRY_THEME_ASSETS . '/images/icons/icon-link.svg' ) ); ?>">
									</a>
								</div>
								<?php echo '<img class="img-border" src="' . esc_attr( $featured_image[0] ) . '" alt="' . the_title_attribute( 'echo=0' ) . '">'; ?>
							</figure>
							<h5 class="item-title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
						</div>
						<?php
					endif;
				endwhile;
				wp_reset_postdata();
				?>
			</div><!-- /.rh_gallery__wrap isotope -->
		</div><!-- /.rh_gallery -->

		<?php inspiry_theme_pagination( $gallery_query->max_num_pages ); ?>

	</div><!-- /.rh_page rh_page__main -->

</section>
<?php
if ('1' === $get_content_position ) {

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			?>
            <div class="rh_content <?php if ( get_the_content() ) {echo esc_attr( 'rh_page__content' );} ?>">
				<?php the_content(); ?>
            </div><!-- /.rh_content -->
			<?php
		}
	}
}
?><!-- /.rh_section rh_wrap rh_wrap--padding -->
