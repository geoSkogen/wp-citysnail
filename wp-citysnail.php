<?php
/*
Plugin Name:  wp-citysnail
Description:  SEO cargo, citystyle
Version:      2020.05.02
Author:       Joseph Scoggins
Author URI:   https://github.com/geoSkogen
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

if ( !class_exists( 'Snail_Query' ) ) {
  include_once 'util/snail_query.php';
}

if ( !class_exists( 'Snail_File' ) ) {
  include_once 'util/snail_file.php';
}

/*
add_action(
  'wp_ajax_nopriv_citysnail_submit_structure',
   array('Snail_File','upload_handler')
 );
add_action( '
  wp_ajax_citysnail_submit_structure',
  array('Snail_File','upload_handler')
);
*/
