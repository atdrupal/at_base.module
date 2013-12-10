<?php
namespace Drupal\at_base\Helper;

/**
 * Read weight for module file, update the value in system table.
 *
 * @see  \Drupal\at_base\Hook\Flush_Cache::fixModuleWeight()
 * @see  at_base_modules_enabled()
 */
class Module_Weight {
  public function execute() {
    foreach (system_list('module_enabled') as $module_name => $project) {
      if (!empty($project->info['weight'])) {
        $weight = $project->info['weight'];
        if (is_numeric($weight)) {
          $sql = "UPDATE {system} SET weight = :weight WHERE name = :name";
          db_query($sql, array(':weight' => $weight, ':name' => $module_name));
        }
      }
    }
  }
}
