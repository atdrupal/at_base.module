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

Module that defines dependencies[] = at_base in info file, can autoload the
classes:

````
/path/to/module/lib/Class.php -> \Drupal\module_name\Class
/path/to/module/lib/Controller/PageX.php -> \Drupal\module_name\Controller\PageX
````

Config — at_config()
====

Simple API to read config values from YAML files.

If you have a yaml file like this (atest_config depends on at_base)

````yaml
# at_base/tests/atest_config/config/config.yml
foo: bar
````

You can read foo value with this code:
````php
echo at_config('atest_config')->get('foo'); // bar
````

Yaml file can import data from other files:

````yaml
imports:
  - { resource: to_be_imported.yml }
foo: bar
````

Service Container
=======

Dependency help our code clean, testable…

The Service Container in this module is built on Pimple.

If we defined

````yaml
# /at_base/tests/atest_base/config/services.yml
services:
  atest_base.service_1:
    class: 'Drupal\atest_base\Service_1'
  atest_base.service_2:
    class: 'Drupal\atest_base\Service_2'
    arguments: ['@atest_base.service_1']
````

We can get instance of service_2:

````php
$service_2 = at_container('atest_base.service_2');
````

Easy Routing
=======

Faster way to define menu item in Drupal 7:

```yaml
# %atest_route/config/route.yml
routes:
  atest_route/drupal:
    title: Hello Drupal
    page callback: atest_route_page_callback
    page arguments: ['Andy Truong']
    access arguments: ['access content']
  atest_route/controller:
    title: Hello
    access arguments: ['access content']
    controller: [\Drupal\atest_route\Controller\HelloController, helloAction, {name: 'Andy Truong'}]
  atest_route/string_template:
    title: String Template
    access arguments: ['access content']
    template_string: 'Hello {{ name }}'
    variables: {name: Andy Truong}
```

Easy Block
=======

Blocks can be defined in yaml config style:

```yaml
# %module_name/config/blocks.yml
blocks:
  hello_string:
    info: 'Hello String'
    subject: 'Hello String'
    cache: DRUPAL_CACHE_PER_ROLE
    content: 'Hello Andy Truong'
  hello_template:
    info: 'Hello template'
    subject: 'Hello template'
    cache: DRUPAL_CACHE_PER_PAGE
    content:
      template: '@module_name/templates/block/hello_template.html.twig'
      variables: {name: 'Andy Truong'}
  hello_template_string:
    info: 'Hello Template String'
    subject: 'Hello Template String'
    content:
      template_string: "{{ 'slider_front' | drupalView }}"
      attached:
        css: ['%theme/css/slider.css']
        js: ['//cdnjs.cloudflare.com/ajax/libs/jquery.cycle/3.03/jquery.cycle.all.min.js']
```

Useful functions:
=======

1. at_id()
2. at_cache()
3. at_modules()
4. at_debug()

Config files:
=======

1. ./config/services.yml — Services
2. ./config/blocks.yml — Services
3. ./config/routes.yml — Services
4. ./config/require.yml — External libraries
