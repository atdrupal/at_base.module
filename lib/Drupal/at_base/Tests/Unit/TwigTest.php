<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

/**
 * drush test-run --dirty 'Drupal\at_base\Tests\Unit\TwigTest'
 */
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

  public function testLazyFiltersFunctions() {
    $twig = at_container('twig_string');

    // Use trim() function
    $this->assertEqual($twig->render("{{  '  Drupal 7  '|fn__trim  }}"),  'Drupal 7');
    $this->assertEqual($twig->render("{{  fn__trim('  Drupal 7  ')  }}"), 'Drupal 7');

    // Use At_Base_Test_Class::helloStatic()
    $this->assertEqual($twig->render("{{  'Drupal 8'|At_Base_Test_Class__class__helloStatic  }}"),  'Hello Drupal 8');
    $this->assertEqual($twig->render("{{  At_Base_Test_Class__class__helloStatic('Drupal 8')  }}"), 'Hello Drupal 8');

    // Use At_Base_Test_Class::helloProperty()
    $this->assertEqual($twig->render("{{  'PHP'|At_Base_Test_Class__obj__helloProperty  }}"), 'Hello PHP');
    $this->assertEqual($twig->render("{{  At_Base_Test_Class__obj__helloProperty('PHP')  }}"), 'Hello PHP');

    // Namespace
    $this->assertEqual($twig->render("{{  'Namespace'|ns_Drupal__atest_base__Service_1__class__helloStatic  }}"),  'Hello Namespace');
    $this->assertEqual($twig->render("{{  ns_Drupal__atest_base__Service_1__class__helloStatic('Namespace')  }}"), 'Hello Namespace');
  }

  public function testTwigStringLoader() {
    $output = \AT::twig_string()->render('Hello {{ name }}', array('name' => 'Andy Truong'));
    $this->assertEqual('Hello Andy Truong', $output, 'Template string is rendered correctly.');
  }

  public function testCacheFilter() {
    $twig = at_container('twig_string');

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
      $actual = "string_{$i}";
      $actual = $twig->render($$actual);
      $actual = trim($actual);
      $this->assertEqual('Hello Andy Truong', $actual);
    }
  }

  public function testFilters() {
    $twig = at_container('twig_string');

    $expected = 'Hello Drupal';

    $this->assertEqual($expected, $twig->render("{{ atest_1('Drupal') }}"));
    $this->assertEqual($expected, $twig->render("{{ atest_2('Drupal') }}"));
    $this->assertEqual($expected, $twig->render("{{ atest_3('Drupal') }}"));
  }
}
