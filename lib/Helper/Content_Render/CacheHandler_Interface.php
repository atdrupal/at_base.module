<?php
namespace Drupal\at_base\Helper\Content_Render;

/**
 * Interface of caching handler for Conent_Render service.
 *
 * @see Drupal\at_base\Helper\Content_Render::render()
 */
interface CacheHandler_Interface {
  /**
   * Cache options
   *
   * @see  at_cache()
   * @return CacheHandler_Interface
   */
  public function setOptions($options);

  /**
   * @param callable $callback
   * @return CacheHandler_Interface
   */
  public function setCallback($callback);

  /**
   * Render content.
   */
  public function render();
}
