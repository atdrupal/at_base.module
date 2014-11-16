<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;
use Drupal\at_base\Helper\Test\Cache;

class BreadcrumbTest extends UnitTestCase
{

    private $api;

    public static function getInfo()
    {
        return array('name' => 'AT Unit: Breadcrumb API') + parent::getInfo();
    }

    public function setUp()
    {
        parent::setUp();
        $this->api = at_container('breadcrumb_api');
    }

    protected function setUpModules()
    {
        parent::setUpModules();

        // Fake at_modules('at_base', 'breadcrumb');
        at_container('wrapper.cache')->set('atmodules:at_base:breadcrumb', array('atest_base'), 'cache_bootstrap');

        // Fake entity_bundle(), token_replace(), l() functions
        at_fn_fake('entity_bundle', function($type, $entity) {
            return $entity->type;
        });
        at_fn_fake('token_replace', function($input) {
            return $input;
        });
        at_fn_fake('drupal_get_path_alias', function($input) {
            return $input;
        });
        at_fn_fake('l', function($text, $url) {
            return '<a href="/' . $url . '">' . $text . '</a>';
        });
    }

    public function testNodeStatic()
    {
        $node = (object) array('type' => 'page', 'nid' => 1, 'title' => 'Test page', 'status' => 1);

        // Direct set without hook_entity_view() implementation
        if ($config = $this->api->fetchEntityConfig($node, 'node', 'full', 'und')) {
            $this->api->set($config);
        }

        $this->api->pageBuild();

        $bc = drupal_set_breadcrumb();
        $this->assertEqual(at_fn('l', 'Home', 'home'), $bc[0]);
    }

    public function testPath()
    {
        // Current path is /test/path/foo
        // Active breadcrumb should be 'test/path/foo'
        at_fn_fake('request_path', function() {
            return 'test/path/foo';
        });
        $this->api->pageBuild();
        $bc = drupal_set_breadcrumb();
        $this->assertEqual(at_fn('l', 'Foo 1', 'foo/1'), $bc[0]);
        $this->assertEqual(at_fn('l', 'Foo 2', 'foo/2'), $bc[1]);

        // Current path is /test/path/wildcard
        // Active breadcrumb should be 'test/path/*'
        at_fn_fake('request_path', function() {
            return 'test/path/wildcard';
        });
        $this->api->pageBuild();
        $bc = drupal_set_breadcrumb();
        $this->assertEqual(at_fn('l', 'Wildcard 1', 'wildcard/1'), $bc[0]);
        $this->assertEqual(at_fn('l', 'Wildcard 2', 'wildcard/2'), $bc[1]);

        // Current path is /test/path/bar
        // Active breadcrumb should not be 'test/path/bar', per weight of it is
        // lower priority then 'test/path/*'
        at_fn_fake('request_path', function() {
            return 'test/path/bar';
        });
        $this->api->pageBuild();
        $bc = drupal_set_breadcrumb();
        $this->assertEqual(at_fn('l', 'Wildcard 1', 'wildcard/1'), $bc[0]);
        $this->assertEqual(at_fn('l', 'Wildcard 2', 'wildcard/2'), $bc[1]);
    }

}
