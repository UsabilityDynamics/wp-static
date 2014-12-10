<?php
/**
 * Main UI Class.
 *
 * @author potanin@UD
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\UI' ) ) {

    /**
     * Class UI
     *
     */
    class UI {

      /**
       * Meta Box Accordion Template Function
       *
       * Largely made up of abstracted code from {@link do_meta_boxes()}, this
       * function serves to build meta boxes as list items for display as
       * a collapsible accordion.
       *
       * @since 0.2.1
       *
       * @uses global $wp_meta_boxes Used to retrieve registered meta boxes.
       *
       * @internal param object|string $screen The screen identifier.
       * @internal param string $context The meta box context.
       * @internal param mixed $object gets passed to the section callback function as first parameter.
       *
       * @param $screen
       * @param $context
       * @param $object
       *
       * @return int number of meta boxes as accordion sections.
       */
      static public function do_sections( $screen = null, $context = 'main', $object = array() ) {
        global $wp_meta_boxes;

        wp_enqueue_script( 'accordion' );

        if ( empty( $screen ) )
          $screen = get_current_screen();
        elseif ( is_string( $screen ) )
          $screen = convert_to_screen( $screen );

        $page = $screen->id;

        $hidden = get_hidden_meta_boxes( $screen );

        ?>
        <div class="accordion-container">
          <ul class="outer-border">

          <?php
          $i = 0;
          $first_open = false;
          do {
            if ( ! isset( $wp_meta_boxes ) || ! isset( $wp_meta_boxes[$page] ) || ! isset( $wp_meta_boxes[$page][$context] ) )
              break;

            foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
              if ( isset( $wp_meta_boxes[$page][$context][$priority] ) ) {
                foreach ( $wp_meta_boxes[$page][$context][$priority] as $box ) {
                  if ( false == $box || ! $box['title'] )
                    continue;
                  $i++;
                  $hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';

                  $open_class = '';
                  if ( ! $first_open && empty( $hidden_class ) ) {
                    $first_open = true;
                    $open_class = 'open';
                  }
                  ?>
                  <li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
                    <h3 class="accordion-section-title hidden" tabindex="0" title="<?php echo esc_attr( $box['title'] ); ?>"><?php echo esc_html( $box['title'] ); ?></h3>
                    <div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
                      <div class="inside">
                        <?php call_user_func( $box['callback'], $object, $box ); ?>
                      </div>
                    </div>
                  </li>
                <?php
                }
              }
            }
          } while(0);
          ?>
          </ul>
        </div>
        <?php

        return $i;

      }

      static public function do_tabs( $screen = null, $context = 'main', $object = array() ) {
        global $wp_meta_boxes;

        wp_enqueue_script( 'accordion' );

        if ( empty( $screen ) )
          $screen = get_current_screen();
        elseif ( is_string( $screen ) )
          $screen = convert_to_screen( $screen );

        $page = $screen->id;

        $hidden = get_hidden_meta_boxes( $screen );

        ?>

          <?php
          $i = 0;
          $first_open = false;
          do {
            if ( ! isset( $wp_meta_boxes ) || ! isset( $wp_meta_boxes[$page] ) || ! isset( $wp_meta_boxes[$page][$context] ) )
              break;

            foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
              if ( isset( $wp_meta_boxes[$page][$context][$priority] ) ) {
                foreach ( $wp_meta_boxes[$page][$context][$priority] as $box ) {
                  if ( false == $box || ! $box['title'] )
                    continue;
                  $i++;
                  $hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';

                  $open_class = '';
                  if ( ! $first_open && empty( $hidden_class ) ) {
                    $first_open = true;
                    $open_class = 'open';
                  }
                  ?>
                  <a class="nav-tab accordion-section-title control-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" data-box-id="<?php echo esc_attr( $box['id'] ); ?>"><?php echo esc_html( $box['title'] ); ?></a>
                <?php
                }
              }
            }
          } while(0);
          ?>
        <?php

        return $i;

      }

      /**
       * Meta Box Accordion Template Function
       *
       * Largely made up of abstracted code from {@link do_meta_boxes()}, this
       * function serves to build meta boxes as list items for display as
       * a collapsible accordion.
       *
       * @since 0.2.1
       *
       * @uses global $wp_meta_boxes Used to retrieve registered meta boxes.
       *
       * @internal param object|string $screen The screen identifier.
       * @internal param string $context The meta box context.
       * @internal param mixed $object gets passed to the section callback function as first parameter.
       *
       * @param $screen
       * @param $context
       * @param $object
       *
       * @return int number of meta boxes as accordion sections.
       */
      static public function do_accordion_sections( $screen = null, $context = 'main', $object = array() ) {
        global $wp_meta_boxes;

        wp_enqueue_script( 'accordion' );

        if ( empty( $screen ) )
          $screen = get_current_screen();
        elseif ( is_string( $screen ) )
          $screen = convert_to_screen( $screen );

        $page = $screen->id;

        $hidden = get_hidden_meta_boxes( $screen );

        ?>
        <div class="accordion-container">
          <ul class="outer-border">

          <?php
          $i = 0;
          $first_open = false;
          do {
            if ( ! isset( $wp_meta_boxes ) || ! isset( $wp_meta_boxes[$page] ) || ! isset( $wp_meta_boxes[$page][$context] ) )
              break;

            foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
              if ( isset( $wp_meta_boxes[$page][$context][$priority] ) ) {
                foreach ( $wp_meta_boxes[$page][$context][$priority] as $box ) {
                  if ( false == $box || ! $box['title'] )
                    continue;
                  $i++;
                  $hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';

                  $open_class = '';
                  if ( ! $first_open && empty( $hidden_class ) ) {
                    $first_open = true;
                    $open_class = 'open';
                  }
                  ?>
                  <li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
                    <h3 class="accordion-section-title hndle" tabindex="0" title="<?php echo esc_attr( $box['title'] ); ?>"><?php echo esc_html( $box['title'] ); ?></h3>
                    <div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
                      <div class="inside">
                        <?php call_user_func( $box['callback'], $object, $box ); ?>
                      </div>
                    </div>
                  </li>
                <?php
                }
              }
            }
          } while(0);
        ?>
          </ul>
        </div>
        <?php

        return $i;

      }

      /**
       * Prints out all settings sections added to a particular settings page
       *
       * Part of the Settings API. Use this in a settings page callback function
       * to output all the sections and fields that were added to that $page with
       * add_settings_section() and add_settings_field()
       *
       * @since 0.2.1
       *
       * @global $wp_settings_sections Storage array of all settings sections added to admin pages
       * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
       *
       * @param string $page The slug name of the page whos settings sections you want to output
       */
      static public function do_settings_sections( $page ) {
        global $wp_settings_sections, $wp_settings_fields;

        if ( ! isset( $wp_settings_sections[$page] ) )
          return;

        foreach ( (array) $wp_settings_sections[$page] as $section ) {
          if ( $section['title'] )
            echo "<h3>{$section['title']}</h3>\n";

          if ( $section['callback'] )
            call_user_func( $section['callback'], $section );

          if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
            continue;
          echo '<table class="form-table">';
          do_settings_fields( $page, $section['id'] );
          echo '</table>';
        }
      }

      static public function add_object_type_option() {}

      static public function add_plugin_page_tab() {}

      static public function include_page( $args = array() ) {

      }

    }

  }

}