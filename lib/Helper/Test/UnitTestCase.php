<?php

namespace Drupal\at_base\Helper\Test;

require_once dirname(__FILE__) . '/Cache.php';
require_once dirname(__FILE__) . '/Database.php';

define('AT_BASE_TESTING_UNIT', TRUE);

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
    // Fake DB, cache services
    \at_fake::at_modules(function($module, $config_file) {
      if ($module = 'at_base') {
        switch ($config_file) {
          case NULL:
          case '':
          case 'services':
          case 'twig_functions': return array('at_base', 'atest_base');
          case 'twig_filters':   return array('at_base');
          case 'breadcrumb': return array('atest_base');
        }
      }
    });

    at_container()->getDefinition('wrapper.db')->setClass('Drupal\at_base\Helper\Test\Database');
    at_container()->getDefinition('wrapper.cache')->setClass('Drupal\at_base\Helper\Test\Cache');

    $this->setUpModules();

    parent::setUp('at_base', 'atest_base');
  }

  protected function setUpModules() {
    $this->container = at_container();
    spl_autoload_unregister('drupal_autoload_class');
    spl_autoload_unregister('drupal_autoload_interface');
  }
}
