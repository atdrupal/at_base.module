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
   *           - ['%entity', '%entity_type', '%bundle', '%view_mode', '%langcode']
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
    $cache_arguments = func_get_args();

    if ($config = at_cache($cache_options, $cache_callback, $cache_arguments)) {
      $config['context'] = array('type' => 'entity', 'arguments' => $cache_arguments);
      $this->set($config);
    }
  }

  public function fetchEntityConfig($entity, $type, $view_mode, $langcode) {
    foreach (at_modules('at_base', 'breadcrumb') as $module) {
      $config = at_config($module, 'breadcrumb')->get('breadcrumb');
      $bundle = entity_bundle($type, $entity);
      if (isset($config[$type][$bundle][$view_mode])) {
        return $config[$type][$bundle][$view_mode];
      }
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

  /**
   * Apply breadcrumb configuration to page.
   */
  public function execute($config) {
    $bc = array();

    if (!empty($config['breadcrumbs'])) {
      $bc = $config['breadcrumbs'];
    }

    switch ($config['context']['type']) {
      case 'entity':
        return $this->buildEntityBreadcrumbs($bc, $config['tokens'], $config['context']['arguments']);
    }
  }

  private function buildEntityBreadcrumbs($bc = array(), $tokens = array(), $args = array()) {
    global $user;

    $token_data = array('user' => $user);
    switch ($args[1]) {
      case 'node':
      case 'user':
        $token_data[$args[1]] = $args[0];
        break;
    }

    foreach ($bc as &$item) {
      foreach ($item as &$item_e) {
        $item_e = token_replace($item_e, $token_data);
      }

      $item = count($item) == 2 ? l($item[0], $item[1]) : reset($item);
    }

    drupal_set_breadcrumb($bc);
  }
}
