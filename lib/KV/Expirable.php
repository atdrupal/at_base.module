<?php

namespace Drupal\at_base\KV;

class Expirable extends \Drupal\at_base\KV {
  public function __construct($collection, $table = 'at_kv_expire') {
    parent::__construct($collection, $connection, $table);
  }

  public function getMultiple() {
    $values = $this->db
      ->query(
        'SELECT name, value'
          . '  FROM {'. $this->table .'}'
          . '  WHERE expire > :now AND name IN (:keys) AND collection = :collection'
        ,
        array(
          ':now' => REQUEST_TIME,
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
          ':now' => REQUEST_TIME,
        )
      )->fetchAllKeyed();
    return array_map('unserialize', $values);
  }

  public function setWithExpire() {
    $this->db->merge($this->table)
      ->keys(array('name' => $key, 'collection' => $this->collection))
      ->fields(array('value' => serialize($value), 'expire' => REQUEST_TIME + $expire))
      ->execute()
    ;
  }

  public function setWithExpireIfNotExists() {
    return \MergeQuery::STATUS_INSERT == $this->db->merge($this->table)
      ->insertFields(array(
        'collection' => $this->collection,
        'name' => $key,
        'value' => serialize($value),
        'expire' => REQUEST_TIME + $expire,
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
