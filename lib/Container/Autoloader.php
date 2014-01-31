<?php

namespace Drupal\at_base\Container;

/**
 * Help to find service defintions, convert them to real object.
 */
class Autoloader
{
    private $def;

    public function setDefinition($def) {
        if ($this->validateDefinition($def)) {
            $def['namespaceSeparator'] = isset($def['namespaceSeparator']) ? $def['namespaceSeparator'] : '\\';
            $def['fileExtension'] = isset($def['fileExtension']) ? $def['namespaceSeparator'] : '.php';
            $def['includePath'] = DRUPAL_ROOT . '/' . at_container('helper.real_path')->get($def['includePath']);
            $this->def = $def;
        }
    }

    private function validateDefinition($def) {
        if (!isset($def['type'])) {
            throw new \Exception("Missing autoload type (psr-0 or psr-4)");
        }

        if (!isset($def['namespace'])) {
            throw new \Exception("Missing namespace.");
        }

        if (!isset($def['includePath'])) {
            throw new \Exception("Missing includePath.");
        }

        return TRUE;
    }

    public function register($def) {
        $this->setDefinition($def);

        switch ($this->def['type']) {
            case 'psr-0':
                $this->registerPSR0();
                break;

            case 'psr-4':
                $this->registerPSR4();
                break;
        }
    }

    public function registerPSR0() {
        $class_loader = at_container('autoloader.psr0', $this->def['namespace'], $this->def['includePath']);
        $class_loader->setNamespaceSeparator($this->def['namespaceSeparator']);
        $class_loader->setFileExtension($this->def['fileExtension']);
        $class_loader->register();
    }

    public function registerPSR4() {
        $class_loader = new \Drupal\at_base\Autoloader($this->def['namespace'], $this->def['includePath']);
        $class_loader->register(FALSE, FALSE);
        #drush_print_r($this->def);
        #drush_print_r(spl_autoload_functions());
        #exit;
    }
}
