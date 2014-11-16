<?php

namespace Drupal\at_base\Helper;

/**
 * @see at_modules()
 */
class ModulesFetcher
{

    /** @var string */
    private $baseModule;

    /** @var string */
    private $configFile;

    /**
     * @param string $baseModule
     * @param string $configFile
     */
    public function __construct($baseModule, $configFile)
    {
        $this->baseModule = $baseModule;
        $this->configFile = $configFile;
    }

    public function fetch($enabledModules)
    {
        $modules = array();

        foreach ($enabledModules as $name => $info) {
            if ($this->validateModule($name, $info->info)) {
                $modules[] = $name;
            }
        }

        return $modules;
    }

    private function validateModule($name, $info)
    {
        if (empty($info['dependencies'])) {
            return FALSE;
        }

        if (!in_array($this->baseModule, $info['dependencies'])) {
            return FALSE;
        }

        // Do no need checking config file
        if (empty($this->configFile)) {
            return TRUE;
        }

        // Config file is available
        $file = DRUPAL_ROOT . '/' . drupal_get_path('module', $name) . '/config/' . $this->configFile . '.yml';

        return is_file($file);
    }

}
