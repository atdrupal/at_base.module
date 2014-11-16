<?php

namespace Drupal\at_base\HookImplementation;

use Drupal\at_base\Route\RouteToMenu;

class HookMenu
{

    /**
     * Get all menu items.
     */
    public function execute()
    {
        $items = array();
        foreach (at_modules('at_base', 'routes') as $module) {
            $items += $this->import($module);
        }
        return $items;
    }

    private function import($module)
    {
        $items = array();

        $data = at_config($module, 'routes', $refresh = TRUE)->get('routes');
        foreach ($data as $route_name => $route_data) {
            if ($item = at_id(new RouteToMenu($module, $route_name, $route_data))->convert($error)) {
                $items[$route_name] = $item;
            }
            else {
                $this->handleError($route_name, $error);
            }
        }

        return $items;
    }

    /**
     * Print error message.
     *
     * @param  string $route_name
     * @param  string $error
     */
    private function handleError($route_name, $error)
    {
        $msg = "Invalidate configuration for route `{$route_name}`. Error: {$error}";
        if (function_exists('drush_print_r')) {
            drush_print_r($msg);
        }
        elseif (user_access('administer site configuration')) {
            drupal_set_message($msg, 'error');
        }
    }

}
