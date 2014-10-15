<?php

namespace Drupal\at_base\KV;

/**
 * Callback for kv.expirable service.
 *
 * Mostly copied from Drupal\Core\KeyValueStore\DatabaseStorageExpirable
 */
class Expirable extends \Drupal\at_base\KV {

  public function __construct($collection, $table = 'at_kv_expire') {
    parent::__construct($collection, $table);
  }

  public function getMultiple($keys) {
    $values = $this->db
      ->query(
        'SELECT name, value'
          . '  FROM {'. $this->table .'}'
          . '  WHERE expire > :now AND name IN (:keys) AND collection = :collection'
        ,
        array(
          ':now' => \at_fn::time(),
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
          . ' WHERE collection = :collection AND expire > :now'
        ,
        array(
          ':collection' => $this->collection,
          ':now' => \at_fn::time(),
        )
      )->fetchAllKeyed();
    return array_map('unserialize', $values);
  }

  public function setWithExpire($key, $value, $expire) {
    $this->db->merge($this->table)
      ->key(array('name' => $key, 'collection' => $this->collection))
      ->fields(array('value' => serialize($value), 'expire' => \at_fn::time() + $expire))
      ->execute()
    ;
  }

  public function setWithExpireIfNotExists($key, $value, $expire) {
    return \MergeQuery::STATUS_INSERT == $this->db->merge($this->table)
      ->insertFields(array(
        'collection' => $this->collection,
        'name' => $key,
        'value' => serialize($value),
        'expire' => \at_fn::time() + $expire,
      ))
      ->condition('collection', $this->collection)
      ->condition('name', $key)
      ->execute();
  }

  public function setMultipleWithExpire(array $data, $expire) {
    foreach ($data as $key => $value) {
      $this->setWithExpire($key, $value, $expire);
    }
  }

}
