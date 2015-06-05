<?php
/**
 * Plugin Name: WP-Static
 * Text Domain: wp-static
 * Plugin URI: http://UsabilityDynamics.com/plugins/wp-static/
 * Description: Static HTML manager
 * Author: Usability Dynamics, Inc
 * Version: 1.0.1
 * Author URI: http://UsabilityDynamics.com
 * Domain Path: /static/languages/
 * GitHub Plugin URI: UsabilityDynamics/wp-static
 *
 * Copyright 2011-2014  Usability Dynamics, Inc.   (email : info@UsabilityDynamics.com)
 *
 * Created by Usability Dynamics, Inc
 * (website: UsabilityDynamics.com       email : info@UsabilityDynamics.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Andy Potanin <andy.potanin@usabilitydynamics.com>
 */

if( !function_exists( 'get_wp_static' ) ) {

  if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once ( __DIR__ . '/vendor/autoload.php' );
  }

  define( 'WP_STATIC_DIR', plugin_dir_path( __FILE__ ) );
  define( 'WP_STATIC_URL', plugin_dir_url( __FILE__ ) );

  /**
   * Returns WP_AMD object
   *
   * @author peshkov@UD
   */
  function get_wp_static( $key = false, $default = null ) {
    if( class_exists( '\UsabilityDynamics\WPStatic\Bootstrap' ) ) {
      $instance = \UsabilityDynamics\WPStatic\Bootstrap::get_instance();
      return $key ? $instance->get( $key, $default ) : $instance;
    }
    return false;
  }

}

// Initialize.
get_wp_static();
