<?php

class Citysnail_Settings {

  public static function do_field_title_buttons() {
    $str = '';
    $str .= '<div style="display:flex;flex-flow:row wrap;justify-content:center;">';
    $str .= '<input name="submit" type="submit" id="submit" class="snail_admin" value="Save Changes"/>';
    $str .= '<button id="drop_button" class="snail_admin" style="border:1.5px solid red;">Delete All</button>';
    $str .= '</div>';
    return $str;
  }

  public static function do_structure_field_title() {
    $str = '<br/>';
    $str .= '<ul><li>Upload a formatted CSV</li>';
    $str .= '<li style="text-indent:4.5em;padding-bottom:0;margin-bottom:0;">&ndash; or &ndash;</li>';
    $str .= '<li>Create one here <sub class="bigger">&nbsp;»</sub><sub class="bigger">&nbsp;»</sub></li></ul>';
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
      'Site Structure Worksheet:' . self:: do_structure_field_title() . self::do_field_title_buttons(),               //uniqueTitle -
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
    $report =   array(
      'color' => 'red',
      'flavor' => 'good'
    );
    $report_schema = json_encode($report);
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
    $options = get_option('wp_citysnail_structure');
    $options_home = get_option('wp_citysnail');
    $options_keywords = get_option('wp_citysnail_keywords');
    /*
    $this_path = Snail_Tail::try_option_key($options,'structure_path','string');
    $this_file = Snail_Tail::try_option_key($options,'structure_file','string');
    */
    $this_path = ( $options['structure_path'] ) ?
      $options['structure_path'] : '';
    $this_domain = ( $options_home['domain'] ) ?
      $options_home['domain'] : '';
    $my_domain = '';
    // validates result of sitemap crawl
    $resources = (
      $options_keywords['resources'] &&
      is_array($options_keywords['resources']) &&
      count($options_keywords['resources'])
      ) ?
      $options_keywords['resources'] : '';
    // validates user-curated sitemap & keywords
    if ($options['my_pages'] &&
        is_array(json_decode($options['my_pages'])) &&
        count(array_keys(json_decode($options['my_pages']))) ) {
      $my_pages_list = array_keys(json_decode($options['my_pages']));
      $my_pages_schema = json_encode($options['my_pages']);
    } else {
      $my_pages_list = $resources;
      $my_pages_schema = json_encode($options['my_pages']);
    }


    if ($options_keywords['domain']) {
      $my_domain = $options_keywords['domain'];
    } else if ($this_domain) {
      $my_protocol = 'https://';
      $my_domain = (preg_match('/http(s)?\:\/\/(www)?.*/',$this_domain)) ?
        $this_domain : $my_protocol . $this_domain;
    }

    $sitemap_monster = ($resources && $my_domain) ?
      new Sitemap_Monster($my_domain,$resources) : false;

    $this_file = (!$this_path) ? 'upload a file' :
      str_replace(
        site_url(),
        '',
        preg_replace(
          '/\/wp-content\/uploads\/[0-9]{4}\/[0-9]{2}\//',
          '',
          $this_path
        )
      );

    // add UI option - use raw resources or my pages ?

    $value_tag = (!$this_path) ? 'placeholder' : 'value';
    $placeholder = (!$this_path) ? '(not set)' : $this_path;
    $sub = (!$this_path) ? '' : '<!--<br/><span>click to change file:</span>-->';
    $button_is_set = (!$this_path) ? '' : '_unset';
    $input_is_set = (!$this_path) ? '' : ' slight';

    $str = "";
    //$str .= "<div><b>upload your site structure worksheet:</b></div><br/>";
    //$str .= wp_nonce_field( 'citysnail_submit_structure', 'structure_file_nonce_field');
    $str .= "<div class='flexOuterStart'>";
    $str .= "<input type='text' class='zeroTest{$input_is_set}' id='structure_path'
      name='wp_citysnail_structure[structure_path]' {$value_tag}='{$this_path}'/>";
    $str .= $sub;
    $str .= "<div class='snail_admin' id='structure_button{$button_is_set}'><b>{$this_file}</b>";
    $str .= "<input id='structure_file' type='file' class='citysnail'
      name='wp_citysnail_structure[structure_file]'/>";
    $str .= "</div></div>";
    $str .= "<input type='text' class='citysnail invis' id='my_pages'
      name='wp_citysnail_structure[my_pages]' value='{$my_pages_schema}'/>";
    //$str .= "<input type='text' class='invis' id='post_title' name='post_title' value='{$this_domain}_structure_worksheet'/>";
    //$str .= "<input type='text' class='invis' id='post_content' name='post_content' value='{$this_domain}_structure_worksheet'/>";
    //$str .= "<input type='hidden' name='action' value='citysnail_submit_structure'>";
    echo $str;
    if ($sitemap_monster) {
      echo $sitemap_monster->get_html_table($options);
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
    $options = get_option($db_slug);
    $dropped = $options['drop'];
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option($db_slug);
    } else {
      error_log("drop=false");
    }

    foreach ($scripts as $script) {
      wp_enqueue_script($script, plugin_dir_url(__FILE__) . '../lib/citysnail_' . $script . '.js');
    }
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
      'domain' => $my_domain,
      'resources' => array(),
      'report' => ''
    );
    foreach ($sitemap_monster->new_map as $page_url) {
      $report_schema['resources'][] = $page_url;
      echo $sitemap_snail->do_sitemap_item($page_url);
    }
    $schema_string = json_encode($report_schema);
    update_option('wp_citysnail_keywords',$report_schema);
    echo '<div id="report_schema" class="citysnail invis">' . $schema_string . '</div>';
  }

}

?>
