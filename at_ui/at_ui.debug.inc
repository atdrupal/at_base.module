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

function at_trace() {
  require_once at_library('kint') . '/Kint.class.php';
  Kint::trace();
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
