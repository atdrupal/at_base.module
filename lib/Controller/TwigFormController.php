<?php
namespace Drupal\at_base\Controller;

class TwigFormController {
  protected $form;
  protected $form_state;

  public function setForm($form) {
    $this->form = $form;
  }

  public function setFormState(&$form_state) {
    $this->form_state = &$form_state;
  }

  public function get() {
    $form = array('#redirect' => FALSE);
    $form['string'] = array(
      '#type' => 'textarea',
      '#default_value' => (isset($_SESSION['twig_execute_code']) ? $_SESSION['twig_execute_code'] : ''),
    );
    $form['submit'] = array('#type' => 'submit', '#value' => 'Execute');
    return $form;
  }

  public function validate() {}

  public function submit() {
    $string = $this->form_state['values']['string'];
    ob_start();
    print at_container('twig_string')->render($string);
    $_SESSION['twig_execute_code'] = $string;

    if (function_exists('dsm')) {
      dsm(ob_get_clean());
    }
    else {
      drupal_set_message(print_r(ob_get_clean(), TRUE));
    }
  }
}
