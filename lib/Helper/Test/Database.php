<?php
namespace Drupal\at_base\Helper\Test;

class Database {
  public function __call(string $name , array $arguments) {
    return $this;
  }

  public function select($table, $alias = NULL, array $options = array()) {
    return $this; # db_select($table, $alias, $options);
  }

  public function update($table, array $options = array()) {
    return $this; // db_update($table, $options);
  }

  public function delete($table, array $options = array()) {
    return $this; # db_delete($table, $options);
  }

  public function insert($table, array $options = array()) {
    return $this; # db_insert($table, $options);
  }
}
