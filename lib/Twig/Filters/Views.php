<?php

namespace Drupal\at_base\Twig\Filters;

/**
 * Callback for drupalView Twig filter.
 *
 * @todo  More test case for view with custom template, …
 */
class Views {
  private $view;
  private $name;
  private $display_id = 'default';
  private $template;
  private $arguments = array();

  public function __construct($name, $display_id = 'default', $arguments = array()) {
    if (!$this->view = views_get_view($name)) {
      throw new \Exception('View not found: '. $this->name);
    }

    $this->name = $name;
    $this->setDisplayId($display_id);
    $this->setArguments($arguments);

    return $this;
  }

  public function setDisplayId($display_id) {
    $this->display_id = $display_id;
    $this->view->set_display($display_id);

    if (!$this->view->access($this->display_id)) {
      throw new \Exception('<!-- Access denied: '. $this->name .':'. $this->display_id .' -->');
    }
  }

  public function setArguments($arguments) {
    $this->arguments = $arguments;

    if (is_array($this->arguments)) {
      $this->view->set_arguments($this->arguments);
    }
  }

  public function setTemplate($template) {
    $this->template = at_container('helper.real_path')->get($template);
  }

  /**
   * Find Twig template for view on context theme.
   *
   * @todo Remove this magic
   */
  protected function suggestTemplate() {
    $suggestions = array();
    $suggestions[] = path_to_theme() . "/templates/views/{$this->name}.{$this->display_id}.html.twig";
    $suggestions[] = path_to_theme() . "/templates/views/{$this->name}.html.twig";
    foreach ($suggestions as $path) {
      if (is_file(DRUPAL_ROOT . '/' . $path)) {
        return $path;
      }
    }
  }

  public function execute() {
    // ---------------------
    // No template, use default
    // ---------------------
    if (!$this->template && (!$this->template = $this->suggestTemplate())) {
      $this->view->pre_execute();
      return $this->view->preview($this->display_id, $this->arguments);
    }

    // ---------------------
    // With template
    // ---------------------
    // Many tags rendered by views, we get rid of them
    if (!empty($this->view->display[$this->display_id]->display_options['fields'])) {
      foreach (array_keys($this->view->display[$this->display_id]->display_options['fields']) as $k) {
        $this->view->display[$this->display_id]->display_options['fields'][$k]['element_default_classes'] = 0;
        $this->view->display[$this->display_id]->display_options['fields'][$k]['element_type'] = 0;
      }
    }

    $this->view->pre_execute();
    $this->view->execute();

    module_load_include('inc', 'views', 'theme/theme');
    $vars = array('view' => $this->view);
    template_preprocess_views_view($vars);
    return at_container('twig')->render($this->template, $vars);
  }

  /**
   * @param  array $options
   */
  public function resolveOptions($options) {
    foreach ($options as $k => $v) {
      switch ($k) {
        case 'template':   $this->setTemplate($v);  break;
        case 'display_id': $this->setDisplayId($v); break;
        case 'arguments':  $this->setArguments($v); break;
      }
    }
    return $this;
  }

  /**
   * Callback for drupalView filter.
   *
   * Two cases:
   *
   *    Views::render($name, $display_id, ...$args)
   *    Views::render($name, $options)
   *
   * @param  string $name
   * @param  string $display_id
   * @return string
   */
  public static function render($name, $display_id = 'default') {
    $args = func_get_args();
    array_shift($args);

    // Params may wrong
    try {
      $builder = self::getBuilder($display_id);
      return call_user_func_array($builder, array($name, $args));
    }
    catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * Get callback.
   *
   * @param  mixed $a1
   * @return callable
   */
  private static function getBuilder($a1) {
    if (is_string($a1)) {
      return function ($name, $display_id, $args = array()) {
        return at_id(new Views($name, $display_id, $args))->execute();
      };
    }

    return function ($name, $options) {
      return at_id(new Views($name))->resolveOptions($options)->execute();
    };
  }
}
