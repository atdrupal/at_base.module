<?php

namespace Drupal\at_base\Cache\Warming;

/**
 * Class for service cache.tag_flusher
 *
 * Delete cached data by tags.
 */
class Tag_Flusher
{
  private $tag = array();

  public function resetTags()
  {
    $this->tags = array();
    return $this;
  }

  public function setTags($tags)
  {
    $this->tags = $tags;
    return $this;
  }

  public function addTag($tag)
  {
    if (!in_array($tag, $this->tags)) {
      $this->tags[] = $tag;
    }
    return $this;
  }

  public function flush()
  {
    if (empty($this->tags)) {
      return;
    }

    $items = at_container('wrapper.db')->select('at_base_cache_tag', 'atag')
              ->fields('atag', array('bin', 'cid'))
              ->condition('tag', $this->tags)
              ->execute()
              ->fetchAll();

    foreach ($items as $item) {
      at_container('wrapper.cache')->clearAll($item->cid, $item->bin);
    }

    at_container('wrapper.db')->delete('at_base_cache_tag')
      ->condition('tag', $this->tags)
      ->execute()
    ;
  }
}
