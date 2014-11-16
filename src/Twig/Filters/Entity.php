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

  private $entity_type;
  private $entity_id;
  private $view_mode;
  private $entity;
  private $options;

  /**
   * @param  string  $string  %entity_type:%id:%view_mode
   * @param  array   $options
   */
  public function __construct($string, $options = array()) {
    @list($this->entity_type, $this->entity_id, $this->view_mode) = $this->detectParams($string);
    $this->view_mode = !empty($this->view_mode) ? $this->view_mode : 'full';
    $this->entity = $this->load($this->entity_type, $this->entity_id);
    $this->options = $options;
  }

  private function detectParams($string) {
    $string = explode(':', $string);
    if (2 !== count($string)) {
      throw new \Exception('Wrong param');
    }
    return $string;
  }

  private function load($entity_type, $entity_id) {
    if ($entity = entity_load_single($entity_type, $entity_id)) {
      return $entity;
    }

    throw new \Exception('Entity node found');
  }

  public function render() {
    if (!empty($this->options['template'])) {
      return $this->renderTemplate();
    }

    $build = entity_view(
      $this->entity_type,
      array($this->entity),
      $this->view_mode
    );

    return render($build);
  }

  private function renderTemplate() {
    $path = at_container('helper.real_path')->get($this->options['template']);
    return at_container('twig')->render(
      $path,
      array(
        'entity_type' => $this->entity_type,
        'entity_id' => $this->entity_id,
        'entity' => $this->entity,
      )
    );
  }
}
