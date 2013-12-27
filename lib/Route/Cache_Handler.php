<?php
/**
 * Cache handler for route.
 */
namespace Drupal\at_base\Route;

use Drupal\at_base\Helper\Content_Render\CacheHandler_Interface;

class Cache_Handler implements CacheHandler_Interface {
  protected $options;
  protected $callback;

  public function setOptions($options) {
    $this->options = $options;
    return $this;
  }

  public function setCallback($callback) {
    $this->callback = $callback;
    return $this;
  }

  protected function getCacheId() {
    $o       = &$this->options;
    $o['id'] = isset($o['id']) ? $o['id'] : '';

    $cid_parts[] = $o['id'];
    $cid_parts = array_merge($cid_parts, drupal_render_cid_parts($o['type']));

    return implode(':', $cid_parts);
  }

  public function render() {
    $cacheable = !count(module_implements('node_grants')) && ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'HEAD');
    if (!$cacheable) {
      return $this->getEngine()->render();
    }

    $o = &$this->options;

    if (!empty($o['type'])) {
      switch ($o['type']) {
        case DRUPAL_CACHE_CUSTOM:
        case DRUPAL_NO_CACHE:
          return $this->getEngine()->render();

        default:
          $o['id'] = $this->getCacheId();
          break;
      }
    }

    // Tell the proxy does not cache this page
    if (user_is_anonymous()) {
      $GLOBALS['conf']['cache'] = 0;
    }

    return at_cache($o, $this->callback);
  }
}
