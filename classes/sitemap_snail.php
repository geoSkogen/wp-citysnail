<?php

class Sitemap_Snail {

  public $sitemap_monster;

  function __construct($obj) {
    $this->sitemap_monster = $obj;
  }

  public function get_slug_arr($url) {
    $uri = str_replace($this->sitemap_monster->domain,'',$url);
    $slug_arr = explode('/',$uri);
    if (substr($uri,0,1) == '/' && substr($uri,-1) =='/') {
      array_splice($slug_arr,0,1);
      array_splice($slug_arr,-1,1);
    }
    return $slug_arr;
  }

  public function class_name_manager($arr, $item_type) {
    $class_slugs = '';
    $class_type = ($item_type) ? 'sitemap_item ' : 'toggle_icon ';
    for ($i = 0; $i < count($arr); $i++) {
      $class_slugs .= $arr[$i];
      $class_slugs .= ' ';
    }
    return $class_type . $class_slugs . 'tier_' . strval(count($arr));
  }

  public function do_sitemap_item($url) {
    $slug_arr = $this->get_slug_arr($url);
    $has_children = count(
      $this->sitemap_monster->is_parent_dir_of(
        $slug_arr[0],$slug_arr[count($slug_arr)-1],count($slug_arr)
      )
    );
    $display_toggle = (count($slug_arr) > 1) ? 'block' : 'notoggle';
    $str = '';
    $str .= '<div data_toggle="' . $display_toggle . '" class="' .
      $this->class_name_manager($slug_arr, true) . '">';
    $str .= '<div class="flexOuterStart">';
    $str .= ($has_children) ?
      '<div class="' . $this->class_name_manager($slug_arr, false) . '">
        <i class="fas fa-caret-right" data_toggle="down" style="font-size:28px;"></i>
       </div>' : '';
    $str .= '<a href="' . $url . '" class="sitemap_link">';
    $str .= $url;
    $str .= '</a>';
    $str .= '<input id="' .
      implode('_',$slug_arr) . '" class="invis" name="' .
      implode('_',$slug_arr) . '"/>';
    $str .= '</div></div>';
    return $str;
  }
}
