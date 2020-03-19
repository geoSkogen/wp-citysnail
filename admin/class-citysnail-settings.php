<?php

class Citysnail_Settings {

  public static $field_count = 5;
  public static $eq_label_toggle = array(
    "param",
    "shortcode",
    "fallback"
  );
  public static $geo_label_toggle = array(
    "phone",
    "phone_shortcode",
    "locale",
    "locale_shortcode",
    "region",
    "region_shortcode",
    "service_area",
    "service_area_shortcode"
  );
  public static $current_field_index = 0;
  public static $eq_label_toggle_index = 0;
  public static $geo_label_toggle_index = 0;

  public static function wp_citysnail_settings_api_init() {
    add_settings_section(
      'wp_citysnail_settings',                         //uniqueID
      'Enter Your Domain & Sitemap',   //Title
      array('Citysnail_Settings','wp_citysnail_settings_section'),//CallBack Function
      'wp_citysnail'                                //page-slug
    );

    add_settings_section(
      'wp_citysnail_keywords',                         //uniqueID
      'Associate Keywords with URLs',        //Title
      array('Citysnail_Settings','wp_citysnail_keywords_section'),//CallBack Function
      'wp_citysnail_keywords'                         //page-slug
    );

    for ($i = 1; $i < self::$field_count + 1; $i++) {
      self::$current_field_index = $i;
      for ($ii = 0; $ii < count(self::$eq_label_toggle); $ii++) {
        $field_name = self::$eq_label_toggle[$ii];
        $this_field = $field_name . "_" . strval(self::$current_field_index);
        $this_label = ucwords($field_name) . " " . strval(self::$current_field_index);

        add_settings_field(
          $this_field,                   //uniqueID - "param_1", etc.
          $this_label,                  //uniqueTitle -
          array('Citysnail_Settings','wp_citysnail_settings_field'),//callback cb_equips_settings_field();
          'wp_citysnail',                   //page-slug
          'wp_citysnail_settings'          //section (parent settings-section uniqueID)
        );
      }
    }
    self::$current_field_index = 1;
    for ($iii = 0; $iii < count(self::$geo_label_toggle); $iii++) {
      $geo_field_name = self::$geo_label_toggle[$iii];
      $this_geo_field = $geo_field_name;
      $this_geo_label = ucwords(str_replace("_", " ", $geo_field_name));

      add_settings_field(
        $this_geo_field,                   //uniqueID - "param_1", etc.
        $this_geo_label,                  //uniqueTitle -
        array('Citysnail_Settings','wp_citysnail_keywords_field'),//callback cb_equips_settings_field();
        'wp_citysnail_keywords',                   //page-slug
        'wp_citysnail_keywords'          //section (parent settings-section uniqueID)
      );
    }

    register_setting( 'wp_citysnail', 'wp_citysnail' );
    register_setting( 'wp_citysnail_keywords', 'wp_citysnail_keywords' );
  }

  //Templates

  ////template 3 - settings section field - dynamically rendered <input/>

  static function wp_citysnail_settings_field() {
    $options = get_option('wp_citysnail');
    //error_log(print_r($options));
    //local namespace assignments based on global settings &/or database state
    $divider = (self::$eq_label_toggle_index < count(self::$eq_label_toggle)-1) ?
      "" : "<br/><br/><hr/>";
    $field_name = self::$eq_label_toggle[self::$eq_label_toggle_index];
    $this_field = $field_name . "_" . strval(self::$current_field_index);
    $this_label = ucwords($field_name) . " " . strval(self::$current_field_index);
    $placeholder = ("" != ($options[$this_field])) ? $options[$this_field] : "(not set)";
    $value_tag = ($placeholder === "(not set)") ? "placeholder" : "value";
    //reset globals - toggle label and increment pairing series as needed
    self::$eq_label_toggle_index +=
      (self::$eq_label_toggle_index < count(self::$eq_label_toggle)-1 ) ?
      1 : -(count(self::$eq_label_toggle)-1);
    self::$current_field_index += (self::$eq_label_toggle_index === 0) ?
      1 : 0;
    //make an <input/> with dynamic attributes
    echo "<input type='text' name=wp_citysnail[{$this_field}] {$value_tag}='{$placeholder}'/>" . $divider;
  }

  static function wp_citysnail_keywords_field() {
    $options = get_option('wp_citysnail_keywords');
    //error_log(print_r($options));
    //local namespace assignments based on global settings &/or database state
    $divider = (self::$geo_label_toggle_index < count(self::$geo_label_toggle)-1) ?
      "" : "<br/><br/><hr/>";
    $field_name = self::$geo_label_toggle[self::$geo_label_toggle_index];
    $this_field = $field_name;
    $this_label = ucwords($field_name);
    $placeholder = ("" != ($options[$this_field])) ? $options[$this_field] : "(not set)";
    $value_tag = ($placeholder === "(not set)") ? "placeholder" : "value";
    //reset globals - toggle label and increment pairing series as needed
    self::$geo_label_toggle_index +=
      (self::$geo_label_toggle_index < count(self::$geo_label_toggle)-1 ) ?
      1 : -(count(self::$geo_label_toggle)-1);
    //make an <input/> with dynamic attributes
    echo "<input type='text' name=wp_citysnail_keywords[{$this_field}] {$value_tag}='{$placeholder}'/>" . $divider;
  }

  ////template 2 - after settings section title

  static function wp_citysnail_keywords_section() {
    $options = get_option('wp_citysnail_keywords');
    $dropped = $options['drop'];
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option('wp_citysnail_keywords');
    } else {
      error_log("drop=false");
    }
    wp_enqueue_script('wp_citysnail-unset-all', plugin_dir_url(__FILE__) . '../lib/wp_citysnail-unset-all.js');
    ?>
    <hr/>
    <div style="display:flex;flex-flow:row wrap;justify-content:space-between;">
      <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
      <button id='drop_button' class='button-primary' style='border:1.5px solid red;'>
        <?php _e("Delete All") ?>
      </button>
    </div>
    <?php
  }

  static function wp_citysnail_settings_section() {
    $options = get_option('wp_citysnail');
    $dropped = $options['drop'];
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option('wp_citysnail');
    } else {
      error_log("drop=false");
    }
    wp_enqueue_script('wp_citysnail-unset-all', plugin_dir_url(__FILE__) . '../lib/wp_citysnail-unset-all.js');
    ?>
    <hr/>
    <div style="display:flex;flex-flow:row wrap;justify-content:space-between;">
      <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
      <button id='drop_button' class='button-primary' style='border:1.5px solid red;'>
        <?php _e("Delete All") ?>
      </button>
    </div>
    <?php
  }

}

?>
