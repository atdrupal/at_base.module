<?php
namespace Drupal\at_base\Helper\Test;

class Cache {
  static $data;

  /**
   * @param boolean|string $cid
   */
  public function get($cid, $bin = 'cache') {
    if (isset(self::$data[$bin][$cid])) {
      return self::$data[$bin][$cid];
    }

    return FALSE;
  }

  public function set($cid, $data, $bin = 'cache', $expire = \CACHE_PERMANENT) {
    self::$data[$bin][$cid] = (object)array(
      'cid' => $cid,
      'data' => $data,
      'created' => time(),
      'expire' => $expire,
      'serialized' => !is_string($data) && !is_numeric($data),
    );
  }

  public function clearAll($cid = NULL, $bin = NULL, $wildcard = FALSE) {
    if ($wildcard) {
      unset(self::$data[$bin]);
    }
    else {
      unset(self::$data[$bin][$cid]);
    }
  }
}
