<?php

namespace Drupal\at_base;

class KV extends \Drupal\at_base\KV\StorageBase {
  /**
   * @var string
   */
  protected $table;

  /**
   * @var \Drupal\at_base\Helper\Wrapper\Database
   */
  protected $db;

  public function __construct($collection, $table = 'at_kv') {
    $this->collection = $collection;
    $this->table = $table;
    $this->db = at_container('wrapper.db');
  }

  public function getMultiple($keys) {
    $values = $this->db
      ->query(
        'SELECT name, value'
          . '  FROM {'. $this->table .'}'
          . '  WHERE name IN (:keys) AND collection = :collection'
        ,
        array(
          ':keys' => $keys,
          ':collection' => $this->collection,
        ))
      ->fetchAllKeyed();
    return array_map('unserialize', $values);
  }

  public function getAll() {
    $values = $this->db
      ->query(
        'SELECT name, value'
          . ' FROM {' . $this->table . '}'
          . ' WHERE collection = :collection'
        ,
        array(':collection' => $this->collection)
      )->fetchAllKeyed();
    return array_map('unserialize', $values);
  }

  public function set($key, $value) {
    $this->db->merge($this->table)
      ->key(array('name' => $key, 'collection' => $this->collection))
      ->fields(array('value' => serialize($value)))
      ->execute()
    ;
  }

  public function setIfNotExists($key, $value) {
    return \MergeQuery::STATUS_INSERT == $this->db->merge($this->table)
      ->insertFields(array(
        'collection' => $this->collection,
        'name' => $key,
        'value' => serialize($value),))
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute()
    ;
  }

  public function deleteMultiple($keys) {
    do {
      $this->db->delete($this->table)
        ->condition('name', array_splice($keys, 0, 1000))
        ->condition('collection', $this->collection)->execute();
    } while (count($keys));
  }

  public function deleteAll() {
    $this->db->delete($this->table)->condition('collection', $this->collection)->execute();
  }
}
