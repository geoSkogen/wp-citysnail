<?php

class Snail_Query {

  public $table_name;
  public $seo_report;

  public $attributes = [
    'domain',
    'sitemap',
    'structure_file',
    'structure_path',
    'structure_schema',
    'seo_report'
  ];

  function __construct($table_name) {
    $this->table_name = $table_name;
  }

  public function create_client() {

  }

  public function read_client() {

  }

  public function update_client() {

  }

  public function destroy_client() {

  }

}

?>
