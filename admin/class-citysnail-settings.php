<?php

class Citysnail_Settings {

  public static $db_prefix = 'wp_citysnail';
  public static $db_slugs = ['','structure','keywords'];

  public static function do_field_title_buttons() {
    $str = '';
    $str .= '<div style="display:flex;flex-flow:row wrap;justify-content:center;">';
    $str .= '<input name="submit" type="submit" id="top_submit" class="snail_admin" value="Save Changes"/>';
    $str .= '<button id="drop_button" class="snail_admin" style="border:1.5px solid red;">Delete All</button>';
    $str .= '</div>';
    return $str;
  }

  public static function do_structure_field_title() {
    $str = '';
    $str .= '<ul><li>Upload a CSV file</li>';
    $str .= '<li style="text-indent:4.5em;padding-bottom:0;margin-bottom:0;">&ndash; or &ndash;</li>';
    $str .= '<li>-or Create one here <sub class="bigger">&nbsp;»</sub><sub class="bigger">&nbsp;»</sub></li></ul>';
    return $str;
  }

  public static function wp_citysnail_settings_api_init() {

    add_settings_section(
      'wp_citysnail_settings',                         //uniqueID
      'Enter Your Domain & Sitemap<hr/>',   //Title
      array('Citysnail_Settings','wp_citysnail_settings_section'),//CallBack Function
      'wp_citysnail'                                //page-slug
    );

    add_settings_section(
      'wp_citysnail_keywords',                         //uniqueID
      'Associate Keywords with URLs<hr/>',        //Title
      array('Citysnail_Settings','wp_citysnail_keywords_section'),//CallBack Function
      'wp_citysnail_keywords'                         //page-slug
    );

    add_settings_section(
      'wp_citysnail_structure',                         //uniqueID
      'Create Your Site Structure Worksheet<hr/>',        //Title
      array('Citysnail_Settings','wp_citysnail_structure_section'),//CallBack Function
      'wp_citysnail_structure'                         //page-slug
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
      'structure',                   //uniqueID - "param_1", etc.
      ' ' . self::do_field_title_buttons(),               //uniqueTitle -
      array('Citysnail_Settings','wp_citysnail_structure_field'),//callback
      'wp_citysnail_structure',                   //page-slug
      'wp_citysnail_structure'          //section (parent settings-section uniqueID)
    );

    add_settings_field(
      'snail_modal',                   //uniqueID - "param_1", etc.
      '&nbsp',                  //uniqueTitle -
      array('Citysnail_Settings','wp_citysnail_keywords_field'),//callback
      'wp_citysnail_keywords',                   //page-slug
      'wp_citysnail_keywords'          //section (parent settings-section uniqueID)
    );

    register_setting( 'wp_citysnail', 'wp_citysnail' );
    register_setting( 'wp_citysnail_structure', 'wp_citysnail_structure' );
    register_setting( 'wp_citysnail_keywords', 'wp_citysnail_keywords' );
  }

  ////template 3 - settings section field - dynamically rendered <input/>

  static function wp_citysnail_keywords_field() {
    $str = "";
    $sections = ['title','title_vs_h1','h1','anchors','images'];
    $str .= '<div class="flexOuterCenter"><div id="relshell">';
    $str .= '<div data-toggle="block" class="invis" id="snail_modal">';
    $str .= '<div class="flexOuterEnd"><div id="close_modal">&times;</div></div>';
    foreach ($sections as $section) {
      $str .= "<h2>{$section}</h2>";
      $str .= '<section id="' . $section . '" class="nodeReport"></section>';
    }
    $str .= '</div></div></div>';
    echo $str;
  }

  static function wp_citysnail_domain_field() {
    echo self::do_simple_dynamic_input('wp_citysnail','domain','(not set)');
  }

  static function wp_citysnail_sitemap_field() {
    echo self::do_simple_dynamic_input('wp_citysnail','sitemap','(not set)');
  }

