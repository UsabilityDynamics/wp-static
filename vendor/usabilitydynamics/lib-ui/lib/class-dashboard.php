<?php
/**
 * Create Dashboard UI
 *
 * Migrated out of DDP.
 * Needs to be refactored.
 *
 * @author potanin@UD
 */
namespace UsabilityDynamics\UI {

  if( !class_exists( 'UsabilityDynamics\UI\Dashboard' ) ) {

    /**
     * Class Dashboard
     *
     */
    class Dashboard {

      /**
       *
       *
       */
      public function __construct() {
        // add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        // add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        // add_action( 'admin_init', array( $this, 'admin_init' ) );
      }

      /**
       *
       *
       */
      public function admin_init() {

        if( !empty( $_REQUEST[ 'hddp_options' ] ) && wp_verify_nonce( $_POST[ 'hddp_save_form' ], 'hddp_save_form' ) ) {
          update_option( 'hddp_options', $_REQUEST[ 'hddp_options' ] );

          foreach( (array) $_POST[ '_options' ] as $option_name => $option_value ) {
            update_option( $option_name, $option_value );
          }

          die( wp_redirect( admin_url( 'index.php?page=hddp_manage&message=updated' ) ) );
        }

      }

      /**
       *
       */
      public function admin_menu() {
        // $ud_log = Flawless_F::get_log( array( 'limit' => 100 ) );
        // add_dashboard_page( __( 'Manage', HDDP ), __( 'Manage', HDDP ), $this->manage_options, 'manage', array( $this, 'hddp_manage' ) );
      }

      /**
       * Admin Scripts
       *
       * @author potanin@UD
       */
      public function admin_enqueue_scripts() {

        /* General Scripts and CSS styles */
        // wp_enqueue_script( 'hddp-backend-js' );
        // wp_enqueue_style( 'hddp-backend-css' );

        /* Specific scripts and styles should be loaded only on specific pages */
        if( get_current_screen()->id === 'dashboard_page_hddp_manage' ) {
          // wp_enqueue_script( 'jquery-cookie' );
          // wp_enqueue_script( 'ud-load' );
          // wp_enqueue_script( 'ud-socket' );
          // wp_enqueue_script( 'ud-json-editor' );
          // wp_enqueue_style( 'ud-json-editor' );
        }

      }

    }

  }

}