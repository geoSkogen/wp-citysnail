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

  public static function validate_structure_file($schema,$table,$list,$host) {
    $result = new stdClass();
    $result->schema = array();
    $result->error = [false,[],[],[]];
    $index = 0;
    if (is_array($schema)) {
      foreach ($schema as $key => $arr) {
        //TRIM KEY of extra slashes
        $key = (strpos($key,'/')===0) ? substr($key,1) :  $key;
        $key = (substr($key,strlen($key)-1,1)==='/') ?
          substr($key,0,strlen($key)-1) :  $key;
        $abs = in_array($key . '/',$list);
        $rel = in_array($host . '/' . $key . '/',$list);
        //FIXED DATA SETTING - error array key assignments:
        //1 - not found, 2 - missing, 3 - fatal
        if (!$key) {
          $result->error[2][] =
            'missing URI in row: ' . strval($index+1);
        } else if ( $abs || $rel ) {
          //FIXED DATA SETTING!!! takes first seven arguments only!
          $endslice = (count($arr) > 7) ? 7 :  count($arr);
          $result->schema[$key . '/'] = array_slice($arr,0,$endslice);
        } else {
          $result->error[1][] = 'URI not found: ' .
              $key . ' - at row ' . strval($index+1);
        }
        $index++;
      }
      if (!count(array_keys($result->schema))) {
        $result->error[3][] = 'invalid data format - no URIs provided';
      }
    } else {
      $result->error[3][] = 'invalid data format - unrecognized file type';
    }

    $result->error[0] = (count($result->error[3])) ? true : $result->error[0];

    return $result;
  }

  public static function get_page_schema($resource_arr,$db_table) {
    $schema = array();
    foreach($resource_arr as $url) {
      $resource = urldecode($url);
      $schema[$resource] = ( isset($db_table[$resource]) ) ?
        $db_table[$resource] : '';
    }
    return $schema;
  }

  public static function get_client_snail($db_prefix,$db_slugs,$this_db_slug) {

    $result = new stdClass();
    $options = array(
      'home'
    );
    /*
    $options['home'] = get_option('wp_citysnail');
    $options['structure'] = get_option('wp_citysnail_structure');
    $options['keywords'] = get_option('wp_citysnail_keywords');
    */
    /*
    $this_path = Snail_Tail::try_option_key($options,'structure_path','string');
    $this_file = Snail_Tail::try_option_key($options,'structure_file','string');
    */
    $this_options = get_option($this_db_slug);
    $dropped = (isset($this_options['drop'])) ? $this_options['drop'] : '';
    if ($dropped === "TRUE") {
      error_log('got drop call for options => ' . $this_db_slug);
      delete_option($this_db_slug);
    } else {
      //error_log("drop=false");
    }

    foreach($db_slugs as $slug) {
      $splitter = ($slug) ? '_' : '';
      $key = $db_prefix . $splitter . $slug;
      $prop = ($slug) ? $slug : 'home';
      $options[$prop] = get_option($key);
    }

    $message_table = '<table>';

    $this_path = ( isset($options['structure']['structure_path']) ) ?
      $options['structure']['structure_path'] : '';

    $this_domain = ( isset($options['home']['domain']) ) ?
      $options['home']['domain'] : '';
    $this_domain = (substr($this_domain,strlen($this_domain)-1,1)==='/') ?
        substr($this_domain,0,strlen($this_domain)-1) : $this_domain;


    $map_name = ( isset($options['home']['sitemap']) ) ?
      $options['home']['sitemap'] : 'sitemap.xml';

    $format_field = ( isset($options['structure']['format']) ) ?
      $options['structure']['format'] : 'sitemap';

    switch($format_field) {
      case 'file' :
        $format = ($this_path) ? 'file' : 'sitemap';
        break;
      case 'structure' :
      case 'sitemap' :
        $format = $format_field;
        break;
      default:
        $format = 'sitemap';
    }

    $my_domain = '';
    if ($this_domain) {
      $my_protocol = 'https://';
      $my_domain = (preg_match('/http(s)?\:\/\/(www)?.*/',$this_domain)) ?
        $this_domain : $my_protocol . $this_domain;
    }

    if (isset($options['keywords']['resources']) &&
        is_array($options['keywords']['resources']) &&
        count($options['keywords']['resources'])) {
      $resources = $options['keywords']['resources'];
    } else {
      $map_dom = Snail::curl_get_dom($my_domain . '/' . $map_name);
      $resources = Snail::parse_sitemap_dom($map_dom);
    }
    //default setting - crawl the whole site
    $my_pages_schema = self::get_page_schema($resources,$options['structure']);
    $my_pages_list = $resources;

    switch($this_db_slug) {
      case 'wp_citysnail_structure' :
        switch ($format) {
          case 'file' :
              $structure = ($this_path) ?
                self::validate_structure_file(
                  Snail_File::parse_structure_file($this_path),
                  $this_options,
                  $resources,
                  $my_domain
                ) : '';
              for ($i = 1; $i < count($structure->error); $i++) {
                foreach ($structure->error[$i] as $msg) {
                  $message_table .= "<tr>error #" . strval($i) . ": " . $msg. "</tr>";
                }
              };
              if (!$structure->error[0]) {
                error_log('got valid file crawl');
                $my_pages_schema = $structure->schema;
                $my_pages_list = array_keys($structure->schema);
              } else {
                $format = 'sitemap';
                error_log('fatal structure file error');
              }
            break;
          case 'structure' :
            if (isset($options['structure']['my_pages']) &&
                is_string($options['structure']['my_pages'])) {

                  $my_pages_schema = $options['structure']['my_pages'];
                  $my_pages_list = array_keys(
                    json_decode($options['structure']['my_pages'],true)
                  );
                }
            break;
          case 'sitemap' :
            break;
          default :
        }
        break;
      default :

    }

    $sitemap_monster = ($my_pages_list && $my_domain) ?
      new Sitemap_Monster($my_domain,$my_pages_list) : false;
    $message_table .= '</table>';
    $result->sitemap_monster = $sitemap_monster;
    $result->this_path = $this_path;
    $result->my_pages_list = $my_pages_list;
    $result->my_pages_schema = json_encode($my_pages_schema);
    $result->my_domain = $my_domain;
    $result->options = $this_options;
    $result->map_name = $map_name;
    $result->resources = $resources;
    $result->format = $format;
    $result->message = $message_table;

    return $result;
  }

  public static function url_roll_call() {

  }
}

?>
