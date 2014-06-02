<?php

namespace Drupal\at_base;

class Form {

  protected function getStaticCallback($method) {
    return function ($form, &$form_state) use ($method) {
        // Build the form
        list($class, $args) = $form['#at_form'];

        $obj = at_newv($class, $args);

        if ($obj instanceof FormInterface) {
          $obj->setForm($form);
          $obj->setFormState($form_state);
          $obj->{$method}();
        }

        throw new \Exception('Form controller must implement Drupal\\at_base\\FormInterface.');
      };
  }

  private static function buildForm(FormInterface $controller) {
    $controller->setForm($form);
    $controller->setFormState($form_state);

    $return = $controller->get();
    $return['#at_form'] = array($class, $args);
    $return['#validate'][] = self::getStaticCallback('validate');
    $return['#submit'][] = self::getStaticCallback('submit');

    return $return;
  }

  /**
   * Wrapper for class based forms.
   */
  public static function get($form, &$form_state) {
    // Get the variables from arguments
    $args = func_get_args();
    $form = array_shift($args);
    $form_state = array_shift($args);
    $class = array_shift($args);
    $args = reset($args);

    // Build the form
    $controller = at_newv($class, $args);
  }

}
