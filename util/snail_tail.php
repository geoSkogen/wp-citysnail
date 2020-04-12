<?php

class Snail_Tail {
  /*
  attempt to assemble the most "silent" method for querying the options table when
  1) the value of an associative property from that option is needed--get_option('my_option')['my_key']
  2) the existence of that property is unverified: (get_option('my_option')['my_key'])?
  3) the existence of that option itself is unverified: (get_option('my_option'))?
  */

  function __construct() {

  }

  public static function try_option_key($table,$array_key,$type) {
    $result = false;
    $try_object = ( is_array($table) ) ? $table : ( get_option($table) ) ?
      get_option($table) : $result;
    $result = (
      ($try_object) && is_array($try_object) &&
      array_key_exists($array_key,$try_object) &&
      $try_object[$array_key] != ''
      ) ?
      $try_object[$array_key] : $result;
    if (!$result) {
      switch($type) {
        case 'bool' :
          break;
        case 'string' :
          $result = '';
          break;
        case 'array' :
          $result = [];
          break;
        default :
          $result = $type;
      }
    }
    return $result;
  }

  public function formless_submit($tables,$keys) {

  }

  public static function get_client_snail($db_prefix,$db_slugs,$this_db_slug) {

    $result = new stdClass();
    $options = array(
      'home'
    );
    $options['home'] = get_option('wp_citysnail');
    $options['structure'] = get_option('wp_citysnail_structure');
    $options['keywords'] = get_option('wp_citysnail_keywords');
    /*
    $this_path = Snail_Tail::try_option_key($options,'structure_path','string');
    $this_file = Snail_Tail::try_option_key($options,'structure_file','string');
    */
    $this_options = get_option($this_db_slug);
    $dropped = (isset($this_options['drop'])) ? $this_options['drop'] : '';
    if ($dropped === "TRUE") {
      error_log('got drop');
      delete_option($this_db_slug);
    } else {
      error_log("drop=false");
    }

    foreach($db_slugs as $slug) {
      $splitter = ($slug) ? '_' : '';
      $key = $db_prefix . $splitter . $slug;
      $options[$slug] = get_option($key);
    }

    $this_path = ( $options['structure']['structure_path'] ) ?
      $options['structure']['structure_path'] : '';

    $this_domain = ( $options['home']['domain'] ) ?
      $options['home']['domain'] : '';

    $my_domain = '';
    if ($this_domain) {
      $my_protocol = 'https://';
      $my_domain = (preg_match('/http(s)?\:\/\/(www)?.*/',$this_domain)) ?
        $this_domain : $my_protocol . $this_domain;
    }
    // validates result of sitemap crawl
    if ($options['keywords']['resources'] &&
       is_array($options['keywords']['resources']) &&
       count($options['keywords']['resources'])) {
       $resources = $options['keywords']['resources'];
     } else {
       $map_name = ($options['sitemap']) ? $options['sitemap'] : 'sitemap.xml';
       $map_dom = Snail::curl_get_dom($my_domain . '/' . $map_name);
       $resources = Snail::parse_sitemap_dom($map_dom);
     }

    // validates user-curated sitemap & keywords
    if ( isset($options['structure']['my_pages']) &&
        is_string($options['structure']['my_pages'])) {
      $my_pages_list = array_keys(json_decode($options['structure']['my_pages'],true));
      $my_pages_schema = $options['structure']['my_pages'];
    } else {
      $my_pages_list = $resources;
      $my_pages_schema = '';
    }

    $sitemap_monster = ($resources && $my_domain) ?
      new Sitemap_Monster($my_domain,$resources) : false;

    $result->sitemap_monster = $sitemap_monster;
    $result->this_path = $this_path;
    $result->my_pages_list = $my_pages_list;
    $result->my_pages_schema = $my_pages_schema;
    $result->my_domain = $my_domain;
    $result->options = $this_options;

    return $result;
  }
}

?>
