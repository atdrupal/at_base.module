<?php
namespace Drupal\at_base\Helper\Content_Render;

/**
 * @todo  Doc & Test for $data['before'], $data['after']
 */
class Process {
  private $data;
  private $args;

  public function __construct($data, $args) {
    $this->data = $data;
    $this->args = $args ? $args : array();
  }

  public function execute() {
    $this->runBefore();

    foreach (get_class_methods(get_class($this)) as $method) {
      if ('process' === substr($method, 0, 7)) {
        if ($return = $this->{$method}()) {
          return $return;
        }
      }
    }

    $this->runAfter();
  }

  private function runAfter() {
    $this->runBefore('after');
  }

  private function runBefore($key = 'before') {
    if (!empty($this->data[$key])) {
      $this->runCallbacks($this->data[$key]);
    }
  }

  private function runCallbacks($calls) {
    $cr = at_container('helper.controller.resolver');
    foreach ($calls as $call) {
      $call = is_string($call) ? array($call, array()) : $call;
      if ($controller = $cr->get($call[0])) {
        call_user_func_array($controller, isset($call[1]) ? $call[1] : array());
      }
    }
  }

  private function processFunction() {
    if (isset($this->data['function'])) {
      $func = $this->data['function'];
      return call_user_func_array($func, $this->args);
    }
  }

  private function processForm() {
    if (isset($this->data['form'])) {
      $args = array('at_form', $this->data['form']);
      $args[] = isset($this->data['form arguments']) ? $this->data['form arguments'] : array();
      return call_user_func_array('drupal_get_form', $args);
    }
  }

  private function processController() {
    if (isset($this->data['controller'])) {
      @list($class, $method, $args) = $this->data['controller'];
      $obj = new $class();

      if (empty($args) && !empty($this->data['arguments'])) {
        $args = $this->data['arguments'];
      }

      return call_user_func_array(
        array($obj, $method),
        $this->getControllerArguments($args, $obj)
      );
    }
  }

  private function getControllerArguments($args, $obj) {
    $args = !empty($args) ? $args : array();
    if (empty($args) && method_exists($obj, 'getVariables')) {
      $args = $obj->getVariables();
    }
    return $args;
  }

  private function processTemplate() {
    if (isset($this->data['template']) || isset($this->data['template_file'])) {
      $tpl = isset($this->data['template']) ? $this->data['template'] : $this->data['template_file'];

      return is_string($tpl)
        ? $this->__templateSingle($tpl)
        : $this->__templateMultiple($tpl);
    }
  }

  private function __templateSingle($tpl) {
    $tpl = at_container('helper.real_path')->get($tpl);
    return at_container('twig')->render($tpl, $this->args);
  }

  private function __templateMultiple($tpls) {
    if (is_array($tpls)) {
      foreach ($tpls as $tpl) {
        $file = at_container('helper.real_path')->get($tpl);
        if (is_file($file)) {
          return at_container('twig')->render($file, $this->args);
        }
      }
    }
  }

  private function processTemplateString() {
    $k = isset($this->data['template_string'])
          ? 'template_string'
          : (isset($this->data['content']) ? 'content' : NULL)
    ;

    if (!empty($k)) {
      $tpl = $this->data[$k];
      return at_container('twig_string')->render($tpl, $this->args);
    }
  }
}
