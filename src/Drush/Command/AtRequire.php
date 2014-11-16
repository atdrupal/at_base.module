<?php

namespace Drupal\at_base\Drush\Command;

use \Drupal\at_base\Drush\Command\AtRequire\DependencyFetcher;

class AtRequire
{

    private $module;

    public function __construct($module = 'all')
    {
        $this->module = $module;
    }

    public function execute()
    {
        $modules = array($this->module);

        if ($this->module === 'all') {
            $modules = array('at_base' => 'at_base') + at_modules('at_base', 'require');
        }

        foreach ($modules as $module) {
            $this->fetchDependencies($module);
        }
    }

    private function fetchDependencies($module)
    {
        $data = at_config($module, 'require')->get('projects');

        foreach ($data as $name => $info) {
            (new DependencyFetcher($name, $info))->fetch();
        }
    }

}
