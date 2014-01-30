<?php
namespace Drupal\at_base\Helper\Content_Render;

class Process {
  private $data;
  private $args;

  public function __construct($data, $args) {
    $this->data = $data;
    $this->args = $args ? $args : array();
  }

  public function execute() {
    foreach (get_class_methods(get_class($this)) as $method) {
      if ('_process' === substr($method, 0, 8)) {
        if ($return = $this->{$method}()) {
          return $return;
        }
      }
    }
  }

  private function _processFunction() {
    if (isset($this->data['function'])) {
      $func = $this->data['function'];
      return call_user_func_array($func, $this->args);
    }
  }

  private function _processForm() {
    if (isset($this->data['form'])) {
      $args = array('at_form', $this->data['form']);
      $args[] = isset($this->data['form arguments']) ? $this->data['form arguments'] : array();
      return call_user_func_array('drupal_get_form', $args);
    }
  }

  private function _processController() {
    if (isset($this->data['controller'])) {
      @list($class, $method, $args) = $this->data['controller'];
      $obj = new $class();
      $args = !empty($args) ? $args : array();
      if (empty($args)) {
        if (method_exists($obj, 'getVariables')) {
          $args = $obj->getVariables();
        }
      }
      return call_user_func_array(array($obj, $method), $args);
    }
  }

  /**
   * @todo  Test case for template as array.
   */
  private function _processTemplate() {
    if (isset($this->data['template']) || isset($this->data['template_file'])) {
      $template = $this->data['template'];
      if (is_string($template)) {
        $template = at_container('helper.real_path')->get($template);
        return at_container('twig')->render($template, $this->args);
      }

      if (is_array($template)) {
        foreach ($template as $tpl) {
          $file = at_container('helper.real_path')->get($tpl);
          if (is_file($file)) {
            return at_container('twig')->render($file, $this->args);
          }
        }
      }
    }
  }

  private function _processTemplateString() {
    if (isset($this->data['template_string'])) {
      $tpl = $this->data['template_string'];
      return at_container('twig_string')->render($tpl, $this->args);
    }
  }
}
