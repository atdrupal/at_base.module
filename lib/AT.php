<?php

class AT {
  private static $container;

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
    return static::getContainer()->get('twig');
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig_string() {
    return static::getContainer()->get('twig_string');
  }

  public function getExpressionLanguage() {
    static $engine;

    if (!$engine) {
      at_id(new Drupal\at_base\Autoloader('Symfony\Component\ExpressionLanguage', at_library('expression_language')))
        ->register();

      $engine = new \Symfony\Component\ExpressionLanguage\ExpressionLanguage();
    }

    return $engine;
  }
}
