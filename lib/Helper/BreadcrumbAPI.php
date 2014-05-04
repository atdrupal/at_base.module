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
    $cache_options = array('id' => "atbc:{$type}:". at_fn('entity_bundle', $type, $entity) .":{$view_mode}");
    $cache_callback = array($this, 'fetchEntityConfig');
    $cache_arguments = func_get_args();

    if ($config = at_cache($cache_options, $cache_callback, $cache_arguments)) {
      $config['context'] = array('type' => 'entity', 'arguments' => func_get_args());
      $this->set($config);
    }
  }

  public function fetchEntityConfig($entity, $type, $view_mode, $langcode) {
    foreach (\at_fn::at_modules('at_base', 'breadcrumb') as $module) {
      $config = at_config($module, 'breadcrumb')->get('breadcrumb');

      $bundle = \at_fn::entity_bundle($type, $entity);

      if (isset($config['entity'][$type][$bundle][$view_mode])) {
        return $config['entity'][$type][$bundle][$view_mode];
      }
    }
  }

  public function checkPathConfig() {
    $path_config = at_cache('atbc:paths', function () {
      $items = array();
      foreach (\at_fn::at_modules('at_base', 'breadcrumb') as $module) {
        $config = at_config($module, 'breadcrumb')->get('breadcrumb');
        if (!empty($config['paths'])) {
          $items = array_merge($items, $config['paths']);
        }
      }
      return $items;
    });

    // Convert the Drupal path to lowercase
    $current_path = drupal_strtolower(at_fn('drupal_get_path_alias', at_fn('request_path')));

    foreach ($path_config as $path => $config) {
      if (drupal_match_path($current_path, $path)) {
        $config['context'] = array('type' => 'path');
        $this->set($config);
      }
    }
  }

  /**
   * Set a breacrumb configuration to service container.
   *
   * @param array $config
   */
  public function set(array $config) {
    if ($_config = $this->get()) {
      $old_weight = isset($_config['weight']) ? $_config['weight'] : 0;
      $new_weight = isset($config['weight'])  ? $config['weight']  : 0;
      if ($new_weight <= $old_weight) {
        at_context('breadcrumb', $config);
      }
    }
    else {
      at_context('breadcrumb', $config);
    }
  }

  /**
   * Get breadcrumb configuration from service container.
   *
   * @return array
   */
  public function get() {
    return at_context('breadcrumb');
  }

  /**
   * @see at_base_page_build()
   */
  public function pageBuild() {
    $this->checkPathConfig();

    if ($config = $this->get()) {
      $bc = !empty($config['breadcrumbs']) ? $config['breadcrumbs'] : array();

      // User can send direct breadcrumb structure, or use a callback to build it.
      if (empty($bc)) {
        $bc = at_container('helper.content_render')->render($config);
      }

      $args = isset($config['context']['arguments']) ? $config['context']['arguments'] : array();
      return $this->buildBreadcrumbs($bc, $args);
    }
  }

  private function buildBreadcrumbs($bc = array(), $args = array()) {
    global $user;

    $token_data = array('user' => $user);
    if (!empty($args[1])) {
      switch ($args[1]) {
        case 'node':
        case 'user':
          $token_data[$args[1]] = $args[0];
          break;
      }
    }

    foreach ($bc as &$item) {
      foreach ($item as &$item_e) {
        $item_e = at_fn('token_replace', $item_e, $token_data);
      }

      $item = count($item) == 2 ? at_fn('l', $item[0], $item[1]) : reset($item);
    }

    drupal_set_breadcrumb($bc);
  }
}
