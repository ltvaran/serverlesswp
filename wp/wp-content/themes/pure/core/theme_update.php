<?php
//Theme Updater Function

function gt3_get_product_name (){
    $product = 'Pure - Multipurpose Responsive WordPress Theme';
    return $product;
}

////  Autoupdate theme
function gt3_check_theme_update ( $transient ){
    $slug = basename( get_template_directory() );
    $slug = 'pure';
    global $wp_version;

    if ( empty( $transient->checked ) || empty( $transient->checked[ $slug ] ) || ! empty( $transient->response[ $slug ] ) ) {
        return $transient;
    }    

    $product = gt3_get_product_name();
    $response = wp_remote_post('https://gt3accounts.com/update/upgrade_free.php', array(
        'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
        'sslverify' => false,
        'body' => array(
            'slug' => urlencode($slug),
            'product' => urlencode($product)
        )
    ));

    $response_code = wp_remote_retrieve_response_code( $response );
    $version_info = wp_remote_retrieve_body( $response );    

    if ( $response_code != 200 || is_wp_error( $version_info ) ) {
        return $transient;
    }

    $response = json_decode($version_info,true);
    if ( isset( $response['allow_update'] ) && $response['allow_update'] && isset( $response['transient'] ) 
    && version_compare( $transient->checked[ $slug ], $response['transient']['new_version'], '<') ) {
        $transient->response[ $slug ] = (array) $response['transient'];
    }

    return $transient;
}

add_action( 'pre_set_site_transient_update_themes', 'gt3_check_theme_update', 100 );