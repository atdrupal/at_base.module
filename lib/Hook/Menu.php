<?php
namespace Drupal\at_base\Hook;

use Drupal\at_base\Route\Importer;

class Menu
{
  private $items;

  /**
   * @var Importer
   */
  private $importer;

  public function __construct(Importer $importer)
  {
    $this->importer = $importer;
  }

  /**
   * Get all menu items.
   */
  public function getMenuItems()
  {
    $items = array();
    foreach (array('at_base' => 'at_base') + at_modules('at_base', 'routes') as $module) {
      $items += $this->importer->setModule($module)->import();
    }
    return $items;
  }
}
