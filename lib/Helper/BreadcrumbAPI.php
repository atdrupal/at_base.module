<?php
namespace Drupal\at_base\Helper;

/**
 * Callback for breadcrumb_api service.
 */
class BreadcrumbAPI {
  /**
   * Load breadcrumb configuration for a specific entity/view module. If
   *   configuraiton is available, set it to service container.
   *
   * Example configuration
   *
   * file: your_module/config/breadcrumb.yml
   *
   * entity:
   *   node:                   # <-- entity type
   *     page:                 # <-- bundle
   *       full:               # <-- view mode
   *         breadcrumbs:      # <-- static breadcrumb
   *           - ['Home', '<front>']
   *           - ['About', 'about-us']
   *     article:              # <-- bundle
   *       full:               # <-- view mode
   *         controller:       # <-- dynamic breadcrumbs, rendered by a controller.
   *           - class_name
   *           - method_name
   *           - [argument_1, argument_2, argument_3]
   *     gallery:              # <-- bundle
   *       full:               # <-- view mode
   *         function:  my_fn  # <-- dynamic breadcrumbs, rendered by a controller.
   *         arguments: [argument_1, argument_2, argument_3]
   *
   * @see at_base_entity_view()
   * @param  \stdClass $entity
   * @param  string $type
   * @param  string $view_mode
   * @param  string $langcode
   * @return null
   */
  public function checkEntityConfig($entity, $type, $view_mode, $langcode) {
    $cache_options = array('id' => "atbc:{$type}:{$view_mode}");
    $cache_callback = array($this, 'fetchEntityConfig');
    $cache_arguments = array($type, $view_mode);
    if ($config = at_cache($cache_options, $cache_callback, $cache_arguments)) {
      $this->set($config);
    }
  }

  public function fetchEntityConfig($type, $view_mode) {
    dsm(array($type, $view_mode));
    foreach (at_modules('at_base', 'breadcrumb') as $module) {
      dsm($module);
    }
  }

  /**
   * Set a breacrumb configuration to service container.
   *
   * @param array $config
   */
  public function set(array $config) {
    at_container('container')->offsetSet('breadcrumb', $config);
  }

  /**
   * Get breadcrumb configuration from service container.
   *
   * @return array
   */
  public function get() {
    if (at_container('container')->offsetExists('breadcrumb')) {
      return at_container('breadcrumb');
    }
  }
}
