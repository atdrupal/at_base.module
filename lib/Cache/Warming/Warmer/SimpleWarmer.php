<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

class SimpleWarmer implements Warmer_Interface {
  public function validateTag($tag) {
    return TRUE;
  }

  public function processTag($tag, $context = array()) {
    return $tag;
  }
}
