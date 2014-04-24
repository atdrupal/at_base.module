<?php

namespace Drupal\at_base\Container;

/**
 * Class to replace array of tokens to real values.
 *
 * Usage 1:
 *     $tokens = ['@service'];
 *     $args = (new ArgumentResolver)->prepareItems($tokens);
 *
 * Usage 2:
 *     $tokens = ['calls' => [
 *         ['method', ['@service']]
 *     ]];
 *     $args = (new ArgumentResolver)->prepareItemsPartial($tokens['calls'], 1);
 *
 * @todo  Test me
 */
class ArgumentResolver
{

    public function resolve(&$def) {
        $args = $calls = array();

        if (!empty($def['file'])) {
            require at_container('helper.real_path')->get($def['file']);
            unset($def['file']);
        }

        if (!empty($def['autoload'])) {
            at_container('autoloader')->register($def['autoload']);
            unset($def['autoload']);
        }

        if (!empty($def['arguments'])) {
            $args = $this->prepareItems($def['arguments']);
            unset($def['arguments']);
        }

        if (!empty($def['calls'])) {
            $calls = $this->prepareItemsPartial($def['calls'], 1);
            unset($def['calls']);
        }

        return array($args, $calls);
    }

    public function prepareItemsPartial($items, $part = 0) {
        foreach ($items as $k => $item) {
            if (is_array($item) && isset($item[$part])) {
                $items[$k][$part] = $this->prepareItem($item[$part]);
            }
        }
        return $items;
    }

    public function prepareItems($items, $loop = TRUE) {
        foreach ($items as $k => $item) {
            $items[$k] = $this->prepareItem($item, $loop);
        }

        return $items;
    }

    public function prepareItem($item, $loop = TRUE) {
        if (is_array($item)) {
            if ($loop) {
                return $this->prepareItems($item, FALSE);
            }
        }
        elseif (is_string($item)) {
            return $this->replaceItem($item);
        }

        return $item;
    }

    /**
     * Replace item string to real object.
     *
     * @param  string $item
     */
    private function replaceItem($item) {
        foreach (get_class_methods(get_class($this)) as $method) {
          if ('detect' === substr($method, 0, 6)) {
            if ($return = $this->{$method}($item)) {
                return $this->prepareItem($return);
            }
          }
        }
        return $item;
    }

    /**
     * @param  string $item
     */
    private function detectConfig($item) {
        if ('%' === substr($item, 0, 1)) {
            list($module, $id, $key) = explode(':', substr($item, 1), 3);
            return at_config($module, $id)->get($key);
        }
    }

    /**
     * @param  string $item
     */
    private function detectService($item) {
        if ('@' === substr($item, 0, 1)) {
            return at_container(substr($item, 1));
        }
    }
}
