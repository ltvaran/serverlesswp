<div class="rh_sticky_header_container">
    <div class="sticky_header_box">
        <div class="header_logo">
			<?php get_template_part( 'assets/classic/partials/header/logo' ); ?>
        </div>
        <div class="main-menu">
			<?php
			if ( has_nav_menu( 'main-menu' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'main-menu',
					'walker'         => new RH_Walker_Nav_Menu(),
					'menu_class'     => 'rh_menu__main_menu clearfix',
					'fallback_cb'    => false // Do not fall back to wp_page_menu()
				) );
			}
			?>
        </div>
        <div class="contact-wrapper">
			<?php
			// Header email.
			$header_email = get_option( 'theme_header_email' );
			if ( ! empty( $header_email ) ) {
				?>
                <div class="sticky-contact-email">
                    <i class="far fa-envelope-open"></i>
                    <a href="mailto:<?php echo antispambot( sanitize_email( $header_email ) ); ?>"><?php echo antispambot( sanitize_email( $header_email ) ); ?></a>
                </div>
				<?php
			}
			?>

			<?php
			$header_phone = get_option( 'theme_header_phone' );
			if ( ! empty( $header_phone ) ) {
				$header_phone_icon = get_option( 'theme_header_phone_icon', 'phone' );
				?>
                <div class="sticky-contact-number"><i class=" <?php
	                if ( 'phone' == $header_phone_icon ) {
		                echo 'fas fa-phone';
	                }else{
		                echo "fab fa-whatsapp";
	                }
	                ?>"></i>

					<?php if ( 'phone' == $header_phone_icon ) {
						$phone_click = "tel://" . esc_attr( $header_phone );
					} else {
						$phone_click = "https://api.whatsapp.com/send?phone=" . esc_attr( $header_phone );
					}
					?>
                    <a class="rh_make_a_call" target="_blank" href="<?php echo esc_url( $phone_click ); ?>"
                       title="<?php esc_attr_e( 'Make a Call', 'framework' ); ?>"><?php echo esc_html( $header_phone ); ?></a>
                </div>
				<?php
			}

			?>
        </div>
    </div>
</div>

