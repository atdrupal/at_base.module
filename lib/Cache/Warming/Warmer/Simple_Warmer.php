<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

class Simple_Warmer {
  public function validateTag($tag) {
    return TRUE;
  }

  public function warm($tag, $context = array()) {
    at_cache_flush_by_tags(array($tag));
  }
}
