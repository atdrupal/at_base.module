at_base
=======

[![Build Status](https://secure.travis-ci.org/andytruong/at_base.png?branch=7.x-1.x)](http://travis-ci.org/andytruong/at_base)

Base module to provide helper functions for at_* modules.


Useful functions:
=======

1. at_id()

  …

2. at_cache()

  Without at_cache()

    function your_data_provider($reset = FALSE) {
      $cache_id = '…';
      $bin = 'bin';
      $expire = strtotime('+ 15 minutes');

      if (!$reset && $cache = cache_get($cache_id, $bin)) {
        return $cache->data;
      }

      $data = your_logic();

      cache_set($data, $cache_id, $bin, $expire);

      return $data;
    }

  With at_cache(), your logic becomes cleaner:

    function your_data_provider() {
      return your_logic();
    }

    $data = at_cache(array('cache_id' => '…'), 'your_data_provider');

3. at_modules()

  …
