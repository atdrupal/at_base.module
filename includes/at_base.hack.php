<?php

/**
 * @file at_base.misc.php
 *
 * Mostly hack here, to be improved/removed.
 */

/**
 * Return if key|upercase/variable is not FALSE|NULL|0.
 */
function at_valid($key, $get_value = FALSE) {
  $c = strtoupper($key);
  if (defined($c)) {
    $return = constant($c);
  }
  else {
    $return = variable_get($key, FALSE);
  }
  return $get_value ? $return : (boolean) ($return);
}

/**
 * Check is Drupal system on dev or not.
 *
 * @return boolean
 */
function at_debug() {
  return defined('AT_DEBUG');
}

if (!function_exists('yaml_parse')) {

  /**
   * Read YAML file.
   *
   * @param  string $path Path to yaml file.
   * @return mixed
   */
  function yaml_parse_file($path) {
    if (!is_file(DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php')) {
      throw new \RuntimeException('Missing library: spyc');
    }

    if (!function_exists('spyc_load_file')) {
      require_once DRUPAL_ROOT . '/sites/all/libraries/spyc/Spyc.php';
    }

    return spyc_load_file($path);
  }

}

if (!function_exists('yaml_emit')) {

  function yaml_emit($data) {
    return spyc_dump($data);
  }

}

/**
 * Override default callback of $fn.
 *
 * @see  at_fn()
 * @param  string   $fn
 * @param  callable $callback
 */
function at_fn_fake($fn, $callback) {
  global $conf;
  $conf["atfn:{$fn}"] = $callback;
}

/**
 * Similar to at_fn_fake(). Usage:
 *
 *  $time = time();
 *  at_fake::time(function() use ($time) { return $time; });
 *  echo at_fn::time(); // same to $time
 *
 *  // Next 1 hour
 *  at_fake::time(function() use ($time) { return $time + 3600; });
 *  echo at_fn::time(); // same to $time + 3600
 */
class at_fake {

  public static function __callStatic($fn, $args) {
    $GLOBALS['conf']["atfn:{$fn}"] = $args[0];
  }

}

/**
 * Wrapper for class based forms.
 */
function at_form_validate($form, &$form_state) {
  // Build the form
  list($class, $args) = $form['#at_form'];

  $obj = at_newv($class, $args);
  $obj->setForm($form);
  $obj->setFormState($form_state);
  $obj->validate();
}

/**
 * Wrapper for class based forms.
 */
function at_form_submit($form, &$form_state) {
  // Build the form
  list($class, $args) = $form['#at_form'];

  $obj = at_newv($class, $args);
  $obj->setForm($form);
  $obj->setFormState($form_state);
  $obj->submit();
}

/**
 * Wrapper for class based forms.
 *
 * @todo  Test me.
 */
function at_form($form, &$form_state) {
  // Get the variables from arguments
  $args = func_get_args();
  $form = array_shift($args);
  $form_state = array_shift($args);
  $class = array_shift($args);
  $args = reset($args);

  // Build the form
  $obj = at_newv($class, $args);
  $obj->setForm($form);
  $obj->setFormState($form_state);

  $form = $obj->get();
  $form['#at_form'] = array($class, $args);

  return $form;
}

/**
 * Shortcut to expression_language:evaluate.
 */
function at_eval($expression) {
  return at_container('expression_language')->evaluate($expression);
}
