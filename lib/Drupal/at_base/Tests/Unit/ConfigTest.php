<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class ConfigTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Config') + parent::getInfo();
  }

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
      $not_there = at_config('atest_config')->get('not_there');
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

    $expected['condition'] = array('name', 'atest_base');
    $expected['fields'] = array('weight' => 10);

    $this->assertTrue(in_array($expected['condition'], $db_log['condition']));
    $this->assertTrue(in_array($expected['fields'], $db_log['fields'][0]));
  }
}
