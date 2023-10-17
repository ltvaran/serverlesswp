<?php
/**
 * Footer template
 *
 * @package realhomes
 * @subpackage modern
 */

$get_border_type = get_post_meta( get_the_ID(), 'inspiry_home_sections_border', true );

if ( is_page_template( 'templates/home.php' ) ) {
	if ( $get_border_type == 'diagonal-border' ) {
		$border_class = 'diagonal-border-footer';
	} else {
		$border_class = 'rh_footer__before_fix';
	}
} else {
	$border_class = 'rh_footer__before_fix';
}
?>
<footer class="rh_footer <?php echo esc_attr( $border_class ); ?>">

    <div class="rh_footer__wrap rh_footer--alignCenter rh_footer--paddingBottom">

        <div class="rh_footer__logo">
			<?php
			$logo_enabled = get_option( 'inspiry_enable_footer_logo', 'true' );
			$logo_path    = get_option( 'inspiry_footer_logo' );
			if ( 'true' === $logo_enabled && ! empty( $logo_path ) ) {
				?>
                <a title="<?php bloginfo( 'name' ); ?>" href="<?php echo esc_url( home_url() ); ?>">
                    <img src="<?php echo esc_url( $logo_path ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                </a>
				<?php
			} elseif ( 'true' === $logo_enabled ) {
				?>
                <h2 class="rh_footer__heading">
                    <a href="<?php echo esc_url( home_url() ); ?>" title="<?php bloginfo( 'name' ); ?>">
						<?php bloginfo( 'name' ); ?>
                    </a>
                </h2>
				<?php
			}
			$desc_enabled = get_option( 'inspiry_enable_footer_tagline', 'true' );
			$description  = get_option( 'inspiry_footer_tagline' );
			if ( 'true' === $desc_enabled && $description ) {
				echo '<p class="tag-line"><span class="separator">/</span><span class="text">';
				echo esc_html( $description );
				echo '</span></p>';
			}
			?>
        </div><!-- /.rh_footer__logo -->

		<?php get_template_part( 'assets/modern/partials/footer/social-nav' ); ?>
        <!-- /.rh_footer__social -->

    </div><!-- /.rh_footer__wrap -->

    <div class="rh_footer__wrap rh_footer--alignTop rh_footer--paddingBottom">
		<?php
		$footer_columns = get_option( 'inspiry_footer_columns', '3' );

		switch ( $footer_columns ) {
			case '1' :
				$column_class = 'column-1';
				break;
			case '2' :
				$column_class = 'columns-2';
				break;
			case '4' :
				$column_class = 'columns-4';
				break;
			default:
				$column_class = 'columns-3';
		}
		?>
        <div class="rh_footer__widgets <?php echo esc_attr( $column_class ); ?>">
			<?php get_template_part( 'assets/modern/partials/footer/first-column' ); ?>
        </div><!-- /.rh_footer__widgets -->

		<?php
		if ( intval( $footer_columns ) >= 2 ) {
			?>
            <div class="rh_footer__widgets <?php echo esc_attr( $column_class ); ?>">
				<?php get_template_part( 'assets/modern/partials/footer/second-column' ); ?>
            </div><!-- /.rh_footer__widgets -->
			<?php
		}

		if ( intval( $footer_columns ) >= 3 ) {
			?>
            <div class="rh_footer__widgets <?php echo esc_attr( $column_class ); ?>">
				<?php get_template_part( 'assets/modern/partials/footer/third-column' ); ?>
            </div><!-- /.rh_footer__widgets -->
			<?php
		}

		if ( intval( $footer_columns ) == 4 ) {
			?>
            <div class="rh_footer__widgets <?php echo esc_attr( $column_class ); ?>">
				<?php get_template_part( 'assets/modern/partials/footer/fourth-column' ); ?>
            </div><!-- /.rh_footer__widgets -->
			<?php
		}
		?>
    </div><!-- /.rh_footer__wrap -->

    <div class="rh_footer__wrap rh_footer--space_between">
        <p class="copyrights">
			<?php
			if ( 'true' === get_option( 'inspiry_copyright_text_display', 'true' ) ) {
				$copyrights = apply_filters( 'inspiry_copyright_text', get_option( 'theme_copyright_text' ) );
				if ( ! empty( $copyrights ) ) {
					echo wp_kses( $copyrights, inspiry_allowed_html() );
				} else {
					printf( '&copy; %s. %s', date_i18n( 'Y' ), esc_html__( 'All rights reserved.', 'framework' ) );
				}
			}
			?>
        </p><!-- /.copyrights -->

        <p class="designed-by">
			<?php
			$designed_by = apply_filters( 'inspiry_designed_by_text', get_option( 'theme_designed_by_text' ) );
			echo ( ! empty( $designed_by ) ) ? wp_kses( $designed_by, inspiry_allowed_html() ) : false;
			?>
        </p><!-- /.copyrights -->
    </div><!-- /.rh_footer__wrap -->
</footer><!-- /.rh_footer -->




