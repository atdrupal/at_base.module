<?php

namespace Drupal\at_base;

class FormInterface {

  public function setForm(array &$form);

  public function setFormState(array &$form_state);

  public function get();

  public function validate();

  public function submit();
}
