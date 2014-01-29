<?php

namespace Drupal\at_base\Container;

/**
 * Class to replace array of tokens to real values.
 *
 * Usage 1:
 *     $tokens = ['@service'];
 *     $args = (new Argument_Resolver)->prepareItems($tokens);
 *
 * Usage 2:
 *     $tokens = ['calls' => [
 *         ['method', ['@service']]
 *     ]];
 *     $args = (new Argument_Resolver)->prepareItemsPartial($tokens['calls'], 1);
 *
 * @todo  Test me
 */
class Argument_Resolver
{

    public function prepareItemsPartial($items, $part = 0) {
        foreach ($items as $k => $item) {
            if (is_array($item) && isset($item[$part])) {
                $items[$k][$part] = $this->prepareItem($item[$part]);
            }
        }
        return $items;
    }

    public function prepareItems($items) {
        foreach ($items as $k => $item) {
            $items[$k] = $this->prepareItem($item);
        }
        return $items;
    }

    public function prepareItem($item) {
        if (is_array($item)) {
            return $this->prepareItems($item);
        }

        if (is_string($item)) {
            return $this->replaceItem($item);
        }

        return $item;
    }

    private function replaceItem($item) {
        foreach (get_class_methods(get_class($this)) as $method) {
          if ('detect' === substr($method, 0, 6)) {
            if ($return = $this->{$method}($item)) {
              return $return;
            }
          }
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
