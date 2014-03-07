<?php
namespace Drupal\at_base\Helper\Wrapper;

class Database {
  public function select($table, $alias = NULL, array $options = array()) {
    return db_select($table, $alias, $options);
  }

  public function update($table, array $options = array()) {
    return db_update($table, $options);
  }

  public function delete($table, array $options = array()) {
    return db_delete($table, $options);
  }

  public function insert($table, array $options = array()) {
    return db_insert($table, $options);
  }

  public function merge($table, array $options = array()) {
    return db_merge($table, $options);
  }

  public function query($query, array $args = array(), array $options = array()) {
    return db_query($query, $args, $options);
  }
}
