<?php

use Drupal\at_base\Autoloader;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class for type hint.
 */
class AT {
  protected static $container;

  /**
   * Factory method to get container.
   * @return \Drupal\at_base\Container
   */
  public static function getContainer() {
    if (!static::$container) {
      static::$container = new \Drupal\at_base\Container();
    }
    return static::$container;
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig() {
      return atcg('twig');
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig_string() {
    return atcg('twig_string');
  }

  /**
   * Get expression language object.
   *
   * @return ExpressionLanguage
   * @todo  Use service container when PSR-4 autoloading can be attached there.
   */
  public static function getExpressionLanguage() {
    static $engine;

    if (!$engine) {
      $engine = new ExpressionLanguage();
    }

    return $engine;
  }
}
