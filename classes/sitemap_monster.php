<?php

class Sitemap_Monster {

  public $domain = '';
  public $old_map = [];
  public $old_page_arrs = [];
  public $branches = [];
  public $new_page_arrs = [];
  public $new_map = [];
  public $csv_str = '';
  public $html_table = '';

  function __construct($this_domain_str,$these_urls_arr) {
    $this->domain = $this_domain_str;
    $this->old_map = $these_urls_arr;
    $this->old_page_arrs = $this->get_path_arrs();
    $this->branches = $this->get_branches();
    $this->get_nested_page_arr();
    $this->new_map = $this->urls_from_arrays();
    $this->csv_str = $this->get_csv_nest();
  //  $this->html_table = $this->get_html_table();
  }

  public function get_path_arrs() {
    $uris = [];
    $slug_arr = [];
    $dirs = [];
    foreach($this->old_map as $url) {
      $slug_arr = explode( '/', str_replace($this->domain,'',$url) );
      array_splice($slug_arr,0,1);
      array_splice($slug_arr,-1,1);

      $uris[] = $slug_arr;

    }
    return $uris;
  }

  public function get_branches() {
    $branches = [];
    foreach ($this->old_page_arrs as $slug_arr) {
      if (count($slug_arr)) {
        if (isset($branches[count($slug_arr)])) {
          $branches[count($slug_arr)][] = $slug_arr;
        } else {
          $branches[count($slug_arr)] = [$slug_arr];
        }
      }
    }
    $branches[0] = '/';
    return $branches;
  }

  public function is_parent_dir_of($root,$slug,$slug_tier) {
    $result = [];
    if (isset($this->branches[$slug_tier+1])) {
      foreach ($this->branches[$slug_tier+1] as $uri) {
        if ($uri[$slug_tier-1] === $slug && $uri[0] === $root) {
          $result[] = $uri;
        }
      }
    }
    return $result;
  }

  function parent_find_child($this_root,$this_slug,$this_tier) {
    $child_dirs = $this->is_parent_dir_of($this_root,$this_slug,$this_tier);
    if (count($child_dirs)) {
      foreach($child_dirs as $child_dir) {
        $this->new_page_arrs[] = $child_dir;
        if (isset($this->branches[$this_tier+1])) {
          $this->parent_find_child($this_root,$child_dir[$this_tier],$this_tier+1);
        }
      }
    }
    //return;
  }

  public function get_nested_page_arr() {
    $roots = $this->branches[1];
    foreach($roots as $root) {
      $root_slug = $root[0];
      $this_slug = $root_slug;
      $this->new_page_arrs[] = $root;
      $this->parent_find_child($root_slug,$this_slug,1);
    }
    return $this->new_page_arrs;
  }

  public function urls_from_arrays() {
    $arr = [$this->domain . '/'];
    foreach ($this->new_page_arrs as $url_arr) {
      $arr[] = $this->domain . '/' . join('/', $url_arr) . '/';
    }
    return $arr;
  }

  public function get_csv_nest() {
    $nest_index = -1;
    $slug = '';
    $line = '';
    $csv_str = $this->get_nested_csv_line(0,'/',count($this->branches));
    foreach($this->new_page_arrs as $slug_arr) {
      $nest_index = count($slug_arr)-1;
      $slug =  '/' . $slug_arr[$nest_index] . '/';
      $line = $this->get_nested_csv_line($nest_index,$slug,count($this->branches));
      $csv_str .= $line;
    }
    return $csv_str;
  }

  public function get_html_table($data_table) {
    $nest_index = -1;
    $slug = '';
    $line = '';
    $html_str = '<table>';
    $html_str .= $this->get_html_table_row(
      0,'/',count($this->branches),$data_table,[]
    );
    foreach($this->new_page_arrs as $slug_arr) {
      $nest_index = count($slug_arr)-1;
      $slug =  '/' . $slug_arr[$nest_index] . '/';
      $line = $this->get_html_table_row(
        $nest_index,$slug,count($this->branches),$data_table,$slug_arr
      );
      $html_str .= $line;
    }
    $html_str .= '</table>';
    return $html_str;
  }

  public function repeat_me($str,$int) {
    $result = "";
    for ($i = 0; $i < $int; $i++) {
      $result .= $str;
    }
    return $result;
  }

  public function get_nested_csv_line($depth,$arg,$range) {
    $str = $this->repeat_me(',',$depth);
    $str .= $arg;
    $str .= $this->repeat_me(',', ($range-$depth-1) );
    $str .= "\r\n";
    return $str;
  }

  public function get_html_table_row($depth,$arg,$range,$options,$slug_arr) {
    $slug = ($arg === '/') ? 'homepage' : $arg;
    $url = $this->domain . '/';
    $url .= ($arg === '/') ? '' : join('/', $slug_arr) . '/';
    $field_val = (isset($options[$url])) ? $options[$url] : '';
    $str = '<tr class="monster_row"><td class="short_cell drop_me">&times</td>';
    $str .= $this->repeat_me('<td></td>',$depth);
    $str .= '<td class="monster_slug">' . $slug . '</td>';
    $str .= $this->repeat_me('<td></td>', ($range-$depth-1) );
    $str .= '<td class="monster_key invis" data-toggle="block,pause,invis">';
    $str .= '<input id="' . $url . '" class="citysnail zeroTest monster_field"
      type="text" data="none" name="wp_citysnail_structure[' . $url . ']"
      value="' . $field_val . '"/>';
    $str .= '</td>';
    $str .= '</tr>';
    return $str;
  }
}

?>
