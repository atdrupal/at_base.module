<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 *  drush test-run --dirty 'Drupal\at_base\Tests\Unit\CommonTest'
 */
class CommonTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Basic features') + parent::getInfo();
  }

  /**
   * Test for at_id() function.
   */
  public function testAtId() {
    $container = new \Drupal\at_base\Container();
    $this->assertTrue(TRUE, 'No exception raised.');
  }

  /**
   * Test for \Drupal\at_base\Helper\RealPath class
   */
  public function testRealPath() {
    $helper = at_container('helper.real_path');

    \at_fake::drupal_get_path(function($type, $name) {
      return "sites/all/modules/at_base";
    });

    // @module
    $this->assertEqual(
      \at_fn::drupal_get_path('module', 'at_base') . '/at_base.module',
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
    $engine = \AT::getExpressionLanguage();

    $expected = 'Symfony\Component\ExpressionLanguage\ExpressionLanguage';
    $actual = get_class($engine);
    $this->assertEqual($expected, $actual);

    $expected = 3;
    $actual = $engine->evaluate("constant('MENU_CONTEXT_PAGE') | constant('MENU_CONTEXT_INLINE')");
    $this->assertEqual($expected, $actual);
  }

  /**
   * Test at_fn()
   */
  public function testAtFn() {
    // Fake the function
    $GLOBALS['conf']['atfn:entity_bundle'] = function($type, $entity) { return $entity->type; };

    // Make sure the fake function is executed
    $this->assertEqual('page', at_fn('entity_bundle', 'node', (object)array('type' => 'page')));
  }
}
