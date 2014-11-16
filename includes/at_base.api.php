<?php

/**
 * Service container.
 *
 * @staticvar \Drupal\at_base\Container $container
 * @param string $id
 * @return mixed
 *
 * @see https://github.com/andytruong/at_base/wiki/7.x-2.x-service-container
 */
function at_container($id = 'container')
{
    static $container = NULL;

    if (!$container) {
        $container = new Container();
    }

    $args = func_get_args();
    if (1 !== count($args)) {
        array_shift($args);
        $container["{$id}:arguments"] = $args;
    }

    return $container[$id];
}

/**
 * Wrapper for Key-Value services.
 *
 * @param  string $bin
 * @param  array  $options
 */
function at_kv($bin, $options = array(), $engine_name = 'array')
{
    global $at;

    if (isset($at['kv'][$bin]['engine'])) {
        $engine_name = $at['kv'][$bin]['engine'];
    }

    $engine = at_container("kv.engine.{$engine_name}");
    $engine->setOptions($options);

    return $engine;
}
