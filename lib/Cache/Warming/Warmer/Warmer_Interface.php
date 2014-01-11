<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

/**
 * Interface for cache-warmer.
 *
 *
 */
interface Warmer_Interface {
  /**
   * Check if the cache should warm a specific tag.
   *
   * @param  string $tag
   * @return boolean
   */
  public function validateTag($tag);

  /**
   * Logic to flush cached-items which tagged with $tag.
   *
   * @param  string $tag
   */
  public function processTag($tag, $context = array());
}
