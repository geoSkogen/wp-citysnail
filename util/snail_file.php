<?php

class Snail_File {

  function __constructor() {

  }

  public static function parse_structure_file($abspath) {
    $result = array();
    if (strpos($abspath,'.csv')==strlen($abspath)-4) {
      $result_schema = new Schema($abspath);
      $result = Schema::get_labeled_rows($result_schema->data_index);
    } else {
      $result = false;
    }
    return $result;
  }

  public static function upload_handler() {

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
}
