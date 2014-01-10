<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

class Simple_Warmer implements Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return TRUE;
  }

  /**
   * @inheritdoc
   */
  public function processTag($tag, $context = array()) {
    return $tag;
  }
}
