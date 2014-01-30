<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class CommonTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Basic features') + parent::getInfo();
  }

  /**
   * Autoload feature
   */
  public function testAutoloader() {
    $this->assertTrue(class_exists('Drupal\at_base\Cache\Warming\Warmer'));
    $this->assertTrue(class_exists('Drupal\at_base\Container'));
  }

  /**
   * Test for at_id() function.
   */
  public function testAtId() {
    at_id(new \Drupal\at_base\Autoloader())->register();
    $this->assertTrue(TRUE, 'No exception raised.');
  }

  /**
   * Test for \Drupal\at_base\Helper\RealPath class
   */
  public function testRealPath() {
    $helper = at_container('helper.real_path');

    // @module
    $this->assertEqual(
      drupal_get_path('module', 'at_base') . '/at_base.module',
      $helper->get('@at_base/at_base.module')
    );

    // %theme
    $this->assertEqual(
      path_to_theme() . '/templates/page.home.html.twig',
      $helper->get('%theme/templates/page.home.html.twig')
    );

    // %library
    $this->assertEqual(
      at_library('pimple') . '/lib/Pimple.php',
      $helper->get('%pimple/lib/Pimple.php')
    );
  }

  /**
   * Test ExpressionLanguage.
   */
  public function testExpressionLanguage() {
    $expected = 'Symfony\Component\ExpressionLanguage\ExpressionLanguage';
    $actual = get_class(at_container('expression_language'));
    $this->assertEqual($expected, $actual);

    $expected = 3;
    $actual = at_container('expression_language')->evaluate("constant('MENU_CONTEXT_PAGE') | constant('MENU_CONTEXT_INLINE')");
    $this->assertEqual($expected, $actual);
  }
}
