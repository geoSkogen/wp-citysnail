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

  public static function get_client_snail() {

    $result = new stdClass();
    $options_home = get_option('wp_citysnail');
    $options_structure = get_option('wp_citysnail_structure');
    $options_keywords = get_option('wp_citysnail_keywords');
    /*
    $this_path = Snail_Tail::try_option_key($options,'structure_path','string');
    $this_file = Snail_Tail::try_option_key($options,'structure_file','string');
    */
    $this_path = ( $options_structure['structure_path'] ) ?
      $options_structure['structure_path'] : '';

    $this_domain = ( $options_home['domain'] ) ?
      $options_home['domain'] : '';

    $my_domain = '';
    if ($this_domain) {
      $my_protocol = 'https://';
      $my_domain = (preg_match('/http(s)?\:\/\/(www)?.*/',$this_domain)) ?
        $this_domain : $my_protocol . $this_domain;
    }
    // validates result of sitemap crawl
    if ($options_keywords['resources'] &&
       is_array($options_keywords['resources']) &&
       count($options_keywords['resources'])) {
       $resources = $options_keywords['resources'];
     } else {
       $map_name = ($options['sitemap']) ? $options['sitemap'] : 'sitemap.xml';
       $map_dom = Snail::curl_get_dom($my_domain . '/' . $map_name);
       $resources = Snail::parse_sitemap_dom($map_dom);
     }

    // validates user-curated sitemap & keywords
    if ($options_structure['my_pages'] &&
        is_array(json_decode($options_structure['my_pages'])) &&
        count(array_keys(json_decode($options_structure['my_pages']))) ) {
      $my_pages_list = array_keys(json_decode($options_structure['my_pages']));
      $my_pages_schema = json_encode($options_structure['my_pages']);
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

    return $result;
  }
}

?>
