<?php
/**
 * 
 * @class Post_AMD_WP_UnitTestCase
 */
class Post_AMD_WP_UnitTestCase extends AMD_WP_UnitTestCase {

  /**
   * @type object
   */
  protected $asset;
  
  /**
   * Constructor
   */
  function setUp() {
	  parent::setUp();
    $this->setAMDPostObject();
  }
  
  /**
   * Destructor
   */
  function tearDown() {
	  parent::tearDown();
    $this->asset = NULL;
  }
  
  /**
   * Sets self::$asset variable.
   *
   * Rewrite the method in child class!
   */
  function setAMDPostObject() {
    $this->fail( 'setAMDPostObject must be rewritten' );
  }
  
  /**
   * Return sample data
   */
  function getSampleData( $path ) {
    $content = '';
    if( file_exists( $path ) ) {
      $content = @file_get_contents( $path );
    }
    if( empty( $content ) ) {
      $this->fail( 'Fixture data for testing is not available.' );
    }
    return $content;
  }
  
  /**
   * Rewrite the method in child class!
   */
  function checkRegisteredAsset() {}
  
  /**
   * Checks specific actions.
   *
   * Rewrite the method in child class!
   */
  function proceedSpecificActions() {}
  
  /**
   * 
   * Rewrite the method in child class!
   */
  function checkDependencies() {}
  
  /**
   *
   * @group asset
   */
  function testPobj() {
    $this->assertTrue( is_object( $this->asset ) );
    // Test get settings
    $this->assertNotEmpty( $this->asset->get( 'post_type' ) );
  }
  
  /**
   * Test Registering of Post
   *
   * @group asset
   */
  function testRegisterPostType() {
    $this->assertNull( get_post_type_object( $this->asset->get( 'post_type' ) ) );
    $this->asset->register_post_type();
    
    $pobj = get_post_type_object( $this->asset->get( 'post_type' ) );
    $this->assertInstanceOf( 'stdClass', $pobj );
    $this->assertEquals( $this->asset->get( 'post_type' ), $pobj->name );

    // Test if post supports revisions
    $this->assertTrue( post_type_supports( $this->asset->get( 'post_type' ), 'revisions' ) );

    _unregister_post_type( $this->asset->get( 'post_type' ) );
  }
  
  /**
   * Test Actions
   *
   * @group asset
   */
  function testActions() {
    $this->assertGreaterThan( 0, has_filter( 'init', array( $this->asset, 'register_post_type' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'admin_init', array( $this->asset, 'admin_init' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'redirect_canonical', array( $this->asset, 'redirect_canonical' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'pre_update_option_rewrite_rules', array( $this->asset, 'update_option_rewrite_rules' ) ) );
    // @todo: It's being tested twice. Probably, need to moved it out.
    $this->assertGreaterThan( 0, has_filter( 'query_vars', array( 'UsabilityDynamics\AMD\Scaffold', 'query_vars' ) ) );
    $this->assertGreaterThan( 0, has_filter( 'template_include', array( 'UsabilityDynamics\AMD\Scaffold', 'return_asset' ) ) );
    
    //** Determine if Admin Menu is enabled */
    if( $this->asset->get( 'admin_menu' ) ) {
      $this->assertGreaterThan( 0, has_filter( 'admin_menu', array( $this->asset, 'add_admin_menu' ) ) );
      // @todo: It's being tested twice. Probably, need to moved it out.
      $this->assertGreaterThan( 0, has_filter( 'get_edit_post_link', array( 'UsabilityDynamics\AMD\Scaffold', 'revision_post_link' ) ) );
    }
    
    //** Checks specific actions */
    $this->proceedSpecificActions();
  }
  
  /**
   * Tests:
   * - save asset
   * - get asset
   * - revision ID
   *
   * @group asset
   */
  function testAsset() {
    
    //** Get First Revision and save asset */
    $data = $this->getSampleData( $this->root_dir . '/test/php/fixtures/sample-1rev.' . $this->asset->get( 'extension' ) );
    $this->assertGreaterThan( 0, $this->asset->save_asset( $data ) );
    
    //** Get Second Revision and save asset */
    $data2 = $this->getSampleData( $this->root_dir . '/test/php/fixtures/sample-2rev.' . $this->asset->get( 'extension' ) );
    $this->assertGreaterThan( 0, $this->asset->save_asset( $data2 ) );
    
    //** Get Asset */
    $post = $this->asset->get_asset( $this->asset->get( 'type' ) );
    $this->assertTrue( is_array( $post ) );
    $post = (array)$post;
    $this->assertArrayHasKey( 'ID', $post );
    $this->assertArrayHasKey( 'post_content', $post );
    
    //** Check content */
    $this->assertEquals( $data2, ( !empty( $post[ 'post_content' ] ) ? $post[ 'post_content' ] : false ) );
    
    //** Check latest version ID */
    $this->assertEquals( 1, $this->asset->get_latest_version_id( $post[ 'ID' ] ) );
    
    //** Save 'dependencies' */
    $this->assertTrue( is_array( $this->asset->get( 'dependencies' ) ) );
    add_post_meta( $post[ 'ID' ], 'dependency', array_keys( (array)$this->asset->get( 'dependencies' ) ) ) or 
      update_post_meta( $post[ 'ID' ], 'dependency', array_keys( (array)$this->asset->get( 'dependencies' ) ) );
    
    //** Check Dependencies */
    $this->checkDependencies();
    
  }
  
}
