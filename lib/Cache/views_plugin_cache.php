<?php
namespace Drupal\at_base\Cache;

class views_plugin_cache extends \views_plugin_cache {
  public function __construct($view, $display) {
    $this->view = $view;
    $this->display = $display;
    $this->set_default_options();
  }
}