  static function wp_citysnail_structure_field() {
    $client_snail = Snail_Tail::get_client_snail(
      self::$db_prefix,
      self::$db_slugs,
      'wp_citysnail_structure'
    );
    $sitemap_snail = new Sitemap_Snail($client_snail->sitemap_monster);
    $this_file = (!$client_snail->this_path) ? 'upload a file' :
      str_replace(
        site_url(),
        '',
        preg_replace(
          '/\/wp-content\/uploads\/[0-9]{4}\/[0-9]{2}\//',
          '',
          $client_snail->this_path
        )
      );
    //display options
    $value_tag = (!$client_snail->this_path) ? 'placeholder' : 'value';
    $placeholder = (!$client_snail->this_path) ? '(not set)' : $client_snail->this_path;
    $sub = (!$client_snail->this_path) ? '' : '<!--<br/><span>click to change file:</span>-->';
    $button_is_set = (!$client_snail->this_path) ? '' : '_unset';
    $input_is_set = (!$client_snail->this_path) ? '' : ' slight';
    $select = array( 'sitemap' => '', 'structure' => '', 'file'=>'',''=>'');
    $select[$client_snail->format] = 'checked';
    $my_pages_db_map = " name='wp_citysnail_structure[my_pages]' ";
    //uploader inputs
    $str = "";
    //$str .= wp_nonce_field( 'citysnail_submit_structure', 'structure_file_nonce_field');
    $str .= "<div class='flexOuterStart'><span>Options:</span>";
    $str .= "<input type='radio' name='wp_citysnail_structure[format]' value='structure' {$select['structure']}/>";
    $str .= "<label class='radioLabel' for='structure'>my structure worksheet</label>";
    $str .= "<input type='radio' name='wp_citysnail_structure[format]' value='file' {$select['file']}/>";
    $str .= "<label class='radioLabel' for='sitemap'>crawl structure file</label>";
    $str .= "<input type='radio' name='wp_citysnail_structure[format]' value='sitemap' {$select['sitemap']}/>";
    $str .= "<label class='radioLabel' for='sitemap'>crawl full sitemap</label>";
    $str .= "</div>";

    $str .= "<div class='flexOuterStart'>";
    $str .= "<input type='text' class='zeroTest{$input_is_set}' id='structure_path'
      name='wp_citysnail_structure[structure_path]' {$value_tag}='{$client_snail->this_path}'/>";
    $str .= $sub;
    $str .= "<div class='snail_admin' id='structure_button{$button_is_set}'><b>{$this_file}</b>";
    $str .= "<input id='structure_file' type='file' class='citysnail'
      name='wp_citysnail_structure[structure_file]'/>";
    $str .= "</div></div>";
    $str .= "<input type='text' class='citysnail invis' id='my_pages' $my_pages_db_map
       value='{$client_snail->my_pages_schema}'/>";
    //$str .= "<input type='text' class='invis' id='post_title' name='post_title' value='{$this_domain}_structure_worksheet'/>";
    //$str .= "<input type='text' class='invis' id='post_content' name='post_content' value='{$this_domain}_structure_worksheet'/>";
    //$str .= "<input type='hidden' name='action' value='citysnail_submit_structure'>";
    //uploader inputs
    echo $str;
    echo $client_snail->message;
    //interactive sitemap table
    if ($client_snail->sitemap_monster) {
      echo $client_snail->sitemap_monster->get_html_table($client_snail->options);
    }
  }

  static function do_simple_dynamic_input($db_slug,$this_field,$fallback_str) {
    $options = ( get_option($db_slug) ) ? get_option($db_slug) : $db_slug;
    //$placeholder = Snail_Tail::try_option_key($options,$this_field,$fallback_str);
    $placeholder = ("" != ($options[$this_field])) ? $options[$this_field] : $fallback_str;
    $value_tag = ($placeholder === $fallback_str) ? "placeholder" : "value";
    return "<input type='text' class='citysnail zeroTest' id='{$this_field}'
      name={$db_slug}[$this_field] {$value_tag}='{$placeholder}'/>";
  }

  ////template 2 - after settings section title

  static function wp_citysnail_settings_section() {
    self::do_simple_dynamic_section('wp_citysnail',[]);
  }

  static function wp_citysnail_structure_section() {
    self::do_simple_dynamic_section('wp_citysnail_structure',
      ['unset_all','upload_helper','structure_helper','mypages_editor']
    );
    wp_enqueue_media();
    wp_register_script(
      'citysnail_media_uploader_csv',
      plugins_url('../lib/citysnail_media_uploader_csv.js', __FILE__),
      array('jquery')
    );
    wp_enqueue_script('citysnail_media_uploader_csv');
  }

  static function wp_citysnail_keywords_section() {
    self::do_sitemap_keywords_section('wp_citysnail_keywords',
      ['unset_all','sitemap_nester','modal_model']
    );
  }

 static function do_simple_dynamic_section($db_slug,$scripts) {
    foreach ($scripts as $script) {
      wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../lib/citysnail_' . $script . '.js');
    }
  }

  static function do_sitemap_keywords_section($db_slug,$scripts) {
    $client_snail = Snail_Tail::get_client_snail(
      self::$db_prefix,
      self::$db_slugs,
      $db_slug
    );
    $sitemap_snail = new Sitemap_Snail($client_snail->sitemap_monster);
    /*
    Snail::init_curl_crawl(
      $client_snail->my_domain,
      $client_snail->map_name,
      $client_data
    );
    */
    wp_enqueue_script('wp-citysnail-fa', 'https://kit.fontawesome.com/a076d05399.js');
    foreach ($scripts as $script) {
      wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../lib/citysnail_' . $script . '.js');
    }
    ?>
    <hr/>
    <div style="display:flex;flex-flow:row wrap;justify-content:space-between;">
      <input name="submit" type="submit" id="submit" class="snail_admin" value="<?php _e("Save Changes") ?>"/>
      <button id="drop_button" class="snail_admin" style="border:1.5px solid red;">
        <?php _e("Delete All") ?>
      </button>
    </div>
    <?php
    $report_schema = array(
      'domain' => $client_snail->my_domain,
      'resources' => array(),
      'report' => ''
    );
    foreach ($client_snail->sitemap_monster->new_map as $page_url) {
      $report_schema['resources'][] = $page_url;
      echo $sitemap_snail->do_sitemap_item($page_url);
    }
    $schema_string = json_encode($report_schema);
    update_option($db_slug,$report_schema);
    echo '<div id="report_schema" class="citysnail invis">' . $schema_string . '</div>';
  }

}

?>
