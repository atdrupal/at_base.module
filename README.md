at_base
=======

[![Build Status](https://secure.travis-ci.org/andytruong/at_base.png?branch=7.x-2.x)](http://travis-ci.org/andytruong/at_base)

Base module to provide helper functions for at_* modules.

Install
=====

Requires:

  - https://github.com/mustangostang/spyc.git v0.5.1
  - https://github.com/fabpot/Pimple.git Version v1.1.0

If you install the module with Drush, the libraries will be automatically
downloaded.

Autoload
=====

Support PSR-4 autloading for Drupal 7 modules.

Module that defines dependencies[] = at_base in info file, can autoload the classes:

````
/path/to/module/lib/Class.php -> \Drupal\module_name\Class
/path/to/module/lib/Controller/PageX.php -> \Drupal\module_name\Controller\PageX
````

Useful functions:
=======

1. at_id()
2. at_cache()
3. at_modules()
4. at_debug()
5. at_container()

Config files:
=======

1. ./config/services.yml — Services
2. ./config/at_require.yml — External libraries
