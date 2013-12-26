<?php

namespace Drupal\at_base\Route;

class Controller {
  private $route;

  public function setRoute($route) {
    $this->route = $route;
    return $this;
  }

  public function execute() {
    $item = menu_get_item($_GET['q']);
    $path = explode('/', $this->route['pattern']);
    foreach ($path as $i => $part) {
      if (strpos($part, '%') === 0) {
        $part = substr($part, 1);
        $this->route['variables'][$part] = $item['map'][$i];
      }
    }

    // Prepair the cache ID
    if (!empty($this->route['cache']) && empty($this->route['cache']['id'])) {
      $this->route['cache']['id'] = 'atroute:' . $item['tab_root_href'];
    }

    return at_container('helper.content_render')->setData($this->route)->render();
  }
}
