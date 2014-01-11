<?php
namespace Drupal\at_base;

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
}
