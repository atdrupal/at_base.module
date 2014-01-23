<?php

namespace Drupal\at_base\Tests\Web;

/**
 * cache_get()/cache_set() does not work on unit test cases.
 */
class TwigTest extends \DrupalWebTestCase {
  public function getInfo() {
    return array(
      'name' => 'AT Base: Twig Service',
      'description' => 'Test Twig service',
      'group' => 'AT Base'
    );
  }

  public function setUp() {
    $this->profile = 'testing';
    parent::setUp('atest_base');
  }

  public function testTwigFilters() {
    $output = at_container('twig_string')->render("{{ 'user:1'|drupalEntity }}");
    $this->assertTrue(strpos($output, 'History'), 'Found text "History"');
    $this->assertTrue(strpos($output, 'Member for'), 'Found text: "Member for"');

    $output  = "{% set o = { template: '@atest_base/templates/entity/user.html.twig' } %}";
    $output .= "{{ 'user:1'|drupalEntity(o) }}";
    $output  = @at_container('twig_string')->render($output);
    $this->assertTrue(strpos($output, 'History'), 'Found text "History"');
    $this->assertTrue(strpos($output, 'Member for'), 'Found text: "Member for"');
    $this->assertTrue(strpos($output, '@atest_base/templates/entity/user.html.twig'), 'Found text: path to template');
  }

  /**
   * Test easy block definition.
   */
  public function testEasyBlocks() {
    $block_1 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_s'  | drupalBlock(TRUE) }}");
    $block_2 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_t'  | drupalBlock(TRUE) }}");
    $block_3 = \AT::twig_string()->render("{{ 'at_base:atest_base|hi_ts' | drupalBlock(TRUE) }}");

    $expected = 'Hello Andy Truong';
    $this->assertEqual($expected, trim($block_1));
    $this->assertEqual($expected, trim($block_2));
    $this->assertEqual($expected, trim($block_3));
  }
}

// class At_Base_Cache_Views_Warmer extends DrupalWebTestCase {
//   public function getInfo() {
//     return array(
//       'name' => 'AT Theming: AT Cache > Views-Cache warmer',
//       'description' => 'Try views-cache warmer of at_base.',
//       'group' => 'AT Theming',
//     );
//   }

//   protected function setUp() {
//     parent::setUp('atest_theming');
//   }

//   /**
//    * @todo test me.
//    */
//   public function testViewsCacheWarming() {
//     // Build the first time
//     // $output = at_id(new Drupal\at_base\Helper\SubRequest('atest_theming/users'))->request();
//     $output = views_embed_view('atest_theming_user', 'page_1');

//     // Invoke entity save event
//     $u = $this->drupalCreateUser();

//     // Build the second time
//     // $output = at_id(new Drupal\at_base\Helper\SubRequest('atest_theming/users'))->request();
//     $output = views_embed_view('atest_theming_user', 'page_1');
//     $this->assertTrue(FALSE !== strpos($output, $u->name), "Found {$u->name}");

//     $this->verbose(print_r(_cache_get_object('cache_views_data'), TRUE));
//   }
// }
