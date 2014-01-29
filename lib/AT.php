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
      return at_container('twig');
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig_string() {
      return at_container('twig_string');
  }

  public function getExpressionLanguage() {
    static $engine;

    if (!$engine) {
      at_id(new Autoloader('Symfony\Component\ExpressionLanguage', at_library('expression_language')))
        ->register();

      $engine = new ExpressionLanguage();
    }

    return $engine;
  }
}

