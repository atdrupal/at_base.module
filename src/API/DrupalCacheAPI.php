<?php

namespace Drupal\at_base\Helper\Wrapper;

class DrupalCacheAPI
{

    public function get($cid, $bin = 'cache')
    {
        return cache_get($cid, $bin);
    }

    public function set($cid, $data, $bin = 'cache', $expire = \CACHE_PERMANENT)
    {
        return cache_set($cid, $data, $bin, $expire);
    }

    public function clearAll($cid = NULL, $bin = NULL, $wildcard = FALSE)
    {
        cache_clear_all($cid, $bin, $wildcard);
    }

}
