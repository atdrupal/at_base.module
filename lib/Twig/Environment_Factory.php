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

    $twig->addExtension(new \Drupal\at_base\Twig\Extension());

    if (at_debug()) {
      $twig->addExtension(new \Twig_Extension_Debug());
    }

    return $twig;
  }

  private function getFileLoader() {
    if (!self::$loader) {
      self::$loader = at_cache('atwig:file_loader, + 1 year', array($this, 'fetchFileLoader'));
    }

    return self::$loader;
  }

  public function fetchFileLoader() {
    $loader = new \Twig_Loader_Filesystem(DRUPAL_ROOT);

    foreach (array('at_base' => 'at_base') + at_modules('at_base') as $module_name) {
      $dir = DRUPAL_ROOT . '/' . drupal_get_path('module', $module_name);
      if (is_dir($dir)) {
        $loader->addPath($dir, $module_name);
      }
    }

    return $loader;
  }
}
