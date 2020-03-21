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
        'WP Citysnail Keywords',                // Page Title
        'citysnail keywords',               // Menu Title
        'manage_options',             // for Capabilities level of user with:
        'wp_citysnail_keywords',             // menu Slug(page)
        array('Citysnail_Options','wp_citysnail_keywords_page')// CB Function plugin_options_page()
    );
  }

  //// template 1 - <form> body

  static function wp_citysnail_options_page() {
    ?>
    <div class='form-wrap'>
      <h2>WP Citysnail - Settings</h2>
      <form method='post' action='options.php' id='wp-citysnail-form'>
        <?php
          settings_fields( 'wp_citysnail' );
          do_settings_sections( 'wp_citysnail' );
        ?>
        <div class='inivs-div' style="display:none;">
          <input class='invis-input' id='drop_field' name=wp_citysnail[drop] type='text'/>
        </div>
        <p class='submit'>
          <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
        </p>
      </form>
    </div>
    <?php
  }

  static function wp_citysnail_keywords_page() {
    ?>
    <div class='form-wrap'>
      <h2>WP Citysnail - Keywords</h2>
      <form method='post' action='options.php' id='wp-citysnail-keywords-form'>
        <?php
          settings_fields( 'wp_citysnail_keywords' );
          do_settings_sections( 'wp_citysnail_keywords' );
        ?>
        <div class='inivs-div' style="display:none;">
          <input class='invis-input' id='drop_field' name=wp_citysnail_keywords[drop] type='text'/>
        </div>
        <p class='submit'>
          <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
        </p>
      </form>
    </div>
    <?php
  }

}

?>
