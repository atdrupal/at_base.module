<?php

namespace Drupal\at_base\KV;

abstract class StorageBase {
  /**
   * @var string
   */
  protected $collection;

  /**
   * Set collection name.
   *
   * @param string $collection
   */
  public function setCollection($collection) {
    $this->collection = $collection;
  }

  /**
   * Get collection name.
   *
   * @return string
   */
  public function getCollection() {
    return $this->collection;
  }

  public function getCollectionName() {
    return $this->collection;
  }

  abstract public function getMultiple($keys);

  public function get($key, $default = NULL) {
    $values = $this->getMultiple(array($key));
    return isset($values[$key]) ? $values[$key] : $default;
  }

  public function setMultiple(array $data) {
    foreach ($data as $key => $value) {
      $this->set($key, $value);
    }
  }

  public function delete($key) {
    $this->deleteMultiple(array($key));
  }
}
