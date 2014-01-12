<?php
namespace Drupal\at_base\Helper\Content_Render;

/**
 * @todo  Doc & Test for $data['before'], $data['after']
 */
class Process {
  private $data;
  private $args;

  /**
   * @var Process_Call
   */
  private $caller;

  public function __construct($data, $args) {
    $this->data = $data;
    $this->args = $args ? $args : array();

    if (!empty($data['before'])) {
      $this->caller = new Process_Call(
        !empty($data['before']) ? $data['before'] : array()
      );
    }
  }

  public function execute() {
    !empty($this->caller) && $this->caller->callBefore();

    foreach (get_class_methods(get_class($this)) as $method) {
      if ('process' === substr($method, 0, 7)) {
        $return = $this->{$method}();
        if (!is_null($return)) {
          return $return;
        }
      }
    }
    throw new \Exception('Unsupported data structure.');
  }

  private function processFunction() {
    if (isset($this->data['function'])) {
      $func = $this->data['function'];
      if (!function_exists($func)) {
        throw new \Exception('Function "'.$func.'" does not exist.');
      }
      
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
  
      if (class_exists($class)) {
        $obj = new $class();
        if ( !method_exists($obj, $method)) {
          throw new \Exception('Class "'.$class.'" hasn\'t method "'.$method.'"');
        }
      }
      else {
        throw new \Exception('Class "'.$class.'" does not exist.');
      }
      
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
    if (!is_array($args)) {
      throw new \Exception('This variable "'.$args.'" must be a array.');
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

  /**
   * @param string $tpl
   */
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

      throw new \Exception('No template available: ' . print_r($tpls, TRUE));
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