<?php

namespace Drupal\at_base\Twig\Filters;

/**
 * Callback for drupalView Twig filter.
 *
 * @todo  More test case for view with custom template, …
 * @todo  Test pager option.
 */
class Views extends Views_Base {
  /**
   * @var \Exception
   */
  private $exception;

  public function __construct() {
    $args = func_get_args();

    $method = !isset($args[1]) || is_string($args[1])
      ? 'constructTradition'
      : 'constructFancy'
    ;

    try {
      call_user_func_array(array($this, $method), $args);
    }
    catch (\Exception $e) {
      $this->exception = $e;
    }
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

  public function render() {
    if (!empty($this->exception)) {
      return $this->exception->getMessage();
    }

    // No template, use default
    if (!$this->template && (!$this->template = $this->suggestTemplate())) {
      $this->view->pre_execute();
      return $this->view->preview($this->display_id, $this->arguments);
    }

    return $this->renderTemplate();
  }

  protected function beforeRenderTemplate() {
    // Include Views theming functions
    module_load_include('inc', 'views', 'theme/theme');

    // Many tags rendered by views, we get rid of them
    if (!empty($this->view->display[$this->display_id]->display_options['fields'])) {
      foreach (array_keys($this->view->display[$this->display_id]->display_options['fields']) as $k) {
        $this->view->display[$this->display_id]->display_options['fields'][$k]['element_default_classes'] = 0;
        $this->view->display[$this->display_id]->display_options['fields'][$k]['element_type'] = 0;
      }
    }

    // Execute view pre-hooks
    $this->view->pre_execute();
  }

  protected function renderTemplate() {
    $this->beforeRenderTemplate();

    0 === strpos($this->view->base_table, 'search_api_index_')
      ? $this->view->preview($this->display_id, $this->arguments)
      : $this->view->execute();

    // Issue when view returns no result.
    if (empty($this->view->style_plugin)) {
      $this->view->init_style();
    }

    $vars = array('view' => $this->view);
    template_preprocess_views_view($vars);
    return at_container('twig')->render($this->template, $vars);
  }
}
