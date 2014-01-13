<?php

namespace Drupal\at_base\Helper\Test;

require_once dirname(__FILE__) . '/Cache.php';
require_once dirname(__FILE__) . '/Database.php';

/**
 * cache_get()/cache_set() does not work on unit test cases.
 */
abstract class UnitTestCase extends \DrupalUnitTestCase {
  protected $container;

  public function getInfo() {
    return array(
      'name' => 'AT Unit',
      'description' => 'Make sure the at_cache() is working correctly.',
      'group' => 'AT Unit'
    );
  }

  public function setUp() {
    $this->container = at_container('container');

    // Mock db, cache
    $this->container->register('wrapper.db',    function() { return new \Drupal\at_base\Helper\Test\Database(); });
    $this->container->register('wrapper.cache', function() { return new \Drupal\at_base\Helper\Test\Cache(); });

    // Make our autoloader run first — drush_print_r(spl_autoload_functions());
    spl_autoload_unregister('drupal_autoload_class');
    spl_autoload_unregister('drupal_autoload_interface');
    at_id(new \Drupal\at_base\Autoloader())->register(FALSE, TRUE);

    // at_modules() > system_list() > need db, fake it!
    $cids[] = "at_modules:at_base:";
    $cids[] = "at_modules:at_base:services";
    $cids[] = "at_modules:at_base:twig_filters";
    $cids[] = "at_modules:at_base:twig_functions";
    $data = array('at_base', 'atest_base');
    foreach ($cids as $cid) {
      at_container('wrapper.cache')->set($cid, $data, 'cache_bootstrap');
    }

    parent::setUp('at_base', 'atest_base');
  }
}