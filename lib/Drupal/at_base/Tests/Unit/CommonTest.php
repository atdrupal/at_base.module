<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class CommonTest extends UnitTestCase
{

    public static function getInfo()
    {
        return array('name' => 'AT Unit: Basic features') + parent::getInfo();
    }

    /**
     * Test for \Drupal\at_base\Helper\RealPath class
     */
    public function testRealPath()
    {
        $helper = at_container()->get('helper.real_path');

        // @module
        $module_file = drupal_get_path('module', 'at_base') . '/at_base.module';
        $this->assertEqual($module_file, $helper->get('@at_base/at_base.module'));

        // %theme
        $theme_file = path_to_theme() . '/templates/page.home.html.twig';
        $this->assertEqual($theme_file, $helper->get('%theme/templates/page.home.html.twig'));
    }

    /**
     * Test ExpressionLanguage.
     */
    public function testExpressionLanguage()
    {
        $engine = at_container()->get('expression_language');
        $this->assertEqual('Symfony\Component\ExpressionLanguage\ExpressionLanguage', get_class($engine));
        $this->assertEqual(3, $engine->evaluate("constant('MENU_CONTEXT_PAGE') | constant('MENU_CONTEXT_INLINE')"));
    }

    /**
     * Test at_fn()
     */
    public function testAtFn()
    {
        // Fake the function
        $GLOBALS['conf']['atfn:entity_bundle'] = function($type, $entity) {
            return $entity->type;
        };

        // Make sure the fake function is executed
        $this->assertEqual('page', at_fn('entity_bundle', 'node', (object) array('type' => 'page')));
    }

}
