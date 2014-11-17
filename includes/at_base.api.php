<?php

use Drupal\at_base\Cache;
use Drupal\at_base\Config\Config;

/**
 * Service container.
 *
 * @return \Drupal\at_base\Container
 */
function at_container()
{
    if (!$container = &drupal_static(__FUNCTION__)) {
        $fileName = variable_get('file_private_path', '') . '/at_container.php';
        if (file_exists($fileName)) {
            require_once $fileName;
            return $container = new AT_Container();
        }
        return $container = at()->getHelper()->getContainerCreator($fileName)->create();
    }
    return $container;
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

/**
 * Care about site caching.
 *
 * @param  array|string $options
 * @param  Closure|string $callback
 * @param  array  $arguments
 * @return mixed
 * @see    at_cache_flush_by_tag()
 * @see    https://github.com/andytruong/at_base/wiki/7.x-2.x-cache-warming
 * @see    https://github.com/andytruong/at_base/wiki/7.x-2.x-Function:-at_cache
 */
function at_cache($options, $callback = NULL, $arguments = array())
{
    // User prefer string as cache options
    // Style: $id OR $id,$ttl OR $id,~,$bin OR $id,~,~ OR $id,$ttl,$bin
    if (is_string($options)) {
        @list($id, $ttl, $bin) = explode(',', $options);

        $options = array(
            'id'  => $id,
            'ttl' => is_null($ttl) ? NULL : ('~' === $ttl ? NULL : $ttl),
            'bin' => is_null($bin) ? NULL : ('~' === $bin ? NULL : $bin),
        );
    }

    if (isset($options['cache_id'])) {
        $options['id'] = $options['cache_id'];
        unset($options['cache_id']);
    }

    foreach (array('callback', 'options') as $k) {
        if (!empty($kk) && isset($options[$k])) {
            $kk = $options[$k];
        }
    }

    return (new Cache($options, $callback, $arguments))->get();
}

/**
 * Usage
 *
 * // Lookup at /path/to/my_module/config/config.yml > webmaster
 * $webmaster_email = at_config('my_module')->get('webmaster');
 *
 * // Lookup at /path/to/my_module/config/templates.yml > email.notify
 * $mail_notify_template = at_config('my_module', 'templates')->get('email.notify');
 *
 * @param  string  $module    Module name
 * @param  string  $id        Config ID
 * @param  boolean $refresh   Build new cache
 * @return Config
 */
function at_config($module, $id = 'config', $refresh = FALSE)
{
    return at_container('config')->setModule($module)->setId($id);
}

/**
 * Shortcut to render to icon.
 */
function at_icon($name, $source = 'icon.fontawesome')
{
    try {
        return at_container($source)->get($name)->render();
    }
    catch (Exception $e) {
        return $e->getMessage();
    }
}
