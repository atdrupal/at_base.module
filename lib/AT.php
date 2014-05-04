<?php

use Drupal\at_base\Autoloader;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class for type hint.
 */
class AT {
  /**
   * Factory method to get container.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  public static function getContainer() {
    return at_container();
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

  /**
   * Get content render service.
   *
   * @return Drupal\at_base\Helper\ContentRender
   */
  public static function getRender() {
    return at_container('helper.content_render');
  }
}
