<?php

namespace Drupal\at_base\Tests\Unit;

use \Drupal\at_base\Helper\Test\UnitTestCase;
use \Drupal\at_base\Helper\ModulesFetcher;

class ConfigTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Config') + parent::getInfo();
  }

  /**
   * Test case for at_config() function.
   */
  public function testConfigGet() {
    // Test getPath(), case #1
    $expected_path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'atest_config') . '/config/config.yml';
    $actual_path = at_config('atest_config')->getPath();
    $this->assertEqual($expected_path, $actual_path);

    // Test getPath(), case #2
    $expected_path = DRUPAL_ROOT . '/' . drupal_get_path('module', 'atest_config') . '/config/to_be_imported.yml';
    $actual_path = at_config('atest_config', '.to_be_imported')->getPath();
    $this->assertEqual($expected_path, $actual_path);

    // Test simple value getting
    $foo = at_config('atest_config')->get('foo');
    $this->assertEqual($foo, 'bar');

    // Test not found exception
    try {
      at_config('atest_config')->get('not_there');
      $this->assertTrue('No exception thrown');
    }
    catch (\Drupal\at_base\Config\NotFoundException $e) {
      $this->assertTrue('Throw NotFoundException if config item is not configured.');
    }

    // Test import data
    $config = at_config('atest_config', '/import_resources');
    $this->assertEqual('imported_data', $config->get('imported_data'));
    $array_data = $config->get('array_data');
    $this->assertEqual('A',   $array_data['a']);
    $this->assertEqual('CCC', $array_data['c']);
  }

  /**
   * Test for service: helper.config_fetcher
   */
  public function testConfigFetcher() {
    $config_fetcher = at_container('helper.config_fetcher');

    // Get all
    $items = $config_fetcher->getItems('at_base', 'services', 'services', TRUE);
    $this->assertTrue(isset($items['twig']));

    // Get specific item
    $item = $config_fetcher->getItem('at_base', 'services', 'services', 'twig', TRUE);
    $this->assertEqual('@twig.core', $item['arguments'][0]);
  }

  /**
   * Module weight can be updated correctly
   */
  public function testWeight() {
    at_container('wrapper.db')->resetLog();
    at_id(new \Drupal\at_base\Hook\Flush_Cache())->resolveModuleWeight('atest_base', 10);
    $db_log = at_container('wrapper.db')->getLog('update', 'system');

    $expected = array(
      'condition' => array('name', 'atest_base'),
      'fields' => array('weight' => 10)
    );

    $this->assertTrue(in_array($expected['condition'], $db_log['condition']));
    $this->assertTrue(in_array($expected['fields'], $db_log['fields'][0]));
  }

  /**
   * Make sure at_modules() function is working correctly.
   */
  public function testAtModules() {
    $enabled_modules = array();

    // Just check with two modules
    foreach (array('at_base', 'atest_base') as $module_name) {
      $enabled_modules[$module_name] = drupal_get_path('module', $module_name) . '/'. $module_name .'.info';
      $enabled_modules[$module_name] = file_get_contents($enabled_modules[$module_name]);
      $enabled_modules[$module_name] = drupal_parse_info_format($enabled_modules[$module_name]);
      $enabled_modules[$module_name] = (object)array(
        'name' => $module_name,
        'stauts' => 1,
        'info' => $enabled_modules[$module_name],
      );
    }

    // Case 1: Do not need other modules has any config file.
    $expected = array('atest_base');
    $actual = at_id(new ModulesFetcher('at_base', $config_file = ''))->fetch($enabled_modules);
    $this->assertEqual($expected, $actual);

    // Case 2: The modules have to have a specific config file
    $expected = array('atest_base');
    $actual = at_id(new ModulesFetcher('at_base', $config_file = 'services'))->fetch($enabled_modules);
    $this->assertEqual($expected, $actual);

    $expected = array();
    $actual = at_id(new ModulesFetcher('at_base', $config_file = 'un_real_config_file'))->fetch($enabled_modules);
    $this->assertEqual($expected, $actual);
  }
}
