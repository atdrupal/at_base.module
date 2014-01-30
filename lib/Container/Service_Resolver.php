<?php

namespace Drupal\at_base\Container;

/**
 * Help to find service defintions, convert them to real object.
 */
class Service_Resolver
{
    /**
     * Generate closure which to be used to fetch the service.
     *
     * @param string $id
     * @return \Closure
     */
    public function getClosure($id) {
        $def = $this->getDefinition($id);

        return function($c) use ($def) {
            $ar = $c['argument.resolver'];
            $sr = $c['service.resolver'];

            $args = $calls = array();

            if (!empty($def['file'])) {
                require at_container('helper.real_path')->get($def['file']);
                unset($def['file']);
            }

            if (!empty($def['arguments'])) {
                $args = $ar->prepareItems($def['arguments']);
                unset($def['arguments']);
            }

            if (!empty($def['calls'])) {
                $calls = $ar->prepareItemsPartial($def['calls'], 1);
                unset($def['calls']);
            }

            return $sr->convertDefinitionToService($def, $args, $calls);
        };
    }

    /**
     * Get service definition in configuration files.
     *
     * @param string $id
     */
    private function getDefinition($id) {
        if (!$def = at_container('helper.config_fetcher')->getItem('at_base', 'services', 'services', $id, TRUE)) {
            throw new \Exception("Missing service: {$id}");
        }

        $def['arguments'] = !empty($def['arguments']) ? $def['arguments'] : array();

        // A service depends on others, this method to resolve them.
        foreach (array('arguments', 'calls', 'factory_service') as $k) {
            if (isset($def[$k])) {
                $this->resolveDependencies($def[$k]);
            }
        }

        return $def;
    }

    /**
     * Resolve array of dependencies.
     *
     * @see self::resolve()
     */
    private function resolveDependencies($array)
    {
        $array = is_string($array) ? array($array) : $array;

        foreach ($array as $id) {
            if (is_array($id)) {
                $this->resolveDependencies($id);
            }
            elseif (is_string($id) && '@' === substr($id, 0, 1)) {
                at_container(substr($id, 1));
            }
        }
    }

    /**
     * Init service object from definition.
     *
     * @param array $def
     * @param array $args
     * @return object
     */
    public function convertDefinitionToService($def, $args = array(), $calls = array()) {
        if (!empty($def['factory_service'])) {
            return call_user_func_array(
              array(at_container($def['factory_service']), $def['factory_method']), $args
            );
        }

        if (!empty($def['factory_class'])) {
            $service = call_user_func_array(array(new $def['factory_class'], $def['factory_method']), $args);
        }
        else {
            $service = at_newv($def['class'], $args);
        }

        if (!empty($calls)) {
            foreach ($calls as $call) {
                list($method, $params) = $call;
                call_user_func_array(array($service, $method), $params);
            }
        }

        return $service;
    }

    /**
     * Get services definitions those are tagged with specific tag.
     *
     * @param string $tag
     * @return array
     */
    public function fetchDefinitions($tag)
    {
        $tagged_defs = array();

        $defs = at_container('helper.config_fetcher')->getItems('at_base', 'services', 'services', TRUE);
        foreach ($defs as $name => $def) {
            if (empty($def['tags'])) {
                continue;
            }

            foreach ($def['tags'] as $_tag) {
                if ($tag === $_tag['name']) {
                    $tagged_defs[] = $name;
                    break;
                }
            }
        }

        uasort($tagged_defs, 'drupal_sort_weight');

        return $tagged_defs;
    }
}
