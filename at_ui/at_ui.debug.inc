<?php
/**
 * @file
 */

function atsm() {
  if (user_access('access devel information')) {
    require_once at_library('kint') . '/Kint.class.php';

    ob_start();
    call_user_func_array("Kint::dump", func_get_args());
    $output = ob_get_clean();

    drupal_set_message($output);
  }
}

function atdr() {
  if (user_access('access devel information')) {
    require_once at_library('kint') . '/Kint.class.php';
    ob_start();
    call_user_func_array("Kint::dump", func_get_args());
    return ob_get_clean();
  }
}

function at_trace($trace = null, $return = 'print') {
  require_once at_library('kint') . '/Kint.class.php';

  ob_start();
  Kint::trace($trace);
  $output = ob_get_clean();

  switch ($return) {
    case 'message';
      drupal_set_message($output);
      break;

    case 'return':
      return $output;

    case 'print':
    default:
      echo $output;
      break;
  }
}

function atd() {
  if (user_access('access devel information')) {
    require_once at_library('kint') . '/Kint.class.php';
    call_user_func_array("Kint::dump", func_get_args());
  }
}

function atdd() {
  if (user_access('access devel information')) {
    require_once at_library('kint') . '/Kint.class.php';
    call_user_func_array("Kint::dump", func_get_args());
    die;
  }
}
