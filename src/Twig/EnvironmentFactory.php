<?php

namespace Drupal\at_base\Twig;

class EnvironmentFactory
{

    private static $twig;
    private $options;

    /**
     * Factory for @twig.core
     *
     * Return \Twig_Environment
     */
    public function getObject()
    {
        if (!self::$twig) {
            $this->options = array(
                'debug'       => at_debug(),
                'auto_reload' => at_debug(),
                'autoescape'  => FALSE,
                'cache'       => variable_get('file_temporary_path', FALSE),
            );

            // Init the object
            self::$twig = new \Twig_Environment(NULL, $this->options);
            self::$twig->addExtension(new Extension());
        }

        return self::$twig;
    }

    /**
     * Factory for @twig
     *
     * Return \Twig_Environment
     */
    public function getFileService($twig)
    {
        return clone $twig;
    }

    /**
     * Factory for @twig.string
     *
     * Return \Twig_Environment
     */
    public function getStringService($twig)
    {
        return clone $twig;
    }

    /**
     * Factory method for @twig.file_loader
     * @return \Twig_Loader_Filesystem
     */
    public function getFileLoader()
    {
        return at_cache('atwig:file_loader, + 1 year', function() {
            $loader = new \Twig_Loader_Filesystem(DRUPAL_ROOT);
            foreach (array('at_base' => 'at_base') + at_modules('at_base') as $module) {
                $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module);
                if (is_dir($dir)) {
                    $loader->addPath($dir, $module);
                }
            }
            return $loader;
        });
    }

}
