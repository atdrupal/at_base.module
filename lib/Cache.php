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
   * [$ttl description]
   * @var [type]
   */
  private $ttl;

  /**
   * Flag to rebuild cache data by pass.
   *
   * @var boolean
   */
  private $reset;

  /**
   * Rebuild data if cached data is empty/false/null.
   *
   * @var boolean
   */
  private $allow_empty;

  /**
   * Attached tags.
   *
   * @var array
   */
  private $tags;

  /**
   * Callable string or closure.
   * @var mixed
   */
  private $callback;

  /**
   * Callback arguments.
   * @var array
   */
  private $arguments;

  public function __construct($options, $callback, $arguments = array()) {
    $_keys = array(
      'bin' => 'cache',
      'id' => '',
      'ttl' => '+ 15 minutes',
      'reset' => FALSE,
      'allow_empty' => TRUE,
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
    if (!$this->reset) {
      if ($cache = cache_get($this->id, $this->bin)) {
        if (!empty($cache->data) {
          return $cache->data;
        }
        elseif ($this->allow_empty) {
          return $cache->data;
        }
      }
    }
    return $this->fetch();
  }

  /**
   * Fetch data.
   *
   * @return mixed
   */
  public function fetch() {
    if (is_callable($this->callback) || is_a($this->callback, 'Closure')) {
      $return = call_user_func_array($this->callback, $this->arguments);
    }
    else {
      throw new \InvalidArgumentException('Invalid callback: ' . print_r($this->callback, TRUE));
    }

    $this->write($return);

    return $return;
  }

  /**
   * Write data to cache bin.
   *
   * @param  mixed $data
   */
  protected function write($data) {
    if (FALSE !== cache_set($this->id, $data, $this->bin, strtotime($this->ttl))) {
      $this->removeAllTags();
      if ($this->tags) {
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
    db_query(
      "INSERT INTO {at_base_cache_tag} (bin, cid, tag) VALUES ('%s', '%s', '%s')",
      $this->bin, $this->id, $tag
    );
  }

  public function removeAllTags() {
    $sql = "DELETE FROM {at_base_cache_tag} WHERE bin = '%s' AND cid = '%s'";
    db_query($sql, $this->bin, $this->id);
  }

  /**
   * Remove a tag from a cache item.
   *
   * @param  string $tag
   */
  public function removeTag($tag) {
    $sql = "DELETE FROM {at_base_cache_tag} WHERE bin = '%s' AND cid = '%s' AND tag = '%s'";
    db_query($sql, $this->bin, $this->id, $tag);
  }
}
