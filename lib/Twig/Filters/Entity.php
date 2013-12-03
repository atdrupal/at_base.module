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
   * @param  string  $string       %entity_type:%id:%view_mode
   */
  public static function render($string) {
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

    if (!function_exists('entity_view')) {
      throw new \Exception('Missing module: entity');
    }

    $output = entity_view($entity_type, array($entity), $view_mode);
    return render($output);
  }
}
