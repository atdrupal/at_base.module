<?php
/**
 * @file at_base.misc.inc
 *
 * Mostly hack here, to be improved/removed.
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Drupal\at_base\CompilerPass as AT_CompilerPass;

if (!function_exists('at_id')) {
  function at_id($x) {
    return $x;
  }
}

/**
 * Implements hook_node_insert()
 */
function at_base_node_insert($node) {
  module_invoke_all('node_save', $node);
}

/**
 * Implements hook_node_update()
 */
function at_base_node_update($node) {
  module_invoke_all('node_save', $node);
}

if (!function_exists('entity_bundle')) {
  /**
   * Get bundle value of an entity.
   *
   * @param  string   $entity_type
   * @param  stdClass $entity
   * @return string
   */
  function entity_bundle($entity_type, $entity) {
    $info = entity_get_info($entity_type);
    if (!empty($info['entity keys']['bundle'])) {
      $key = $info['entity keys']['bundle'];
      return isset($entity->$key) ? $entity->$key : NULL;
    }
  }
}

/**
 * Make function easier to be replaced by an other one.
 *
 * For example:
 *
 *  // Override entity_bundle function
 *  $GLOBALS['conf']['atfn:entity_bundle'] = function($type, $entity) { return $entity->type; };
 *
 *  Call replacable entity_bundle function:
 *    at_fn('entity_bundle', 'node', $node);
 *
 *  @see  at_fn_fake()
 *  @todo Use at_fn class, remove in next release.
 */
function at_fn() {
  $args = func_get_args();
  $fn   = array_shift($args);
  return call_user_func_array(variable_get("atfn:{$fn}", $fn), $args);
}

/**
 * Similar to at_fn(). Usage:
 *
 *  \at_fn::entity_bundle('node', $node);
 */
class at_fn {
  public static function __callStatic($fn, $args) {
    return call_user_func_array(variable_get("atfn:{$fn}", $fn), $args);
  }
}

/**
 * Return if key|upercase/variable is not FALSE|NULL|0.
 */
function at_valid($key, $get_value = FALSE) {
  $c = strtoupper($key);

  $return = defined($c) ? constant($c) : variable_get($key, FALSE);

  return $get_value ? $return : (boolean)($return);
}

/**
 * Check is Drupal system on dev or not.
 *
 * @return boolean
 */
function at_debug() {
  return defined('AT_DEBUG');
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
 * Shortcut to expression_language:evaluate.
 */
function at_eval($expression) {
  return at_container('expression_language')->evaluate($expression);
}

/**
 * Function to build service container object.
 *
 * @return \Symfony\Component\DependencyInjection\ContainerBuilder
 */
function at_container_factory() {
  $dump = !defined('AT_BASE_TESTING_UNIT');

  if ($dump) {
    if ($dumped_file = variable_get('file_temporary_path', FALSE)) {
      $dumped_file .= '/container.php';
    }

    if (file_exists($dumped_file)) {
      require_once $dumped_file;
      return new AT_Container();
    }
  }

  $container = new ContainerBuilder();
  $container->addCompilerPass(new AT_CompilerPass());
  $container->compile();

  if ($dump && !empty($dumped_file)) {
    $dumper = new PhpDumper($container);
    file_put_contents($dumped_file, $dumper->dump(array('class' => 'AT_Container')));
    require_once $dumped_file;
    return new AT_Container();
  }

  return $container;
}
