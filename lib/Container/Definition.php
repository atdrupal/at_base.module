<?php
namespace Drupal\at_base\Container;

class Definition {
  private $service_name;

  public function __construct($service_name) {
    $this->service_name = $service_name;
  }

  public function get() {
    $service_name = $this->service_name;
    $options = array('ttl' => '+ 1 year', 'cache_id' => "at_base:services:{$service_name}");

    return at_cache($options, function() use ($service_name) {
      $services = Definition::getAll();
      return isset($services[$service_name]) ? $services[$service_name] : FALSE;
    });
  }

  public static function getAll() {
    $options = array('ttl' => '+ 1 year', 'cache_id' => 'at_base:services');
    return at_cache($options, function() {
      $services = array();
      foreach (array('at_base' => 'at_base') + at_modules('at_base', 'services') as $module_name) {
        $services += at_config($module_name, 'services')->get('services');
      }
      return $services;
    });
  }

  /**
   * Returns service's definitions for a given tag.
   *
   * @param array $tags
   *   List of tags to find service's definitions by.
   * @param string $operator
   *   Supported operators are 'and' and 'or'.
   *
   * @return array
   *   An array of tagged service's definitions.
   *
   * @api
   */
  public static function findByTags($tags = array(), $operator = 'and') {
    if (!is_array($tags) || !in_array($operator, array('and', 'or'))) {
      return array();
    }

    // Remove empty tags.
    $tags = array_filter($tags);
    // Sort by alphabet.
    sort($tags);

    // If the tags is empty, we return all service's definitions.
    if (empty($tags)) {
      return Definition::getAll();
    }

    // The tags are sorted, so hash value is the same if the tags are shuffled.
    $hash_tags = md5(implode('', $tags));
    $options = array('ttl' => '+ 1 year', 'cache_id' => 'at_base:tagged_services:{$hash_tags}{$operator}');

    return at_cache($options, function() use ($tags, $operator) {
      $services = array();
      $all_services = Definition::getAll();
      foreach ($all_services as $service_name => $service) {
        if (!isset($service['tags'])) {
          continue;
        }
        $intersect_tags = array_intersect($tags, $service['tags']);
        if (($operator == 'and' && $intersect_tags == $tags) ||
          ($operator == 'or' && !empty($intersect_tags))) {
          $services[$service_name] = $service;
        }
      }

      return $services;
    });
  }

  /**
   * Returns all tags the defined services use.
   *
   * @return array
   *   An array of tags
   */
  public static function findTags() {
    $options = array('ttl' => '+ 1 year', 'cache_id' => 'at_base:service_tags');

    return at_cache($options, function() {
      $tags = array();
      foreach (Definition::getAll() as $service_name => $service) {
        $tags = array_merge($service['tags'], $tags);
      }

      return array_unique($tags);
    });
  }
}
