<?php

namespace Drupal\at_base\Tests\Web;

/**
 * drush test-run --dirty 'Drupal\at_base\Tests\Web\TwigTest'
 */
class TwigTest extends \DrupalWebTestCase
{

    public static function getInfo()
    {
        return array(
            'name'        => 'AT Base: Twig Service',
            'description' => 'Test Twig service',
            'group'       => 'AT Web'
        );
    }

    public function setUp()
    {
        $this->profile = 'testing';
        parent::setUp('atest_base');
    }

    public function testTwigFilters()
    {
        $output = at_container('twig_string')->render("{{ 'user:1'|drupalEntity }}");
        $this->assertTrue(strpos($output, 'History'), 'Found text "History"');
        $this->assertTrue(strpos($output, 'Member for'), 'Found text: "Member for"');

        $output = "{% set o = { template: '@atest_base/templates/entity/user.html.twig' } %}";
        $output .= "{{ 'user:1'|drupalEntity(o) }}";
        $output = @at_container('twig_string')->render($output);
        $this->assertTrue(strpos($output, 'History'), 'Found text "History"');
        $this->assertTrue(strpos($output, 'Member for'), 'Found text: "Member for"');
        $this->assertTrue(strpos($output, '@atest_base/templates/entity/user.html.twig'), 'Found text: path to template');
    }

    /**
     * Test easy block definition.
     */
    public function testEasyBlocks()
    {
        $block_1 = at_container('twig_string')->render("{{ 'atest_base:hi_s'  | drupalBlock(TRUE) }}");
        $block_2 = at_container('twig_string')->render("{{ 'atest_base:hi_t'  | drupalBlock(TRUE) }}");
        $block_3 = at_container('twig_string')->render("{{ 'atest_base:hi_ts' | drupalBlock(TRUE) }}");

        $expected = 'Hello Andy Truong';
        $this->assertEqual($expected, trim($block_1));
        $this->assertEqual($expected, trim($block_2));
        $this->assertEqual($expected, trim($block_3));
    }

    public function testDrupalView()
    {
        $twig = at_container('twig_string');

        $output = $twig->render("{{ 'atest_theming_user'|drupalView('default', 1) }}");
        $this->assertTrue(strpos($output, 'views-field views-field-name') !== FALSE);

        $output = $twig->render("{{ 'atest_theming_user'|drupalView({arguments: [1]}) }}");
        $this->assertTrue(strpos($output, 'views-field views-field-name') !== FALSE);

        $output = $twig->render("{{ 'atest_theming_user'|drupalView('default', 11111) }}");
        $this->assertTrue(strpos($output, 'views-field views-field-name') === FALSE);

        $output = $twig->render("{{ 'atest_theming_user'|drupalView({arguments: [11111]}) }}");
        $this->assertTrue(strpos($output, 'views-field views-field-name') === FALSE);
    }

}

// class At_Base_Cache_Views_Warmer extends DrupalWebTestCase {
//   public static function getInfo() {
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
