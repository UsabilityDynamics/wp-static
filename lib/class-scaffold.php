<?php
/**
 * 
 * 
 *
 * @author usabilitydynamics@UD
 * @version 1.0.0
 * @module UsabilityDynamics\WPStatic
 */
namespace UsabilityDynamics\WPStatic {

  if( !class_exists( '\UsabilityDynamics\WPStatic\Scaffold' ) ) {

    abstract class Scaffold {
      
      /**
       * Constructor
       * Must be called in child constructor firstly!
       *
       */
      public function __construct( $args = array(), $context = null ) {
      
        $this->args = wp_parse_args( $args, array(
          'version' => null,
          'type' => '', // style, script
          'minify' => false,
          'load_in_head' => true,
          'permalink' => '', // (assets/wp-amd.js|assets/wp-amd.css)
          'dependencies' => array(),
          'admin_menu' => true,
          'post_type' => false,
        ));
        
        // If context is provided (e.g. instance of wp-amd bootstrap), get settings from it.
        if( is_object( $context ) && method_exists( $context, 'get' ) ) {
          $this->args[ 'version' ] = $context->get( 'version' );
        }
        
        add_action( 'init', array( &$this, 'register_post_type' ) );
        
        //** Determine if Admin Menu is enabled */
        if( $this->get( 'admin_menu' ) ) {
          
          add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );

          // Override the edit link, the default link causes a redirect loop
          add_filter( 'get_edit_post_link', array( __CLASS__, 'revision_post_link' ) );

        }
        
      }

      /**
       * Register Post Types
       *
       */
      public function register_post_type() {

        $labels = array(
          'name'               => _x( 'Static', 'post type general name', get_wp_static( 'domain' ) ),
          'singular_name'      => _x( 'Static', 'post type singular name', get_wp_static( 'domain' ) ),
          'menu_name'          => _x( 'Static', 'admin menu', get_wp_static( 'domain' ) ),
          'name_admin_bar'     => _x( 'Static', 'add new on admin bar', get_wp_static( 'domain' ) ),
          'add_new'            => _x( 'Add New', 'book', get_wp_static( 'domain' ) ),
          'add_new_item'       => __( 'Add New Static', get_wp_static( 'domain' ) ),
          'new_item'           => __( 'New Static', get_wp_static( 'domain' ) ),
          'edit_item'          => __( 'Edit Static', get_wp_static( 'domain' ) ),
          'view_item'          => __( 'View Static', get_wp_static( 'domain' ) ),
          'all_items'          => __( 'All Static', get_wp_static( 'domain' ) ),
          'search_items'       => __( 'Search Static', get_wp_static( 'domain' ) ),
          'parent_item_colon'  => __( 'Parent Static:', get_wp_static( 'domain' ) ),
          'not_found'          => __( 'No books found.', get_wp_static( 'domain' ) ),
          'not_found_in_trash' => __( 'No books found in Trash.', get_wp_static( 'domain' ) ),
        );

        register_post_type( 'static_html', array(
          'labels'              => $labels,
          'can_export'          => true,
          'public'              => false,
          'publicly_queryable'  => false,
          'show_ui'             => false,
          'show_in_menu'        => false,
          'capability_type'     => 'post',
          'supports' =>         array( 'revisions' )
        ));

      }

      /**
       * Add Administrative Menus
       *
       * @return array
       */
      public function add_admin_menu() {

        $id = add_theme_page( __('Static', get_wp_static( 'domain' )), __('Static', get_wp_static( 'domain' )), 'edit_theme_options', 'theme-page-static', array( $this, 'admin_edit_page' ) );

        add_action( 'admin_print_scripts-' . $id, array( $this, 'admin_scripts' ) );

        add_meta_box( 'static-manage', __( 'Manage', get_wp_static( 'domain' ) ), array( $this, 'render_metabox_publish' ),      $id, 'side', 'core' );

        add_meta_box( 'static-revisions', __( 'Revisions', get_wp_static( 'domain' ) ), array( $this, 'render_metabox_revisions' ),    $id, 'side', 'core' );

      }

      /**
       * Actions box
       */
      static public function render_metabox_publish() {
        ?>
        <ul>
          <li class="form-group">
            <input class="button-primary" type="submit" name="publish" value="<?php _e( 'Save', get_wp_static( 'domain' ) ); ?>"/>
            <a target="_blank" href="<?php echo get_home_url( get_current_blog_id(), '?static-html-preview='.rand(0, 999999) ) ?>" ><?php _e( 'Preview', get_wp_static( 'domain' ) ); ?></a>
          </li>
          <li class="form-group">
            <label>
              <input value="true" type="checkbox" name="static-html-activate" <?php checked( get_option( 'static-html-activate' ), 'true' ); ?> />
              <?php _e( 'Show for visitors' ); ?>
            </label>
          </li>
        </ul>
        <?php
      }
      
      /**
       * register_admin_styles function.
       * adds styles to the admin page
       *
       * Add action to denqueue or deregister scripts which cause conflicts and errors.
       *
       * @access public
       * @return void
       */
      public function admin_scripts() {
        do_action( 'static::admin_scripts::edit_page' );
        
        wp_register_script( 'wp-ace', plugins_url( '/static/scripts/src/ace/ace.js', dirname( __FILE__ ) ), array(), $this->get( 'version' ), true );
        wp_enqueue_style( 'wp-static-styles', plugins_url( '/static/styles/wp-static.css', dirname( __FILE__ ) ) );
        wp_enqueue_script( 'wp-static-scripts', plugins_url( '/static/scripts/wp-static.js',  dirname( __FILE__ ) ), array( 'wp-ace', 'jquery-ui-resizable' ), $this->get( 'version' ), true );

      }
      
      /**
       * Editor page render
       */
      public function admin_edit_page() {
        $msg = 0;

        // the form has been submited save the options
        if( !empty( $_POST ) && check_admin_referer( 'update_static_html', 'update_static_html_nonce' ) ) {

          $data = stripslashes( $_POST [ 'content' ] );
          $post_id = $this->save_asset( $data );
          $updated = true;
          $msg = 1;

          // Redirect back to self.
          die( wp_redirect( admin_url( 'themes.php?page=theme-page-static&updated=true' ) . '&message=' . $msg ) );

        }

        if( isset( $_GET[ 'message' ] ) ) {
          $msg = (int) $_GET[ 'message' ];
        }

        $messages = array(
          0 => false,
          1 => sprintf( __( 'Global %s saved. <a href="%s" target="_blank">View in browser</a>.', get_wp_static( 'domain' ) ), $this->get( 'type' ), home_url( apply_filters( 'wp-amd:' . $this->get( 'type' ) . ':path', 'assets/wp-amd.' .  $this->get( 'extension' ), $this ) ) ),
          5 => isset( $_GET[ 'revision' ] ) ? sprintf( __( '%s restored to revision from %s, <em>Save changes for the revision to take effect</em>', get_wp_static( 'domain' ) ), ucfirst( $this->get( 'type' ) ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false
        );
        
        $data = self::get_asset( 'static_html' );
        $data = $data ? $data : array();
        $data[ 'msg' ] = $messages[ $msg ];
        $data[ 'post_content' ] = isset( $data[ 'post_content' ] ) ? $data[ 'post_content' ] : '';
        
        $template = WP_STATIC_DIR . 'static/templates/html-edit-page.php';
        
        if( file_exists( $template ) ) {
          include( $template );
        }
      }

      /**
       * Saves/updates asset.
       *
       *
       * @todo After POST save, do wp_redirect() back to self to avoid re-posting data accidentally on page reload. - potanin@UD
       *
       * @access public
       *
       * @param $value
       *
       * @internal param mixed $js
       * @return void
       */
      public function save_html( $value = null ) {

        if( !$post = self::get_asset( 'static_html' ) ) {

          $data = array(
            'post_title' => ( 'Static HTML' ),
            'post_content' => $value,
            'post_status' => 'publish',
            'post_type' => 'static_html',
          );

          $post_id = wp_insert_post( $data );

          // @todo Handle is_wp_error;
          if( $post_id && !is_wp_error( $post_id ) ) {
            add_post_meta( $post_id, 'theme_relation', sanitize_key( wp_get_theme()->get( 'Name' ) ), true );
          }

        } else {
          $post[ 'post_content' ] = $value;
          $post_id = wp_update_post( $post );
        }

        return $post_id;

      }
      
      /**
       * Returns asset by type ( script, style )
       *
       * @access public
       *
       * @param $type
       *
       * @return void
       */
      public static function get_asset( $type ) {

        $posts = get_posts( array(
          'numberposts' => 1, 
          'post_type' => $type, 
          'post_status' => 'publish',
          'meta_key' => 'theme_relation',
          'meta_value' => sanitize_key( wp_get_theme()->get( 'Name' ) ),
        ));

        $post = is_array( $posts ) ? array_shift( $posts ) : false;

        if( !$post ) {
          return false;
        }

        return $post ? get_object_vars( $post ) : false;

      }
      
      /**
       * revision_post_link function.
       * Override the edit link, the default link causes a redirect loop
       *
       * @access public
       * @param mixed $post_link
       * @return void
       */
      public static function revision_post_link( $post_link ) {
        global $post;

        if( !isset( $post ) || !isset( $post->post_type ) ) {
          return $post_link;
        }

        if( $post->post_type != 'static_html' ) {
          return $post_link;
        }

        if( isset( $post ) && strstr( $post_link, 'action=edit' ) && !strstr( $post_link, 'revision=' ) ) {
          $post_link = 'themes.php?page=theme-page-static';
        }

        return $post_link;

      }

      /**
       * @param $post
       *
       * @internal param $_post
       */
      public function render_metabox_revisions( $post ) {

        // Specify numberposts and ordering args
        $args = array(
          'numberposts' => 5,
          'orderby' => 'ID',
          'order' => 'DESC'
        );

        // Remove numberposts from args if show_all_rev is specified
        if( isset( $_GET[ 'show_all_rev' ] ) ) {
          unset( $args[ 'numberposts' ] );
        }

        if( isset( $post[ 'ID' ] ) ) {
          wp_list_post_revisions( $post[ 'ID' ], $args );
        }

      }
      
      /**
       * Returns required argument
       */
      public function get( $arg = null ) {

        if( !$arg ) {
          return $this->args;
        }

        return isset( $this->args[ $arg ] ) ? $this->args[ $arg ] : NULL;
      }
      
    }

  }

}


      