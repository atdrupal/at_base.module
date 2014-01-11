<?php
namespace Drupal\at_base;

class Cache {
  /**
   * Cache bin
   * @var string
   */
  private $bin = 'cache';

  /**
   * Cache ID
   * @var string
   */
  private $id;

  /**
   * Time to live.
   *
   * @var string
   */
  private $ttl;

  /**
   * Rebuild data if cached data is empty/false/null.
   *
   * @var boolean
   */
  private $allow_empty;

  /**
   * Flag to rebuild cache data by pass.
   *
   * @var boolean
   */
  private $reset;

  /**
   * Attached tags.
   *
   * @var array
   */
  private $tags;

  /**
   * Callable string or closure.
   *
   * @var mixed
   */
  private $callback;

  /**
   * Callback arguments.
   *
   * @var array
   */
  private $arguments;

  public function __construct($options, $callback, $arguments = array()) {
    $_keys = array(
      'bin' => 'cache',
      'id' => '',
      'ttl' => '+ 15 minutes',
      'reset' => FALSE,
      'tags' => array());

    foreach ($_keys as $k => $v) {
      $this->{$k} = isset($options[$k]) ? $options[$k] : $v;
    }

    $this->callback = $callback;
    $this->arguments = $arguments;

    // No cache_id, can not fetch, can not write, this function is useless.
    if (empty($this->id) || !is_string($this->id)) {
      throw new \InvalidArgumentException('Please provide a valid cache ID');
    }

    // Allow dev to force rebuilding all caches on page
    if (defined('AT_DEV') && !empty($_GET['nocache'])) {
      $this->reset = TRUE;
    }
  }

  /**
   * Fetch the cached data
   *
   * @return  mixed
   */
  public function get() {
    if (!$this->reset && $cache = cache_get($this->id, $this->bin)) {
      if (!empty($cache->data)) return $cache->data;
      if ($this->allow_empty)   return $cache->data;
    }

    return $this->fetch();
  }

  /**
   * Fetch data.
   *
   * @return mixed
   */
  public function fetch() {
    if (!is_callable($this->callback)) {
      throw new \InvalidArgumentException('Invalid callback: ' . print_r($this->callback, TRUE));
    }

    $this->write($return = call_user_func_array($this->callback, $this->arguments));

    return $return;
  }

  /**
   * Write data to cache bin.
   *
   * @param  mixed $data
   */
  protected function write($data) {
    if (FALSE !== cache_set($this->id, $data, $this->bin, strtotime($this->ttl))) {
      if (!empty($this->tags)) {
        $this->removeAllTags();
        foreach ($this->tags as $tag) {
          $this->addTag($tag);
        }
      }
    }
  }

  /**
   * Add tag to a cache item.
   *
   * @param string $tag
   * @see   at_base_flush_caches()
   */
  public function addTag($tag) {
    return at_container('db')->insert('at_base_cache_tag')
      ->fields(array(
          'bin' => $this->bin,
          'cid' => $this->id,
          'tag' => $tag,
      ))
      ->execute()
    ;
  }

  public function removeAllTags() {
    return at_container('db')->delete('at_base_cache_tag')
      ->condition('bin', $this->bin)
      ->condition('cid', $this->id)
      ->execute()
    ;
  }

  /**
   * Remove a tag from a cache item.
   *
   * @param  string $tag
   */
  public function removeTag($tag) {
    return at_container('db')->delete('at_base_cache_tag')
      ->condition('bin', $this->bin)
      ->condition('cid', $this->id)
      ->condition('tag', $tag)
      ->execute()
    ;
  }
}
