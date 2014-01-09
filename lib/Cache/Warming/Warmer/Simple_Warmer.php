<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

class Simple_Warmer {
  private $tag_flusher;

  public function __construct($tag_flusher) {
    $this->tag_flusher = $tag_flusher;
  }

  public function validateTag($tag) {
    return TRUE;
  }

  public function warm($tag, $context = array()) {
    $this->tag_flusher
      ->addTag($tag)
      ->flush();
  }
}
