<?php

namespace Drupal\at_base\Route;
use Drupal\at_base\Helper\Content_Render;

class Controller
{

    /**
     * Content render
     *
     * @var Content_Render
     */
    protected $render;

    /**
     * Route definition.
     *
     * @var array
     */
    protected $route;

    /**
     * Menu item for request.
     *
     * @var array
     */
    protected $menu_item;

    /**
     * @param Content_Render $content_render
     * @param string $request_path Request path — Example: user/login
     */
    public function __construct($content_render, $request_path)
    {
        $this->render = $content_render;
        $this->menu_item = menu_get_item($request_path);
    }

    /**
     * Page callback for routes.
     *
     * @see RouteToMenu
     */
    public static function pageCallback() {
        $args = func_get_args();
        $route = array_shift($args);

        array_shift($route['page arguments']);
        $render = at_container('helper.content_render');

        return at_id(new self($render, filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING)))
                ->setRoute($route)
                ->execute();
    }

    /**
     * Drupal has parametter converting system, which convert %node with result
     * of node_load($context_nid). This method to convert parameter in part of
     * route definition to real object, which already converted by Drupal menu
     * system.
     *
     * @param array $array
     * @param int $position
     * @return array
     */
    protected function repairArguments($array, $position) {
        foreach ($array as $k => $v) {
            if (is_numeric($v) && $v == $position) {
                $array[$k] = $this->menu_item['map'][$position];
            }
        }
        return $array;
    }

    /**
     * Apply reoute definition to the controller.
     *
     * @param array $route
     * @return Controller
     */
    public function setRoute($route) {
        foreach (explode('/', $route['pattern']) as $position => $part) {
            if (strpos($part, '%') !== 0) {
                continue;
            }

            $part = substr($part, 1);
            $route['variables'][$part] = $this->menu_item['map'][$position];

            !empty($route['page arguments'])
                && ($route['page arguments'] = $this->repairArguments($route['page arguments'], $position));

            !empty($route['controller'][2])
                && ($route['controller'][2] = $this->repairArguments($route['controller'][2], $position));
        }

        $this->route = $route;

        return $this;
    }

    /**
     * Dispatch the controller.
     *
     * @return array
     */
    public function execute() {
        $this->prepareCache();
        $this->prepareFunctionCallback();
        $this->prepareContextBlocks();
        return $this->render->render($this->route);
    }

    protected function prepareCache() {
        // User want cache the page
        if (!empty($this->route['cache'])) {
            $this->render->setCacheHandler(new Cache_Handler());

            // Prepair the cache ID
            if (empty($this->route['cache']['id'])) {
                $this->route['cache']['id'] = 'atroute:' . $this->menu_item['tab_root_href'];
            }
        }
    }

    protected function prepareFunctionCallback() {
        if (!empty($this->route['function'])) {
            $this->route['arguments'] = $this->route['page arguments'];
            unset($this->route['page arguments']);
        }
    }

    protected function prepareContextBlocks() {
        global $theme;

        if (!empty($this->route['blocks'][$theme])) {
            at_container('container')->offsetSet('page.blocks', $this->route['blocks'][$theme]);
            unset($this->route['blocks'][$theme]);
        }
    }

}
