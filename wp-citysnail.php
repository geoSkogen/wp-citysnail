<?php
/*
Plugin Name:  wp-citysnail
Description:  Several New Acronyms Involving Language
Version:      2020.03.18
Author:       Joseph Scoggins
Author URI:   https://joseph-scoggins.com
Text Domain:  wp_citysnail
*/
defined( 'ABSPATH' ) or die( 'We make the path by walking.');

if ( !class_exists( 'Citysnail_Options' ) ) {
   include_once 'admin/class-citysnail-options.php';
   add_action(
    'admin_menu',
    array('Citysnail_Options','wp_citysnail_register_menu_page')
  );
}
if ( !class_exists( 'Citysnail_Settings' ) ) {
   include_once 'admin/class-citysnail-settings.php';
   add_action(
     'admin_init',
     array('Citysnail_Settings','wp_citysnail_settings_api_init')
   );
}
