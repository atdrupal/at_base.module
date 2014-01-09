<?php

namespace Drupal\at_base\Cache\Warming;

/**
 * Class for service cache.tag_flusher
 *
 * Delete cached data by tags.
 */
class Tag_Flusher {
  private $tag = array();

  public function setTags($tags) {
    $this->tags = $tags;
    return $this;
  }

  public function addTag($tag) {
    $this->tags[] = $tags;
    return $this;
  }

  public function flush() {
    if (empty($this->tags)) {
      return;
    }

    $items = db_select('at_base_cache_tag', 'atag')
              ->fields('atag', array('bin', 'cid'))
              ->condition('tag', $this->tags)
              ->execute()
              ->fetchAll();

    foreach ($items as $item) {
      cache_clear_all($item->cid, $item->bin);
    }

    db_delete('at_base_cache_tag')
      ->condition('tag', $this->tags)
      ->execute()
    ;
  }
}
