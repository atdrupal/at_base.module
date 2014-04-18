<?php
namespace Drupal\at_base\Helper\Content_Render;

/**
 * @todo  Doc
 */
class Condition {
  private $data;
  private $args;

  public function __construct($data, $args) {
    $this->data = $data;
    $this->args = $args ? $args : array();
  }

  private function initData() {
    if (empty($this->data['conditions'])) {
      // If conditions are not provided, content is always rendered.
      $this->result = TRUE;
      return TRUE;
    }
    $conditions = $this->data['conditions'];

    if (empty($conditions['type'])) {
      $this->conditionType = 'and';
      $this->result = TRUE;
    }
    else {
      switch ($conditions['type']) {
        case 'or':
          $this->conditionType = 'or';
          $this->result = FALSE;
          break;

        case 'xor':
          $this->conditionType = 'xor';
          $this->result = FALSE;
          break;

        case 'not':
          $this->conditionType = 'not';
          $this->result = TRUE;
          break;

        default:
          $this->conditionType = 'and';
          $this->result = TRUE;
          break;
      }
    }

    // Condition callbacks
    if (empty($conditions['callbacks'])) {
      if ($this->conditionType == 'not') {
        // Not of 'always TRUE' is FALSE.
        $this->result = FALSE;
        return TRUE;
      }
      $this->result = TRUE;
      return TRUE;
    }
    else {
      $this->callbacks = $conditions['callbacks'];
    }

    return FALSE;
  }

  public function check() {
    $hardBreak = $this->initData();
    if ($hardBreak) {
      return $this->result;
    }

    foreach ($this->callbacks as $callback) {
      switch ($this->conditionType) {
        case 'and':
          $this->result = $this->result && $this->callCallback($callback);
          break;

        case 'or':
          $this->result = $this->result || $this->callCallback($callback);
          break;

        case 'xor':
          $this->result = $this->result ^ $this->callCallback($callback);
          break;

        default:
          // Not.
          $this->result = $this->result && $this->callCallback($callback);
          break;
      }
    }

    if ($this->conditionType == 'not') {
      $this->result = !$this->result;
    }

    return $this->result;
  }

  private function callCallback($callback) {
    if (is_array($callback) && !empty($callback) && count($callback) <= 3 || is_string($callback)) {
      if (is_string($callback)) {
        $callable = $callback;
        $arguments = array();
      }
      else {
        // Use system argument flag.
        if (count($callback) == 3) {
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

      if (strpos($callable, '@' === 0)) {
        // Getting service.
        $callable = str_replace('@', '', $callable);
        list($service_name, $method) = explode($callable, ':');
        $service = at_container($service_name);
        $callable = array($service, $method);
      }

      if (is_callable($callable)) {
        return call_user_func_array($callable, $arguments);
      }
    }

    throw new \Exception('Callback is not callable.');
  }
}
