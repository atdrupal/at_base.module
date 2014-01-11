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
      'group' => 'AT Base'
    );
  }

  public function setUp() {
    $this->container = at_container('container');

    // Mock db, cache
    $this->container->register('wrapper.db',    function() { return new \Drupal\at_base\Helper\Test\Database(); });
    $this->container->register('wrapper.cache', function() { return new \Drupal\at_base\Helper\Test\Cache(); });

    parent::setUp('at_base');
  }
}
