<?php

namespace Drupal\at_base\HookImplementation;

use Drupal\at_base\HookImplementation\HookMenu;

class Implementation
{

    /** @var HookImplementation */
    private $hookMenu;

    /** @var HookFlushCache */
    private $hookFlushCache;

    /** @var HookBlockInfo */
    private $hookBlockInfo;

    public function getHookMenu()
    {
        if (NULL === $this->hookMenu) {
            $this->hookMenu = new HookMenu();
        }
        return $this->hookMenu;
    }

    public function getHookFlushCache()
    {
        if (NULL === $this->hookFlushCache) {
            $this->hookFlushCache = new HookFlushCache();
        }
        return $this->hookFlushCache;
    }

    public function getHookBlockInfo()
    {
        if (NULL === $this->hookBlockInfo) {
            $this->hookBlockInfo = new HookBlockInfo();
        }
        return $this->hookBlockInfo;
    }

    public function getHookBlockView($delta)
    {
        return new HookBlockView($delta);
    }

    public function getHookPageBuild(&$page, $blocks)
    {
        return new HookPageBuild($page, $blocks);
    }

    public function getHookEntityViewAlter(&$build, $entity_type)
    {
        return new HookEntityViewAlter($build, $entity_type);
    }

    public function setHookMenu($hookMenu)
    {
        $this->hookMenu = $hookMenu;
        return $this;
    }

    public function setHookFlushCache(HookFlushCache $hookFlushCache)
    {
        $this->hookFlushCache = $hookFlushCache;
        return $this;
    }

    public function setHookBlockInfo(HookBlockInfo $hookBlockInfo)
    {
        $this->hookBlockInfo = $hookBlockInfo;
        return $this;
    }

}
