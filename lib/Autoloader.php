<?php

namespace Drupal\at_base;

at_id(new Autoloader('Drupal'))->register();

/**
 * Modified version of SplClassLoader (https://gist.github.com/jwage/221634)
 *
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/final-proposal
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 */
class Autoloader {
  private $_fileExtension = '.php';
  private $_namespace;
  private $_includePath;
  private $_namespaceSeparator = '\\';

  public function __construct($ns, $includePath = NULL) {
    $this->_namespace = $ns;
    $this->_includePath = $includePath ? $includePath : DRUPAL_ROOT;
  }

  public function register($throw = FALSE, $prepend = FALSE) {
    spl_autoload_register(
      array($this, $this->_namespace === 'Drupal' ? 'loadDrupalClass' : 'loadClass'),
      $throw,
      $prepend
    );
  }

  /**
   * Loads the given class or interface.
   *
   * @param string $className The name of the class to load.
   * @return void
   */
  public function loadClass($className) {
    if (null === $this->_namespace || $this->_namespace.$this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace.$this->_namespaceSeparator))) {
      $fileName = $namespace = '';
      if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
        $namespace = substr($className, 0, $lastNsPos);
        $namespace = substr($namespace, strlen($this->_namespace) + 1);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
      }
      $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
      require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
    }
  }

  /**
   * Method to load Drupal classes
   */
  public function loadDrupalClass($className) {
    $do_load = null === $this->_namespace;
    $do_load = $do_load || $this->_namespace.$this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace.$this->_namespaceSeparator));
    if (!$do_load) return;

    // No PSR-0
    if (FALSE !== strpos($className, $this->_namespaceSeparator . 'Tests' . $this->_namespaceSeparator)) return;

    // Find module name from the class
    $secondNamespaceSeparator = strpos($className, $this->_namespaceSeparator, 7);
    $module = substr($className, 7, $secondNamespaceSeparator - 7);

    // Remove Drupal\%module from class name
    $className = substr($className, $secondNamespaceSeparator + 1);
    $className = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $className);

    // Build file path
    // path = _includePath + _pathToModule + _namespaces + class + _extension
    $fileName  = $this->_includePath . DIRECTORY_SEPARATOR . drupal_get_path('module', $module) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
    $fileName .= $className . $this->_fileExtension;

    require $fileName;
  }

}
