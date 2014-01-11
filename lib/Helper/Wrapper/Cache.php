<?php
namespace Drupal\at_base\Helper\Wrapper;

class Cache {
  public function get($cid, $bin = 'cache') {
    return cache_get($cid, $bin);
  }

  public function set($cid, $data, $bin = 'cache', $expire = \CACHE_PERMANENT) {
    return cache_set($cid, $data, $bin, $expire);
  }
}
