<?php
namespace Drupal\at_base\Drush\Command\AtRequire;

class DependenciesFetcher {
  /**
   * @var string
   */
  private $module;

  /**
   * @var array
   */
  private $data;

  public function __construct($module) {
    try {
      $this->module = $module;
      $this->data   = at_config($module, 'require')->get('projects');
    }
    // Missing spyc
    catch (\RuntimeException $e) {
      drush_at_require_spyc();
      $this->data = at_config($module, 'require')->get('projects');
    }
    // Find not found, just ignore
    catch (\Drupal\at_config\NotFoundException $e) {}
  }

  public function fetch() {
    foreach ($this->data as $name => $info) {
      at_id(new DependencyFetcher($name, $info))->fetch();
    }
  }
}
