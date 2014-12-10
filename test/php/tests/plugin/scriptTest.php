<?php
/**
 * 
 * @class ScriptTest
 */
class ScriptTest extends Post_AMD_WP_UnitTestCase {

  var $old_wp_scripts;

  /**
   * Set fixture
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->script ) || get_class( $this->instance->script ) != 'UsabilityDynamics\AMD\Script' ) {
      $this->fail( 'Script object is not available.' );
    }
    $this->asset = $this->instance->script;
  }
  
  /**
   *
   */
  function proceedSpecificActions() {
    $this->assertGreaterThan( 0, has_filter( 'wp_enqueue_scripts', array( $this->asset, 'register_asset' ) ) );
  }
  
  /**
   * Checks loading of script and all dependencies
   */
  function checkDependencies() {
    $this->old_wp_scripts = isset( $GLOBALS['wp_scripts'] ) ? $GLOBALS['wp_scripts'] : null;
    remove_action( 'wp_default_scripts', 'wp_default_scripts' );
    $GLOBALS['wp_scripts'] = new WP_Scripts();
    $GLOBALS['wp_scripts']->default_version = get_bloginfo( 'version' );
    
    $this->asset->register_asset();
    
    $result = get_echo( 'wp_print_scripts' );
    
    //** Be sure all dependencies are loaded */
    $dependencies = (array)$this->asset->get( 'dependencies' );
    foreach( $dependencies as $dependency ) {
      if( !empty( $dependency[ 'url' ] ) ) {
        $this->assertContains( esc_url( $dependency[ 'url' ] ), $result );
      }
    }
    
    //** Be sure our script is loaded */
    $this->assertContains( esc_url( $this->asset->get_asset_url() ), $result );
    
    $GLOBALS['wp_scripts'] = $this->old_wp_scripts;
    add_action( 'wp_default_scripts', 'wp_default_scripts' );
  }
  
}
