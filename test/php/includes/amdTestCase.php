<?php
/**
 * 
 * @class AMD_WP_UnitTestCase
 */
class AMD_WP_UnitTestCase extends WP_UnitTestCase {

  protected $root_dir;
  
  protected $instance;

  /**
   * WP Test Framework Constructor
   */
  function setUp() {
	  parent::setUp();
    $this->root_dir = dirname( dirname( dirname( __DIR__ ) ) );
    if( file_exists( $this->root_dir . '/wp-amd.php' ) ) {
      include_once( $this->root_dir . '/wp-amd.php' );
    }
    if( !class_exists( '\UsabilityDynamics\AMD\Bootstrap' ) ) {
      $this->fail( 'Plugin is not available.' );
    }
    $this->instance = \UsabilityDynamics\AMD\Bootstrap::get_instance();
  }
  
  /**
   * WP Test Framework Destructor
   */
  function tearDown() {
	  parent::tearDown();
    $this->root_dir = NULL;
    $this->instance = NULL;
  }
  
}
