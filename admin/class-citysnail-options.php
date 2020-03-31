<?php


class Citysnail_Options {

  static function wp_citysnail_register_menu_page() {
      add_menu_page(
        'WP Citysnail',                        // Page Title
        'citysnail',                       // Menu Title
        'manage_options',             // for Capabilities level of user with:
        'wp_citysnail',                    // menu Slug(page)
        array('Citysnail_Options','wp_citysnail_options_page'), // CB Function cb_equips_options_page()
        'dashicons-media-code',  // Menu Icon
        20
      );

      add_submenu_page(
        'wp_citysnail',                         //parent menu
        'WP Citysnail Structure',                // Page Title
        'citysnail structure',               // Menu Title
        'manage_options',             // for Capabilities level of user with:
        'wp_citysnail_structure',             // menu Slug(page)
        array('Citysnail_Options','wp_citysnail_structure_page')// CB Function plugin_options_page()
    );

      add_submenu_page(
        'wp_citysnail',                         //parent menu
        'WP Citysnail Keywords',                // Page Title
        'citysnail keywords',               // Menu Title
        'manage_options',             // for Capabilities level of user with:
        'wp_citysnail_keywords',             // menu Slug(page)
        array('Citysnail_Options','wp_citysnail_keywords_page')// CB Function plugin_options_page()
    );
  }

  //// template 1 - <form> body

  static function wp_citysnail_options_page() {
    self::do_simple_dynamic_page('wp_citysnail','options.php');
  }

  static function wp_citysnail_keywords_page() {
    self::do_simple_dynamic_page('wp_citysnail_keywords','options.php');
  }

  static function wp_citysnail_structure_page() {
    $action = admin_url( 'admin-ajax.php' );
    self::do_simple_dynamic_page('wp_citysnail_structure',$action);
  }

  static function do_simple_dynamic_page($db_slug,$post_action) {
    wp_register_style('yuckstyle', plugin_dir_url(__FILE__) . '../styles/' . 'yuckstyle' . '.css');
    wp_enqueue_style('yuckstyle');
    ?>
    <div class='form-wrap'>
      <h3>WP Citysnail</h3>
      <form method='post' action='<?php echo $post_action; ?>' id='<?php echo $db_slug; ?>-form' enctype='multipart/form-data'>
        <?php
          settings_fields( $db_slug );
          do_settings_sections( $db_slug );
        ?>
        <div class='inivs-div' style="display:none;">
          <input class='invis-input' id='drop_field' name=<?php echo "{$db_slug}[drop]"; ?> type='text'/>
        </div>
        <p class='submit'>
          <input name='submit' type='submit' id='submit' class='snail_admin' value='<?php _e("Save Changes") ?>' />
        </p>
      </form>
    </div>
    <?php
  }

}

?>
