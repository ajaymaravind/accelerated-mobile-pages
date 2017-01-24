<?php
function ampforwp_page_template_redirect() {
	global $redux_builder_amp;
	if($redux_builder_amp['amp-mobile-redirection']){

    if( wp_is_mobile() && $_SESSION['isAMPforWPmobileRedirectON']=='mobile-on' && $_GET['isAMPforWPmobileRedirectLogout']==1){
      if ( ampforwp_is_amp_endpoint() ) {
        //do nothing
        session_destroy();
      }
    }

		if ( wp_is_mobile()) {
      session_start();
      $_SESSION['isAMPforWPmobileRedirectON']='mobile-on';
			if ( ampforwp_is_amp_endpoint() ) {
				return;
			} else {
        if( !isset($_SESSION['isAMPforWPmobileRedirectON']) || !isset($_GET['isAMPforWPmobileRedirectLogout']) ){
        session_start();
        $_SESSION['isAMPforWPmobileRedirectON']='mobile-on';
        if ( is_home() ) {
					wp_redirect( trailingslashit( esc_url( home_url() ) ) . AMP_QUERY_VAR ,  301 );
					exit();
				}
        elseif ( is_archive() ) {
          global $wp;
          $current_archive_url = home_url( $wp->request );
          wp_redirect( trailingslashit( esc_url( $current_archive_url ) ) . AMP_QUERY_VAR , 301 );
          exit();
				} else {
					wp_redirect( trailingslashit( esc_url( ( get_permalink( $id ) ) ) ) . AMP_QUERY_VAR , 301 );
					exit();
				}
			}
      }
		}
	}
}

add_action( 'template_redirect', 'ampforwp_page_template_redirect', 30 );

add_action( 'template_redirect', 'ampforwp_page_template_redirect_archive', 10 );
function ampforwp_page_template_redirect_archive() {

	if ( is_404() ) {
		if( ampforwp_is_amp_endpoint() ) {
			global $wp;
			$ampforwp_404_url 	= add_query_arg( '', '', home_url( $wp->request ) );
			$ampforwp_404_url	= trailingslashit($ampforwp_404_url );
				$ampforwp_404_url = dirname($ampforwp_404_url);
			wp_redirect( esc_url( $ampforwp_404_url )  , 301 );
			exit();
		}
	}
}

// Add Custom Rewrite Rule to make sure pagination & redirection is working correctly
function ampforwp_add_custom_rewrite_rules() {
	// For Homepage with Pagination
    add_rewrite_rule(
        'amp/page/([0-9]{1,})/?$',
        'index.php?amp&paged=$matches[1]',
        'top'
    );
    // For Homepage
    add_rewrite_rule(
        'amp/?$',
        'index.php?amp',
        'top'
    );
    // For category pages
    add_rewrite_rule(
        'category\/(.+?)\/amp\/page\/?([0-9]{1,})\/?$',
        'index.php?amp&category_name=$matches[1]&paged=$matches[2]',
        'top'
    );
    // For tag pages
    add_rewrite_rule(
        'tag\/(.+?)\/amp\/page\/?([0-9]{1,})\/?$',
        'index.php?amp&tag=$matches[1]&paged=$matches[2]',
        'top'
    );
}
add_action( 'init', 'ampforwp_add_custom_rewrite_rules' );



function ampforwp_is_amp_endpoint() {
	return false !== get_query_var( 'amp', false );
}
