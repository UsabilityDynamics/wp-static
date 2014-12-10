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
       * 
       */
      public function template_redirect() {
        
        if ( !is_user_logged_in() ) {
          if ( get_option( 'static-html-activate' ) == 'true' ) {
            $post = $this->get_asset('static_html');
            die( $post['post_content'] );
          }
        }
        
      }
      
    }
    
  }

}