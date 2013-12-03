<?php

namespace Drupal\at_base\Twig\Filters;

class Views {
  /**
   * Callback for drupalView filter.
   *
   * @param  string $name       Name of view
   * @param  string $display_id Display ID of view
   * @return string
   */
  public static function render($name, $display_id = 'default') {
    $args = func_get_args();
    array_shift($args); // remove $name
    if (count($args)) {
      array_shift($args); // remove $display_id
    }

    if (!function_exists('views_get_view')) {
      throw new \Exception('Missing module: views');
    }

    if (!$view = views_get_view($name)) {
      return '<!-- Views not found -->';
    }

    if (!$view->access($display_id)) {
      return '<!-- Access denied -->';
    }

    $view->set_display($display_id);

    if (is_array($args)) {
      $view->set_arguments($args);
    }

    if ($template_file = self::findTemplate($name, $display_id)) {
      // Many tags rendered by views, we get rid of them
      if (!empty($view->display[$display_id]->display_options['fields'])) {
        foreach (array_keys($view->display[$display_id]->display_options['fields']) as $k) {
          $view->display[$display_id]->display_options['fields'][$k]['element_default_classes'] = 0;
          $view->display[$display_id]->display_options['fields'][$k]['element_type'] = 0;
        }
      }

      $view->pre_execute();
      $view->execute();

      module_load_include('inc', 'views', 'theme/theme');
      $vars['view'] = $view;
      template_preprocess_views_view($vars);
      return at_theming_render_template($template_file, $vars);
    }

    return $view->preview($display_id, $args);
  }

  /**
   * Find Twig template for view on context theme.
   *
   * @param  string $name       Name of view
   * @param  string $display_id Display ID of view
   * @return string             Path to template file
   */
  protected static function findTemplate($name, $display_id) {
    $suggestions[] = path_to_theme() . "/templates/views/{$name}.{$display_id}.html.twig";
    $suggestions[] = path_to_theme() . "/templates/views/{$name}.html.twig";
    foreach ($suggestions as $path) {
      if (is_file(DRUPAL_ROOT . '/' . $path)) {
        return $path;
      }
    }
  }
}
