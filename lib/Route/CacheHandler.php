<?php

/**
 * Cache handler for route.
 */

namespace Drupal\at_base\Route;

use Drupal\at_base\Helper\Content_Render\CacheHandlerInterface;

class CacheHandler implements CacheHandlerInterface {

  protected $options;
  protected $callback;

  /**
   * Tell the proxy does not cache this page
   */
  public function __destruct() {
    if (!empty($this->options['id']) && user_is_anonymous()) {
      $GLOBALS['conf']['cache'] = 0;
    }
  }

  public function setOptions($options) {
    $this->options = $options;
    return $this;
  }

  public function setCallback($callback) {
    $this->callback = $callback;
    return $this;
  }

  protected function getCacheId() {
    $o = &$this->options;
    $o['id'] = isset($o['id']) ? $o['id'] : '';

    $cid_parts = array($o['id']);
    $cid_parts = array_merge($cid_parts, drupal_render_cid_parts($o['type']));

    return implode(':', $cid_parts);
  }

  public function render() {
    $cachable = drupal_is_cli() || drupal_page_is_cacheable();

    if (!$cachable) {
      return call_user_func($this->callback);
    }

    $o = &$this->options;

    if (!empty($o['type'])) {
      switch ($o['type']) {
        case DRUPAL_CACHE_CUSTOM:
        case DRUPAL_NO_CACHE:
          return call_user_func($this->callback);

        default:
          $o['id'] = $this->getCacheId();
          break;
      }
    }

    return at_cache($o, $this->callback);
  }

}
