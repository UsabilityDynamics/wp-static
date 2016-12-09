<?php
/**
 *
 * @author usabilitydynamics@UD
 * @version 1.0.0
 * @module UsabilityDynamics\AMD
 */
namespace UsabilityDynamics\WPStatic {

  if( !class_exists( '\UsabilityDynamics\WPStatic\App' ) ) {

    class App extends \UsabilityDynamics\WPStatic\Scaffold {
    
      /**
       * Constructor
       *
       * @param array $args
       * @param null  $context
       */
      public function __construct( $args = array(), $context = null ) {
      
        parent::__construct( $args, $context );
        
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        
      }
      
      /**
       * Template redirect actions
       *
       * Does not force redirection to home page when Post Preview (_ppp) is used.  - potanin@UD
       *
       */
      public function template_redirect() {
        
        //** Redirect if enabled static html */ 
        if ( !is_user_logged_in() && !isset( $_GET['_ppp'] ) ) {
          if ( get_option( 'static-html-activate' ) == 'true' ) {
            if ( !is_front_page() ) {
              wp_redirect( get_home_url( get_current_blog_id() ) ); 
            }
            $_post = $this->get_asset('static_html');
            die( $_post['post_content'] );
          }
        }
        
        //** Preview function */
        if ( !empty( $_GET['static-html-preview'] ) ) {
          $_post = $this->get_asset('static_html');
          die( $_post['post_content'] );
        }
        
      }
      
    }
    
  }

}
