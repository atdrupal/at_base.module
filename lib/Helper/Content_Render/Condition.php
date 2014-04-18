<?php
namespace Drupal\at_base\Helper\Content_Render;

/**
 * @todo  Doc
 */
class Condition {
  private $data;
  private $args;
  private $conditionType;
  private $result;
  private $callbacks;
  private $hardBreak;

  public function __construct($data, $args) {
    $this->data = $data;
    $this->args = $args ? $args : array();
    $this->conditionType = 'and';
    $this->result = TRUE;
    $this->callbacks = array();

    if (empty($this->data['conditions'])) {
      // If conditions are not provided, content is always rendered.
      $this->hardBreak = TRUE;
    }
    else {
      $conditions = $this->data['conditions'];
      $this->hardBreak = $this->initConditionType($conditions) ||
        $this->initDefaultValue() ||
        $this->initCallbacks($conditions);
    }
  }

  private function initConditionType($conditions) {
    if (!empty($conditions['type'])) {
      switch ($conditions['type']) {
        case 'or':
          $this->conditionType = 'or';
          break;

        case 'xor':
          $this->conditionType = 'xor';
          break;

        case 'not':
          $this->conditionType = 'not';
          break;

        default:
          break;
      }
    }

    return FALSE;
  }

  private function initDefaultValue() {
    switch ($this->conditionType) {
      case 'or':
        $this->result = FALSE;
        break;

      case 'xor':
        $this->result = FALSE;
        break;

      case 'not':
        $this->result = TRUE;
        break;

      default:
        break;
    }

    return FALSE;
  }

  private function initCallbacks($conditions) {
    if (empty($conditions['callbacks'])) {
      if ($this->conditionType == 'not') {
        // Not of 'always TRUE' is FALSE.
        $this->result = FALSE;
      }
      return TRUE;
    }

    $this->callbacks = $conditions['callbacks'];
    return FALSE;
  }

  public function check() {
    if ($this->hardBreak) {
      return $this->result;
    }

    foreach ($this->callbacks as $callback) {
      switch ($this->conditionType) {
        case 'and':
          $this->result &= $this->callCallback($callback);
          break;

        case 'or':
          $this->result |= $this->callCallback($callback);
          break;

        case 'xor':
          $this->result ^= $this->callCallback($callback);
          break;

        default:
          // Not.
          $this->result &= $this->callCallback($callback);
          break;
      }
    }

    if ($this->conditionType == 'not') {
      $this->result = !$this->result;
    }

    return $this->result;
  }

  private function prepareStringCallback($callback, &$callable, &$arguments) {
    $callable = $callback;
    $arguments = array();
  }

  private function prepareArrayCallback($callback, &$callable, &$arguments) {
    if (empty($callback)) {
      return;
    }

    if (count($callback) == 3) {
      // Use system argument flag.
      list($callable, $arguments, $has_system_arguments) = $callback;
      if ($has_system_arguments) {
        // Convert arguments to system arguments.
        foreach ($arguments as &$argument) {
          if (isset($this->args['build'][$argument])) {
            // $argument is '#entity', '#entity_type', '#bundle', '#view_mode', '#language'...
            // See more at Drupal\at_base\Hook\Entity\View_Alter::build().
            $argument = $this->args['build'][$argument];
          }
        }
      }
    }
    else if (count($callback) == 2) {
      list($callable, $arguments) = $callback;
    }
    else {
      // Sure, you can pass array with only one element, we can convert it to string.
      $callable = (string)reset($callback);
      $arguments = array();
    }
  }

  private function prepareServiceCallable(&$callable) {
    if (strpos($callable, '@' === 0)) {
      // Getting service.
      $callable = str_replace('@', '', $callable);
      list($service_name, $method) = explode($callable, ':');
      $service = at_container($service_name);
      $callable = array($service, $method);
    }
  }

  private function callCallback($callback) {
    $callable = '';
    $arguments = array();

    if (is_string($callback)) {
      $this->prepareStringCallback($callback, $callable, $arguments);
    }
    else if (is_array($callback)) {
      $this->prepareArrayCallback($callback, $callable, $arguments);
    }

    $this->prepareServiceCallable($callable);

    if (is_callable($callable)) {
      return call_user_func_array($callable, $arguments);
    }

    throw new \Exception('Callback is not callable.');
  }
}
