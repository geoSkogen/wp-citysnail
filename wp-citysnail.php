<?php
/*
Plugin Name:  wp-citysnail
Description:  SEO cargo, citystyle
Version:      2020.04.04
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

if ( !class_exists( 'Snail_Query' ) ) {
  include_once 'util/snail_query.php';
}

if ( !class_exists( 'Snail_File' ) ) {
  include_once 'util/snail_file.php';
}

/*
add_action( 'wp_ajax_nopriv_citysnail_submit_structure', 'csv_upload_handler' );
add_action( 'wp_ajax_citysnail_submit_structure', 'csv_upload_handler' );
*/
/*
function csv_upload_handler() {

  $post_data = array(
    'post_title' => $_POST['post_title'],
    'post_content' => $_POST['post_content'],
    'post_status' => 'draft'
  );
  echo $_POST['post_title'];
  echo '<br/>';
  echo $_POST['post_content'];
  echo '<br/>';

  $post_id = wp_insert_post( $post_data );

  $upload = wp_upload_bits(
    $_FILES['wp_citysnail_structure_file']['name'],
    null,
    file_get_contents( $_FILES['wp_citysnail_structure_file']['tmp_name'] )
  );

  $wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );

  $wp_upload_dir = wp_upload_dir();

  $attachment = array(
    'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
    'post_mime_type' => $wp_filetype['type'],
    'post_title' => preg_replace('/\.[^.]+$/', '', basename( $upload['file'] )),
    'post_content' => '',
    'post_status' => 'inherit'
  );

  $attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );

  require_once(ABSPATH . 'wp-admin/includes/file.php');

  $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
  wp_update_attachment_metadata( $attach_id, $attach_data );

  update_post_meta( $post_id, '_thumbnail_id', $attach_id );

  wp_redirect( site_url() );

  die();
}
*/
