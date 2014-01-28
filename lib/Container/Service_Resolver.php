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
            $args = !empty($def['arguments']) ? $def['arguments'] : array();

            // Make arguments are objects.
            foreach ($args as $k => $v) {
                if (is_string($v) && '@' === substr($v, 0, 1)) {
                    $args[$k] = $c[substr($v, 1)];
                }
            }

            return $c['service.resolver']->convertDefinitionToService($def, $args);
        };
    }

    /**
     * Get service definition in configuration files.
     * @param string $id
     */
    private function getDefinition($id) {
        $def = at_container('helper.config_fetcher')
                ->getItem('at_base', 'services', 'services', $id, TRUE);
        if (is_null($def)) {
            throw new \Exception("Missing service: {$id}");
        }
        return $this->resolve($def);
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

    private function resolve($def)
    {
        // A service depends on others, this method to resolve them.
        foreach (array('arguments', 'calls') as $k) {
            if (!empty($def[$k])) {
                $this->resolveDependencies($def[$k]);
            }
        }

        // Service has factory
        if (!empty($def['factory_service'])) {
            at_container($def['factory_service']);
        }

        return $def;
    }

    /**
     * Resolve array of dependencies.
     *
     * @see resolveDefinition()
     */
    private function resolveDependencies($array)
    {
        foreach ($array as $id) {
            if (is_array($id)) {
                $this->resolveDependencies($id);
            }

            if (!is_string($id) || '@' !== substr($id, 0, 1)) {
                continue;
            }

            at_container(substr($id, 1));
        }
    }

    /**
     * Init service object from definition.
     *
     * @param type $def
     * @param type $args
     * @return type
     */
    public function convertDefinitionToService($def, $args = array()) {
        if (!empty($def['factory_service'])) {
            return call_user_func_array(
              array(at_container($def['factory_service']), $def['factory_method']), $args
            );
        }

        if (!empty($def['factory_class'])) {
            return call_user_func_array(array(new $def['factory_class'], $def['factory_method']), $args);
        }

        return at_newv($def['class'], $args);
    }

}
