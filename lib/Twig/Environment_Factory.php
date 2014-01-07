<?php
namespace Drupal\at_base\Twig;

class Environment_Factory {
  private static $twig;
  private static $loader;
  private $options;

  public function getObject() {
    if (!self::$twig) {
      $this->options = array(
        'debug' => at_debug(),
        'auto_reload' => at_debug(),
        'autoescape' => FALSE,
        'cache' => variable_get('file_temporary_path', FALSE),
      );

      self::$twig = $this->fetchService();
    }

    return self::$twig;
  }

  public function getFileService($twig) {
    $service = clone $twig;
    $service->setLoader($this->getFileLoader());
    return $service;
  }

  public function getStringService($twig) {
    $service = clone $twig;
    $service->setLoader(new \Twig_Loader_String());
    return $service;
  }

  private function fetchService() {
    require_once at_library('twig') . '/lib/Twig/Autoloader.php';

    \Twig_Autoloader::register();

    $twig = new \Twig_Environment(NULL, $this->options);

    // Extension
    if (at_debug()) {
      $twig->addExtension(new \Twig_Extension_Debug());
    }

    // Filters
    foreach (self::getFilters() as $filter) {
      $twig->addFilter($filter);
    }

    // Functions
    foreach (self::getFunctions() as $function) {
      $twig->addFunction($function);
    }

    return $twig;
  }

  private function getFileLoader() {
    if (!self::$loader) {
      self::$loader = $this->fetchFileLoader();
    }

    return self::$loader;
  }

  /**
   * @todo cache me
   */
  private function fetchFileLoader() {
    $loader = new \Twig_Loader_Filesystem(DRUPAL_ROOT);

    // Add @module shortcuts
    foreach (array('at_base' => 'at_base') + at_modules('at_base') as $module_name) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module_name);
      if (is_dir($dir)) {
        $loader->addPath($dir, $module_name);
      }
    }

    return $loader;
  }

  private function getFilters() {
    $options['cache_id'] = 'at_theming:twig:filters';
    return at_cache($options, function(){
      return at_id(new \Drupal\at_base\Twig\Filters())->get();
    });
  }

  private function getFunctions() {
    $options['cache_id'] = 'at_theming:twig:functions';
    return at_cache($options, function(){
      return at_id(new \Drupal\at_base\Twig\Functions())->get();
    });
  }
}
