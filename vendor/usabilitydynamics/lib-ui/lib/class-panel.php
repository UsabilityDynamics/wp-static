<?php
/**
 * Panel
 *
 * @author potanin@UD
 */
namespace UsabilityDynamics\UI {

  if( !class_exists( 'UsabilityDynamics\UI\Panel' ) ) {

    /**
     * Class Panel
     *
     */
    class Panel {

      public static $headers = array(
        'name' => 'Name',
        'description' => 'Description',
        'group' => 'Group',
        'version' => 'Version',
        'author' => 'Author'
      );

      private $_settings;

      /**
       * Create Panel.
       *
       * @param null  $id
       * @param array $settings
       */
      public function __construct( $id = null, $settings = array() ) {

        $this->_settings = array(
          '_id' => $id,
          'params' => null,
          'meta' => null
        );

        $this->_settings[ '_id' ]     = $this->_settings[ '_id' ] ? $this->_settings[ '_id' ] : $id;
        $this->_settings[ '_path' ]   = $this->resolve_path( isset( $settings[ 'paths' ] ) ? array_filter( (array) $settings[ 'paths' ] ) : array() );
        $this->_settings[ 'meta' ]    = $this->get_file_meta();
        $this->_settings[ 'params' ]  = isset( $settings[ 'params' ] ) ? $settings[ 'params' ] : array();

        $this->set(array(
          '_id' => $this->_settings[ '_id' ],
          '_path' => $this->_settings[ '_path' ]
        ));

      }

      /**
       * Get File Data from first found file.
       *
       * @param array $paths
       *
       * @return array
       */
      private function get_file_meta( $paths = array() ) {

        foreach( (array) ( $paths ? $paths : $this->_settings[ '_path' ] ) as $path ) {

          if( !is_file( $path ) ) {
            continue;
          }

          $_meta = get_file_data( $path, self::$headers );

          if( $_meta[ 'name' ] ) {
            return array_filter( $_meta );
          }

        }

        return array();

      }

      /**
       * Resolve / Verify Paths.
       *
       * @param array $paths
       *
       * @return array
       */
      private function resolve_path( $paths = array() ) {

        foreach( (array) $paths as $path ) {

          if( is_dir( $path ) ) {

            $absolute_path = wp_normalize_path( trailingslashit( $path ) . $this->_settings[ '_id' ] . '.php' );

            if( is_file( $absolute_path ) ) {
              return $absolute_path;
            }

          }

        }

      }

      /**
       * Prepare Parametrs for Template
       *
       * @param array $args
       *
       * @return object
       */
      private function set( $args = array() ) {

        return $this->_settings[ 'params' ] = (object) array_replace_recursive( (array) $this->_settings[ 'params' ], (array) $args );

      }

      /**
       * Parameter Lookup
       *
       * @param null $key
       *
       * @return mixed
       */
      private function get( $key = null ) {
        return $key ? ( isset( $this->_settings[ 'params' ]->{$key} ) ? $this->_settings[ 'params' ]->{$key} : null ) : $this->_settings[ 'params' ];
      }

      /**
       * Return extractable array.
       *
       * @return array
       */
      private function get_extract() {
        return (array) $this->_settings[ 'params' ];
      }

      /**
       * JSON Output
       *
       * @param null $extra
       */
      public function json( $extra = null ) {
        $this->set( $extra );

        echo '<pre>';
        echo json_encode( $this->get() );
        echo '</pre>';
      }

      /**
       * Standard HTML5 Render
       * @param null $extra
       */
      public function section( $extra = null ) {
        $this->set( $extra );

        extract( $this->get_extract(), EXTR_SKIP );

        echo '<section data-section-id="' . $this->get( '_id' ) . '">';

        if( isset( $this->_settings[ '_path' ] ) && is_file( $this->_settings[ '_path' ] ) ) {
          include( $this->_settings[ '_path' ] );
        }

        echo '</section>';

      }

      /**
       * Aside Render
       * @param null $extra
       */
      public function aside( $extra = null ) {
        $this->set( $extra );

        extract( $this->get_extract(), EXTR_SKIP );

        echo '<aside data-aside-id="' . $this->get( '_id' ) . '">';

        if( isset( $this->_settings[ '_path' ] ) && is_file( $this->_settings[ '_path' ] ) ) {
          include( $this->_settings[ '_path' ] );
        }

        echo '</aside>';

      }

      /**
       * Widget Render
       * @param null $extra
       */
      public function widget( $extra = null ) {
        $this->set( $extra );

        extract( $this->get_extract(), EXTR_SKIP );

        echo '<aside data-widget-id="' . $this->get( '_id' ) . '">';

        if( isset( $this->_settings[ '_path' ] ) && is_file( $this->_settings[ '_path' ] ) ) {
          include( $this->_settings[ '_path' ] );
        }

        echo '</aside>';

      }

      /**
       * Frontend Template.
       *
       * @param null $args
       */
      public function template( $args = null ) {
        $this->set( $extra );

        extract( $this->get_extract(), EXTR_SKIP );

        if( isset( $this->_settings[ '_path' ] ) && is_file( $this->_settings[ '_path' ] ) ) {
          include( $this->_settings[ '_path' ] );
        }

      }

      /**
       * Metabox Render
       *
       * @example
       *
       *    add_shortcode( 'footag', array( $_view, 'shortcode' ) );
       *
       * @param array|null $atts
       * @param string     $content
       * @param null       $name
       *
       * @return string
       * @internal param \UsabilityDynamics\UI\atts $null
       */
      public function shortcode( $atts = array(), $content = '', $name = null ) {

        // Update settings by parsing model data against defined data.
        $this->set( shortcode_atts( $this->get(), $atts ) );

        // Extract result settings.
        extract( $this->get_extract(), EXTR_SKIP );

        if( isset( $this->_settings[ '_path' ] ) && is_file( $this->_settings[ '_path' ] ) ) {
          ob_start();
          include( $this->_settings[ '_path' ] );
          return ob_get_clean();
        }

        return '';

      }

    }

  }

}