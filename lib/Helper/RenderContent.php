<?php

namespace Drupal\at_base\Helper;

/**
 * Helper class for rendering data:
 *
 *  $data = array(
 *    'template_string' => $template_string,
 *    'variables' => $variables,
 *    'attached' => $attached,
 *  );
 *  return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
 *
 *  $data = array(
 *    'controller' => array('\Drupal\atest_base\Controller\Sample', 'renderAction'),
 *  );
 *  return at_id(new \Drupal\at_base\Helper\RenderContent($data))->render();
 *
 * @see  \Drupal\at_route\Controller\DefaultController
 * @see  \Drupal\at_theming\Hook\BlockView
 */
class RenderContent {
  private $data;

  public function __construct($data) {
    if (isset($data['template'])) {
      $data['template_file'] = $data['template'];
      unset($data['template']);
    }
    $this->data = $data;
  }

  public function render() {
    if (is_string($this->data)) {
      return $this->data;
    }

    if (isset($this->data['template_string'])) {
      return $this->renderTemplateString();
    }

    if (isset($this->data['template_file'])) {
      return $this->renderTemplateFile();
    }

    if (isset($this->data['controller'])) {
      return $this->renderController();
    }

    throw new \Exception('Invalid data structure.');
  }

  private function renderTemplateString() {
    $variables = !empty($this->data['variables']) ? $this->data['variables'] : array();

    return array(
      '#markup' => at_theming_render_string_template($this->data['template_string'], $variables),
      '#attached' => $this->processAttachedAsset(),
    );
  }

  private function renderTemplateFile() {
    if (!function_exists('at_theming_render_template')) {
      throw new \Exception('Missing at_theming module');
    }

    return array(
      '#markup' => at_theming_render_template($this->data['template_file'], $this->data['variables']),
      '#attached' => $this->processAttachedAsset(),
    );
  }

  private function renderController() {
    list($class, $action) = $this->data['controller'];

    $return = array(
      '#markup' => call_user_func(array(new $class(), $action)),
      '#attached' => $this->processAttachedAsset(),
    );

    return $return;
  }

  private function processAttachedAsset() {
    if (empty($this->data['attached'])) return array();

    foreach (array_keys($this->data['attached']) as $type) {
      foreach ($this->data['attached'][$type] as $k => $item) {
        if (is_string($item)) {
          $this->data['attached'][$type][$k] = $this->processAssetPath($item);
        }
      }
    }

    return $this->data['attached'];
  }

  private function processAssetPath($path) {
    $path = str_replace('%theme', path_to_theme(), $path);
    return $path;
  }
}
