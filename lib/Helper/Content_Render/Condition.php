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

  public function check() {
    // If conditions are not provided, content is always rendered.
    if (empty($this->data['conditions'])) {
      return TRUE;
    }
    $conditions = $this->data['conditions'];

    if (empty($conditions['type'])) {
      $condition_type = 'and';
      $result = TRUE;
    }
    else {
      switch ($conditions['type']) {
        case 'or':
          $condition_type = 'or';
          $result = FALSE;
          break;

        case 'xor':
          $condition_type = 'xor';
          $result = FALSE;
          break;

        case 'not':
          $condition_type = 'not';
          $result = TRUE;
          break;

        default:
          $condition_type = 'and';
          $result = TRUE;
          break;
      }
    }

    // Condition callbacks
    if (empty($conditions['callbacks'])) {
      if ($condition_type == 'not') {
        // Not of (always TRUE) is FALSE.
        return FALSE;
      }
      return TRUE;
    }
    else {
      $callbacks = $conditions['callbacks'];
    }

    foreach ($callbacks as $callback) {
      if ($condition_type == 'and') {
        $result = $result && $this->callCallback($callback);
      }
      else if ($condition_type == 'or') {
        $result = $result || $this->callCallback($callback);
      }
      else if ($condition_type == 'xor') {
        $result = $result ^ $this->callCallback($callback);
      }
      else {
        // Not
        $result = $result && $this->callCallback($callback);
      }
    }

    if ($condition_type == 'not') {
      $result = !$result;
    }

    return $result;
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
