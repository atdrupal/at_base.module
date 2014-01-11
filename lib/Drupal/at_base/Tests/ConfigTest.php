<?php

namespace Drupal\at_base\Tests;

class ConfigTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Config',
      'description' => 'Make sure the at_config() is working correctly.',
      'group' => 'AT Base',
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_config', 'atest_base', 'atest2_base');
  }

  /**
   * Make sure at_modules() function is working correctly.
   */
  public function testAtModules() {
    $this->assertTrue(in_array('atest_base', at_modules()));
    $this->assertTrue(in_array('atest2_base', at_modules('atest_base')));
  }

  /**
   * Module weight can be updated correctly
   */
  public function testWeight() {
    at_base_flush_caches();

    $query = "SELECT weight FROM {system} WHERE name = :name";
    $weight = db_query($query, array(':name' => 'atest_base'))->fetchColumn();

    $this->assertEqual(10, $weight);
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
}
