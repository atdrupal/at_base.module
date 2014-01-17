<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class TwigTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Twig') + parent::getInfo();
  }

  public function testServiceContainer() {
    $this->assertEqual('Twig_Environment', get_class(at_container('twig')));
    $this->assertEqual('Twig_Environment', get_class(at_container('twig_string')));
  }

  public function testDefaultFilters() {
    $twig = at_container('twig');
    $filters = $twig->getFilters();

    $array = array('render', 't', 'url', '_filter_autop');
    $array = array_merge($array, array('drupalBlock', 'drupalEntity', 'drupalView'));
    $array = array_merge($array, array('at_config'));

    foreach ($array as $filter) {
      $this->assertTrue(isset($filters[$filter]), "Found filter {$filter}");
    }
  }

  public function testTwigStringLoader() {
    $output = \AT::twig_string()->render('Hello {{ name }}', array('name' => 'Andy Truong'));
    $this->assertEqual('Hello Andy Truong', $output, 'Template string is rendered correctly.');
  }

  public function testContentRender() {
    $render = at_container('helper.content_render');

    // Simple string
    $expected = 'Hello Andy Truong';
    $actual = $render->setData($expected)->render();
    $this->assertEqual($expected, $actual);

    // Template string
    $data['template_string'] = 'Hello {{ name }}';
    $data['variables']['name'] = 'Andy Truong';
    $output = $render->setData($data)->render();
    $this->assertEqual($expected, $actual);

    // Template
    $data['template'] = '@atest_base/templates/block/hello_template.html.twig';
    $data['variables']['name'] = 'Andy Truong';
    $output = $render->setData($data)->render();
    $assert = strpos($output, $actual) !== FALSE;
    $this->assertTrue($assert, "Found <strong>{$expected}</strong> in result.");
  }

  public function testCacheFilter() {
    $string_1  = "{% set options = { cache_id: 'atestTwigCache:1' } %}";
    $string_1 .= "\n {{ 'atest_base.service_1:hello' | cache(options) }}";
    $string_2  = "{% set options = { cache_id: 'atestTwigCache:2' } %}";
    $string_2 .= "\n {{ 'At_Base_Test_Class::helloStatic' | cache(options) }}";
    $string_3  = "{% set options = { cache_id: 'atestTwigCache:3' } %}";
    $string_3 .= "\n {{ 'atest_base_hello' | cache(options) }}";
    $string_4  = "{% set options  = { cache_id: 'atestTwigCache:4' } %}";
    $string_4 .= "\n {% set callback = { callback: 'atest_base_hello', arguments: ['Andy Truong'] } %}";
    $string_4 .= "\n {{ callback | cache(options) }}";
    for ($i = 1; $i <= 4; $i++) {
      $expected = 'Hello Andy Truong';
      $actual = "string_{$i}";
      $actual = at_container('twig_string')->render($$actual);
      $actual = trim($actual);
      $this->assertEqual($expected, $actual);
    }
  }
}
