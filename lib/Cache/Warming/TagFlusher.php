<?php

namespace Drupal\at_base\Cache\Warming;

/**
 * Class for service cache.tag_flusher
 *
 * Delete cached data by tags.
 *
 *  at_container('cache.tag_flusher')->flush($tags);
 */
class TagFlusher {

  protected $db;
  protected $cache;
  protected $tags = array();

  public function __construct($db, $cache) {
    $this->db = $db;
    $this->cache = $cache;
  }

  public function resetTags() {
    $this->tags = array();
    return $this;
  }

  public function setTags($tags) {
    $this->tags = $tags;
    return $this;
  }

  public function addTag($tag) {
    if (!in_array($tag, $this->tags)) {
      $this->tags[] = $tag;
    }
    return $this;
  }

  public function flush($tags = array()) {
    if (!empty($tags)) {
      $this->setTags($tags);
    }

    if (!empty($this->tags)) {
      $this->clearCachedItems();
      $this->clearTags();
    }
  }

  /**
   * Clear cached items which were tagged.
   */
  protected function clearCachedItems() {
    $items = $this->db->select('at_base_cache_tag', 'atag')
      ->fields('atag', array('bin', 'cid'))
      ->condition('tag', $this->tags)
      ->execute()
      ->fetchAll();

    foreach ($items as $item) {
      $this->cache->clearAll($item->cid, $item->bin);
    }
  }

  /**
   * Clear saved pairs of (tag, cache_id).
   */
  protected function clearTags() {
    $this->db->delete('at_base_cache_tag')
      ->condition('tag', $this->tags)
      ->execute()
    ;
  }

}
