<?php
/*
Plugin Name:  wp-citysnail
Description:  SEO cargo, citystyle
Version:      2020.03.28
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

if ( !class_exists( 'Schema' ) ) {
  include_once 'classes/schema.php';
}

if ( !class_exists( 'Sitemap_Monster' ) ) {
  include_once 'classes/sitemap_monster.php';
}

if ( !class_exists( 'Snail' ) ) {
  include_once 'classes/snail.php';
}

if ( !class_exists( 'Sitemap_Snail' ) ) {
  include_once 'classes/sitemap_snail.php';
}

if ( !class_exists( 'Snail_Tail' ) ) {
  include_once 'util/snail_tail.php';
}
