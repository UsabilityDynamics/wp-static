<?php
/**
 * 
 * @class StyleTest
 */
class StyleTest extends Post_AMD_WP_UnitTestCase {

  var $old_wp_scripts;

  /**
   * Set fixture
   */
  function setAMDPostObject() {
    if( !is_object( $this->instance->style ) || get_class( $this->instance->style ) != 'UsabilityDynamics\AMD\Style' ) {
      $this->fail( 'Style object is not available.' );
    }
    $this->asset = $this->instance->style;
  }
  
  /**
   *
   */
  function proceedSpecificActions() {
    $this->assertGreaterThan( 0, has_filter( 'wp_print_styles', array( $this->asset, 'register_asset' ) ) );
  }
  
  /**
   * Checks loading of script and all dependencies
   */
  function checkDependencies() {
    $this->old_wp_styles = isset( $GLOBALS[ 'wp_styles' ] ) ? $GLOBALS[ 'wp_styles' ] : null;
    remove_action( 'wp_default_styles', 'wp_default_styles' );
    $GLOBALS['wp_styles'] = new WP_Styles();
    $GLOBALS['wp_styles']->default_version = get_bloginfo( 'version' );
    
    $this->asset->register_asset();
    
    $result = get_echo( 'wp_print_styles' );
    
    //** Be sure all dependencies are loaded */
    $dependencies = (array)$this->asset->get( 'dependencies' );
    foreach( $dependencies as $dependency ) {
      if( !empty( $dependency[ 'url' ] ) ) {
        $this->assertContains( esc_url( $dependency[ 'url' ] ), $result );
      }
    }
    
    //** Be sure our style is loaded */
    $this->assertContains( esc_url( $this->asset->get_asset_url() ), $result );
    
    $GLOBALS['wp_styles'] = $this->old_wp_styles;
    add_action( 'wp_default_styles', 'wp_default_styles' );
  }
  
}
