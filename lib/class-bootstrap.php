<?php
/**
 * WP-Static Plugin.
 *
 */
namespace UsabilityDynamics\WPStatic {

  use UsabilityDynamics\Settings;

  if( !class_exists( 'UsabilityDynamics\WPStatic\Bootstrap' ) ) {

    class Bootstrap {

      /**
       * Cluster core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public $version = false;

      /**
       * Textdomain String
       *
       * @public
       * @property domain
       * @var string
       */
      public $domain = false;

      /**
       * Singleton Instance Reference.
       *
       * @public
       * @static
       * @property $instance
       * @type {Object}
       */
      public static $instance = null;

      public $option = array();

      /**
       * Instantaite class.
       */
      private function __construct() {
        
        $plugin_data = get_file_data( ( dirname( __DIR__ ) . '/wp-static.php' ), array(
          'Name' => 'Plugin Name',
          'Version' => 'Version',
          'TextDomain' => 'Text Domain',
        ), 'plugin' );
        
        $this->version  = trim( $plugin_data[ 'Version' ] );
        $this->domain   = trim( $plugin_data[ 'TextDomain' ] );

        // Initialize Settings.
        $this->settings = new Settings( array(
          'key'  => 'wp_static',
          'data' => array(
            'version' => $this->version,
            'domain' => $this->domain,
          )
        ));

        // Set Dynamics.
        $this->set( 'version',  $this->version );
        $this->set( 'locale',   $this->domain );
        
        $this->app = new App( $this->settings, $this );
        
        add_action( 'wp_ajax_static_save', array( $this, 'ajax_handler' ) );

      }
      
      /**
       * Handle Administrative AJAX Actions
       *
       * @author potanin@UD
       * @method ajax_handler
       */
      public function ajax_handler() {

        $_data = $_POST[ 'data' ];
        parse_str( $_POST['data']['form'], $_form );

        if( !method_exists( $this->app, 'save_html' ) ) {

          return wp_send_json(array(
            'ok' => false,
            'message' => __( 'Unexpected error occured.', $this->get( 'locale' ) )
          ));

        }

        if( !is_wp_error( $_revision = $this->app->save_html( $_data[ 'data' ] ) ) ) {
          
          update_option( 'static-html-activate', !empty( $_form['static-html-activate'] ) ? 'true' : 'false' );

          return wp_send_json(array(
            'ok' => false,
            'revision' => $_revision,
            'message' => __( 'Asset saved successfully.', $this->get( 'locale' ) )
          ));

        }

        return wp_send_json(array(
          'ok' => false,
          'message' => __( 'Unable to save asset.', $this->get( 'locale' ) )
        ));

      }

      /**
       * Determine if instance already exists and Return Theme Instance
       *
       */
      public static function get_instance( $args = array() ) {
        return null === self::$instance ? self::$instance = new self() : self::$instance;
      }

      /**
       * @param null $key
       * @param null $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        return $this->settings->set( $key, $value );
      }

      /**
       * @param null $key
       * @param null $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        return $this->settings->get( $key, $default );
      }

    }

  }

}