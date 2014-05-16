<?php
namespace Drupal\at_base\Twig;

class EnvironmentFactory {
  private static $twig;
  private static $loader;
  private static $options;

  /**
   * Factory for @twig.core
   *
   * Return \Twig_Environment
   */
  public static function getObject() {
    if (!self::$twig) {
      // Autoloading
      require_once at_library('twig') . '/lib/Twig/Autoloader.php';
      \Twig_Autoloader::register();

      self::$options = array(
        'debug' => at_debug(),
        'auto_reload' => at_debug(),
        'autoescape' => FALSE,
        'cache' => variable_get('file_temporary_path', FALSE),
      );

      // Init the object
      self::$twig = new \Twig_Environment(NULL, self::$options);
      self::$twig->addExtension(new \Drupal\at_base\Twig\Extension());
    }

    return self::$twig;
  }

  /**
   * Factory for @twig
   *
   * Return \Twig_Environment
   */
  public static function getFileService($twig) {
    return clone $twig;
  }

  /**
   * Factory for @twig.string
   *
   * Return \Twig_Environment
   */
  public static function getStringService($twig) {
    return clone $twig;
  }

  /**
   * Factory method for @twig.file_loader
   *
   * @return \Twig_Loader_Filesystem
   */
  public static function getFileLoader() {
    $root = DRUPAL_ROOT;

    return at_cache('atwig:file_loader, + 1 year', function() use ($root) {
      $loader = new \Twig_Loader_Filesystem($root);

      foreach (array('at_base' => 'at_base') + \at_fn::at_modules('at_base') as $module) {
        $dir = $root . '/' . drupal_get_path('module', $module);
        if (is_dir($dir)) {
          $loader->addPath($dir, $module);
        }
      }

      return $loader;
    });
  }
}
