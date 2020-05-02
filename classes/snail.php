<?php

class Snail {

  public $name = '';

  function __construct() {

  }

  public static function curl_get_dom($curl_url) {
    // create curl resource
    $ch = curl_init();
    // set url
    curl_setopt($ch, CURLOPT_URL, $curl_url);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output contains the output string
    $output = curl_exec($ch);
    // close curl resource to free up system resources
    curl_close($ch);

    $dom = new DOMDocument();

    if (!$output) {
      error_log('cURL error - no response for ' . $curl_url);
    } else {
      $dom->loadHTML($output);
    }

    return $dom;
  }

  public static function spider_page_dom($dom,$data,$url) {

    $keyword = ($data['struct'][$url][0]) ?
      $data['struct'][$url][0] : '__not_set__';
    $tag_list = ['title','h1','h2','h3','a','img'];
    $dom_objs = [];
    $crawl_report = [];

    foreach ($tag_list as $tag_name) {
      $dom_objs[$tag_name] = ($dom->getElementsByTagName('title')) ?
        $dom->getElementsByTagName($tag_name) : [];
    }
    //headband
    echo $url;
    echo '<br/>';
    echo 'keyword: <b>' . $keyword  . '</b>';
    echo '<br/>';
    echo("title: " .  $dom_objs['title']->item(0)->nodeValue);
    echo '<br/>';
    echo("h1: " .  $dom_objs['h1']->item(0)->nodeValue);
    echo '<br/>';
    if (count($dom_objs['h1']) > 1) {
      echo 'multiple h1 tags--count: ' . strval(count($dom_objs['h1']));
    }
    //report elements
    foreach ($dom_objs as $tag_name => $dom_obj) {
      switch ($tag_name) {
        case 'title' :
        case 'h1' :
          $crawl_report[$tag_name] = self::cross_crawl(
            $dom_obj->item(0)->nodeValue,$keyword,[$tag_name],false
          );
          $crawl_report['title_v_h1'] = self::cross_crawl(
            $dom_obj->item(0)->nodeValue,
            $dom_objs['title']->item(0)->nodeValue,
            [$tag_name,'title'],
            true
          );
          break;
        case 'h2' :
        case 'h3' :
          echo self::h_crawl($dom_obj,$keyword,$tag_name);
          break;
        case 'img' :
          $crawl_report['images'] = self::img_crawl($dom_obj,$keyword);
          break;
        case 'a' :
          $crawl_report['anchors'] = self::anchor_crawl($dom_obj,$keyword);
          break;
        default :
      }
    }
    //page break
    echo '<br/>';
    echo '<hr/>';
    echo '<br/>';
    return $crawl_report;
  }

  public static function cross_crawl($node_value,$keyword,$element_names,$mode) {
    $result = '';
    $pos_a = strpos($node_value,$keyword);
    $pos_b = strpos($keyword,$node_value);
    $test_key = ($mode)? $element_names[1] : 'Structure';
    $reference_key = $element_names[0];
    if ($keyword === $node_value) {
      $result .= $reference_key . ' Matches ' . $test_key;
    } else if ($pos_a > -1) {
      $result .=  $reference_key . ' Contains ' . $test_key;
      $result .=  '--at char: ' . strval($pos_a);
    } else if ($pos_b > -1) {
      $result .=  $reference_key . ' Contains ' . $test_key;
      $result .=  '--at char: ' . strval($pos_b);
    } else {
      $result .=  $reference_key . ' Doesn\'t Contain ' . $test_key;
      $result .=  '--at char: ' . strval($pos_b);
    }
    $result .= '<br/>';
    return $result;
  }

  public static function h_crawl($node_collection,$keyword,$tag_name) {
    $result = $tag_name . ' count: ' . strval(count($node_collection));
    $result .= '<br/>';
    for ($i = 0; $i < count($node_collecion); $i++) {
      $result .= $tag_name . ' ' . strval($i) . ' text: ' . $anchor;
      $result .= '<br/>';
    }
    return $result;
  }

  public static function anchor_crawl($node_collection,$keyword) {
    $a_count = count($node_collection);
    $result = 'anchor count: ' . strval($a_count);
    $result .= '<br/>';
    for ($i = 0; $i < $a_count; $i++) {
      $anchor =  $node_collection->item($i)->nodeValue;
      $href = ( $node_collection->item($i)->attributes->getNamedItem('href')) ?
         $node_collection->item($i)->attributes->getNamedItem('href')->nodeValue : '(href)';
      $result .= 'anchor ' . strval($i) . ' text: ' . $anchor;
      $result .= '<br/>';
      $result .= 'anchor ' . strval($i) . ' href: ' . $href;
      $result .= '<br/>';
    }
    return $result;
  }

  public static function img_crawl($node_collection,$keyword) {
    $img_count = count($node_collection);
    $result = 'img count: ' . strval($img_count);
    $result .= '<br/>';
    for ($i = 0; $i < $img_count; $i++) {
      $src = ( $node_collection->item($i)->attributes->getNamedItem('src')) ?
         $node_collection->item($i)->attributes->getNamedItem('src')->nodeValue : '(src not found)';
      $alt = ( $node_collection->item($i)->attributes->getNamedItem('alt')) ?
         $node_collection->item($i)->attributes->getNamedItem('alt')->nodeValue : '(alt not found)';
      $result .= 'img ' . strval($i) . ' src: ' . $src;
      $result .= '<br/>';
      $result .= 'img ' . strval($i) . ' alt: ' . $alt;
      $result .= '<br/>';
    }
    return $result;
  }

  public static function parse_sitemap_dom($dom) {
    $resource_arr = [];
    $locs = ($dom->getElementsByTagName('loc')) ? $dom->getElementsByTagName('loc') : [];

    error_log('locs ' . strval(count($locs)));

    for($i = 0; $i < count($locs); $i++) {
      if ($locs->item($i)) {

        $this_node_val = $locs->item($i)->nodeValue;

        if (!strpos($this_node_val,'wp-content')) {
          $resource_arr[] = $this_node_val;
          error_log($this_node_val);
        } else {

        }

      }
    }
    return $resource_arr;
  }

  public static function init_curl_crawl($domain,$page_path,$client_data) {
    $crawl_report = [];
    $this_dom = array();
    if (is_array($page_nath)) {
      $resource_arr = $page_path;
    } else {
      $sitemap_dom = ($page_path)?
        self::curl_get_dom($domain . '/' . $page_path) :
        self::curl_get_dom($domain . '/sitemap.xml');
      $resource_arr = self::parse_sitemap_dom($sitemap_dom);
    }

    foreach( $resource_arr as $resource ) {
      $this_dom = self::curl_get_dom($resource);
      $crawl_report[$resource] = self::spider_page_dom($this_dom,$client_data,$resource);
    }
    return $crawl_report;
  }

}
