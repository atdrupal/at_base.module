<?php

namespace Drupal\at_base\HookImplementation;

class HookBlockView
{

    private $module;
    private $key;
    private $isDynamic;
    private static $dynamicData = array();

    public function __construct($delta)
    {
        $this->isDynamic = strpos($delta, 'dyn_') === 0;

        if ($this->isDynamic) {
            $this->key = substr($delta, 4);
        }
        else {
            list($module, $key) = explode('|', $delta);
            $this->module = $module;
            $this->key = $key;
        }
    }

    public function execute()
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

    private function getInfo()
    {
        if ($this->isDynamic) {
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
        return isset(self::$dynamicData[$this->key]) ? self::$dynamicData[$this->key] : array();
    }

    public static function setDynamicData($key, $value)
    {
        self::$dynamicData[$key] = $value;
    }

}
