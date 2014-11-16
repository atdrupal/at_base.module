<?php

namespace Drupal\at_base\Hook;

class BlockView
{

    private $module;
    private $key;
    private $is_dynamic;
    private static $dynamic_data = array();

    public function __construct($delta)
    {
        $this->is_dynamic = strpos($delta, 'dyn_') === 0;

        if ($this->is_dynamic) {
            $this->key = substr($delta, 4);
        }
        else {
            list($module, $key) = explode('|', $delta);
            $this->module = $module;
            $this->key = $key;
        }
    }

    public function view()
    {
        $info = $this->getInfo();
        $render = at_container('helper.ContentRender');

        $block = array();
        foreach (array('subject', 'content') as $k) {
            try {
                $block[$k] = $render->render($info[$k]);
            }
            catch (\Exception $e) {
                $block[$k] = $e->getMessage();
            }
        }

        return $block;
    }

    private static function getInfo()
    {
        if ($this->is_dynamic) {
            return $this->getDynamicInfo();
        }

        $info = at_config($this->module, 'blocks')->get('blocks');
        if (!isset($info[$this->key])) {
            throw new \Exception("Invalid block: {$this->module}:{$this->key}");
        }
        return $info[$this->key];
    }

    private function getDynamicInfo()
    {
        return isset(self::$dynamic_data[$this->key]) ? self::$dynamic_data[$this->key] : array();
    }

    public static function setDynamicData($key, $value)
    {
        self::$dynamic_data[$key] = $value;
    }

}
