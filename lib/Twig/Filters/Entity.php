<?php

namespace Drupal\at_base\Twig\Filters;

/**
 * Handler for drupalEntity Twig filter.
 *
 * Usage:
 *
 *     {{ 'user:1' | drupalEntity }}
 */
class Entity {
  /**
   * Callback for drupalEntity filter.
   *
   * @param  string  $string  %entity_type:%id:%view_mode
   * @param  array   $options
   */
  public static function render($string, $options = array()) {
    $string = explode(':', $string);
    if (2 !== count($string)) {
      return '<!-- Wrong param -->';
    }

    @list($entity_type, $entity_id, $view_mode) = $string;
    $entity = entity_load($entity_type, array($entity_id));
    $view_mode = !empty($view_mode) ? $view_mode : 'full';

    if (!$entity) {
      return '<!-- Entity node found -->';
    }

    $entity = reset($entity);

    $build = entity_view($entity_type, array($entity), $view_mode);

    if (!empty($options['template'])) {
      $path = at_container('helper.real_path')->get($options['template']);
      return at_container('twig')
                ->render($path, array('build' => $build[$entity_type][$entity_id]));
    }

    return render($build);
  }
}
