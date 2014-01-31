<?php

namespace Drupal\at_base\Helper\Test;

use Drupal\at_base\Autoloader;

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
    unset($this->container['wrapper.db']);
    unset($this->container['wrapper.cache']);

    $this->container['wrapper.db'] = function() { return new \Drupal\at_base\Helper\Test\Database(); };
    $this->container['wrapper.cache'] = function() { return new \Drupal\at_base\Helper\Test\Cache(); };

    // Make our autoloader run first — drush_print_r(spl_autoload_functions());
    spl_autoload_unregister('drupal_autoload_class');
    spl_autoload_unregister('drupal_autoload_interface');
    at_id(new Autoloader('Drupal'))->register(FALSE, TRUE);

    // at_modules() > system_list() > need db, fake it!
    // 'id' => "ATConfig:{$module}:{$id}:{$key}:" . ($include_at_base ? 1 : 0),
    $cids_1 = array('atmodules:at_base:', 'atmodules:at_base:services', 'atmodules:at_base:twig_functions');
    $data_1 = array('at_base', 'atest_base');
    foreach ($cids_1 as $cid) {
      at_container('wrapper.cache')->set($cid, $data_1, 'cache_bootstrap');
    }

    $cids_2 = array('atmodules:at_base:twig_filters');
    $data_2 = array('at_base');
    foreach ($cids_2 as $cid) {
      at_container('wrapper.cache')->set($cid, $data_2, 'cache_bootstrap');
    }

    parent::setUp('at_base', 'atest_base');
  }
}
