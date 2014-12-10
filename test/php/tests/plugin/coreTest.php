<?php
/**
 * Be sure plugin's core is loaded.
 *
 * @class BasicTest
 */
class CoreTest extends AMD_WP_UnitTestCase {

  /**
   * 
   * @group core
   */
  function testGetWpAmd() {
    $this->assertTrue( function_exists( 'get_wp_amd' ) );
    $data = get_wp_amd();
    $this->assertTrue( is_object( $data ) && get_class( $data ) == 'UsabilityDynamics\AMD\Bootstrap' );
  }
  
  /**
   *
   * @group core
   */
  function testInstance() {
    $this->assertTrue( is_object( $this->instance ) );
  }
  
  /**
   *
   * @group core
   */
  function testClasses() {
    $this->assertTrue( class_exists( 'UsabilityDynamics\AMD\Scaffold' ) );
    $this->assertTrue( class_exists( 'UsabilityDynamics\AMD\Script' ) );
    $this->assertTrue( class_exists( 'UsabilityDynamics\AMD\Style' ) );
  }
  
  /**
   *
   * @group core
   */
  function testGetSettings() {
    $this->assertGreaterThan( 0, $this->instance->get( 'version' ) );
    $this->assertEquals( 'script', $this->instance->get( 'assets.script.type' ) );
    $this->assertEquals( 'style', $this->instance->get( 'assets.style.type' ) );
  }
  
  /**
   *
   * @group core
   */
  function testSetSettings() {
    $this->instance->set( 'our.test.data', 'test' );
    $this->assertEquals( 'test', $this->instance->get( 'our.test.data' ) );
  }
  
}
