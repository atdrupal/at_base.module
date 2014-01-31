<?php
namespace Drupal\at_base\Helper\Test;

class Database {
  static $log;
  static $last_method = 'unknown_method';
  static $last_table = 'unknown_table';
  static $last_options = array();

  public function __call($name, $arguments) {
    self::$log[self::$last_method][self::$last_table][$name][] = $arguments;
    return $this;
  }

  public function select($table, $alias = NULL, array $options = array()) {
    return $this->methodCall('select', $table, array('alias' => $alias) + $options);
  }

  public function update($table, array $options = array()) {
    return $this->methodCall('update', $table, $options);
  }

  public function delete($table, array $options = array()) {
    return $this->methodCall('delete', $table, $options);
  }

  public function insert($table, array $options = array()) {
    return $this->methodCall('insert', $table, $options);
  }

  /**
   * @param string $method
   */
  private function methodCall($method, $table, $options) {
    self::$last_method = $method;
    self::$last_table = $table;
    self::$last_options = $options;
    return $this;
  }

  public function getLog($method = NULL, $table = NULL) {
    if (!empty($method) && isset(self::$log[$method])) {
      if (!empty($table) && isset(self::$log[$method][$table])) {
        return self::$log[$method][$table];
      }
      return self::$log[$method];
    }

    return self::$log;
  }

  public function resetLog() {
    self::$log = array();
  }
}
