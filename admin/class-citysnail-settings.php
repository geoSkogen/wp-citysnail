<?php

class Citysnail_Settings {

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

    add_settings_field(
      'domain',                   //uniqueID - "param_1", etc.
      'Domain',                  //uniqueTitle -
      array('Citysnail_Settings','wp_citysnail_domain_field'),//callback
      'wp_citysnail',                   //page-slug
      'wp_citysnail_settings'          //section (parent settings-section uniqueID)
    );

    add_settings_field(
      'sitemap',                   //uniqueID - "param_1", etc.
      'Sitemap',                  //uniqueTitle -
      array('Citysnail_Settings','wp_citysnail_sitemap_field'),//callback
      'wp_citysnail',                   //page-slug
      'wp_citysnail_settings'          //section (parent settings-section uniqueID)
    );

    add_settings_field(
      'oil',                   //uniqueID - "param_1", etc.
      'oil',                  //uniqueTitle -
      array('Citysnail_Settings','wp_citysnail_keywords_field'),//callback
      'wp_citysnail_keywords',                   //page-slug
      'wp_citysnail_keywords'          //section (parent settings-section uniqueID)
    );

    register_setting( 'wp_citysnail', 'wp_citysnail' );
    register_setting( 'wp_citysnail_keywords', 'wp_citysnail_keywords' );
  }

  //Templates

  ////template 3 - settings section field - dynamically rendered <input/>

  static function wp_citysnail_keywords_field() {
    echo "oil";
  }

  static function wp_citysnail_domain_field() {
    echo self::do_simple_dynamic_input('wp_citysnail','domain','(not set)');
  }

  static function wp_citysnail_sitemap_field() {
    echo self::do_simple_dynamic_input('wp_citysnail','sitemap','(not set)');
  }

  static function do_simple_dynamic_input($db_slug,$this_field,$fallback_str) {
    $options = get_option($db_slug);
    $placeholder = ("" != ($options[$this_field])) ? $options[$this_field] : $fallback_str;
    $value_tag = ($placeholder === $fallback_str) ? "placeholder" : "value";
    return "<input type='text' name={$db_slug}[$this_field] {$value_tag}='{$placeholder}'/>";
  }

  ////template 2 - after settings section title

  static function wp_citysnail_settings_section() {
    self::do_simple_dynamic_section('wp_citysnail',['citysnail-unset-all']);
  }


  static function wp_citysnail_keywords_section() {
    self::do_sitemap_keywords_section('wp_citysnail_keywords',
    ['citysnail_unset_all','citysnail_sitemap_nester']);
  }

  static function do_simple_dynamic_section($db_slug,$scripts) {
    $options = get_option($db_slug);
    $dropped = $options['drop'];
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option($db_slug);
    } else {
      error_log("drop=false");
    }
    foreach ($scripts as $script) {
      wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../lib/' . $script . '.js');
    }
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

  static function do_sitemap_keywords_section($db_slug,$scripts) {
    $options = get_option($db_slug);
    $dropped = $options['drop'];
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option($db_slug);
    } else {
      error_log("drop=false");
    }
    $options = get_option('wp_citysnail');
    $my_domain_name = (isset($options['domain']) && $options['domain'] != '') ?
      $options['domain'] : false;
    if ($my_domain_name) {
      $my_protocol = 'https://';
      $my_domain = (preg_match('/http(s)?\:\/\/(www)?.*/',$my_domain_name)) ?
        $my_domain_name : $my_protocol . $my_domain_name;
      $map_name = ($options['sitemap']) ? $options['sitemap'] : 'sitemap.xml';
      $map_dom = Snail::curl_get_dom($my_domain . '/' . $map_name);
      $map_list = Snail::parse_sitemap_dom($map_dom);
      $sitemap_monster = new Sitemap_Monster($my_domain,$map_list);
      $sitemap_snail = new Sitemap_Snail($sitemap_monster);
    }
    foreach ($scripts as $script) {
      wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../lib/' . $script . '.js');
    }
    wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../css/' . 'yuckstyle' . '.css');
    ?>
    <hr/>
    <div style="display:flex;flex-flow:row wrap;justify-content:space-between;">
      <input name='submit' type='submit' id='submit' class='button-primary' value='<?php _e("Save Changes") ?>' />
      <button id='drop_button' class='button-primary' style='border:1.5px solid red;'>
        <?php _e("Delete All") ?>
      </button>
    </div>
    <?php
    foreach ($sitemap_monster->new_map as $page_url) {
      echo $sitemap_snail->do_sitemap_item($page_url);
    }
  }

}

?>
