<?php
namespace Drupal\at_base\Drush\Command\AtRequire;

class DependencyFetcher {
  private $name;
  private $info;
  private $contrib_destination;

  public function __construct($name, $info) {
    $this->name = $name;
    $this->info = $info;
  }

  /**
   * Fetch dependency, if it's existing:
   *   0. Cancel
   *   1. Update
   *   2. Download in site directory
   */
  public function fetch() {
    $contrib_destination = $this->getContribDestination();
    if (!empty($contrib_destination)) {
      $this->_fetchDependency($contrib_destination);
    }
  }

  private function getDestination() {
    if ($this->info['type']  === 'module')   return 'modules';
    if ($this->info['type']  === 'theme')    return 'themes';
    if ($this->info['type']  === 'library')  return 'libraries';
  }

  /**
   * Find destination to download the project.
   *
   * @return string|boolean
   */
  private function getContribDestination() {
    if (!is_null($this->contrib_destination)) {
      return $this->contrib_destination;
    }

    $p_all  = 'sites/all/' . $this->getDestination() . '/' . $this->name;
    $p_site = conf_path() . '/' . $this->getDestination() . '/' . $this->name;

    // Non-existing
    if (!is_dir($p_all) && !is_dir($p_site)) {
      return 'sites/all';
    }

    if (is_dir($p_site)) {
      $msg = '[at_require] %s is already exist (%s), would you like to override it?';
      $msg = sprintf($msg, $this->name, $p_site);
      $this->contrib_destination = drush_confirm($msg) ? conf_path() : FALSE;
    }
    elseif (is_dir($p_all))  {
      $msg = '[at_require] %s is already exist (%s), would you like to override it?';
      $msg = sprintf($msg, $this->name, $p_all);
      $choice = array(0 => 'Skip download', 1 => 'Re-download', 2 => 'Download to ' . $p_site);
      $choice = drush_choice($choice, $msg);
      $this->contrib_destination = $choice == 1 ? 'sites/all' : ($choice == 2 ? conf_path() : FALSE);
    }

    return $this->contrib_destination;
  }

  private function _fetchDependency($contrib_destination = 'sites/all') {
    $this->info += array(
      'type' => $this->info['type'],
      'destination' => $this->getDestination(),
      'name' => $this->name,
      'build_path' => DRUPAL_ROOT,
      'make_directory' => DRUPAL_ROOT,
      'contrib_destination' => $contrib_destination,
      'directory_name' => $this->name,
    );

    $class = \DrushMakeProject::getInstance('AtRequire_Library', $this->info);
    $class->make();
  }
}
