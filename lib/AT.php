<?php

class AT {
  private static $container;

  public static function setContainer($container) {
    static::$container = $container;
  }

  public static function getContainer() {
    return static::$container;
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig() {
    return static::$container->get('twig');
  }

  /**
   * @return \Twig_Environment
   */
  public static function twig_string() {
    return static::$container->get('twig_string');
  }
}
