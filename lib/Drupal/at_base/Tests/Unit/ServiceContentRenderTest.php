<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;

class ServiceContentRenderTest extends UnitTestCase {
  public static function getInfo() {
    return array('name' => 'AT Unit: Test helper.content_render service') + parent::getInfo();
  }

  public function setUp() {
    parent::setUp();
    $this->render = at_container('helper.content_render');
  }

  public function testString() {
    $string = 'Hello Andy Truong';
    $output = $this->render->render($string);
    $this->assertEqual($string, $output);
  }

  public function testFunction() {
    $output = $this->render->render(array('function' => 'atest_base_hello'));
    $this->assertEqual('Hello Andy Truong', $output);
  }

  public function testStaticMethod() {
    $output = $this->render->render(array('function' => '\At_Base_Test_Class::helloStatic'));
    $this->assertEqual('Hello Andy Truong', $output);
  }

  public function testTemplateString() {
    $data = array();
    $data['template_string'] = 'Hello {{ name }}';
    $data['variables']['name'] = 'Andy Truong';
    $output = $this->render->render($data);
    $this->assertEqual('Hello Andy Truong', $output);
  }

  public function testTemplate() {
    $data = array();
    $data['template'] = '@atest_base/templates/block/hello_template.html.twig';
    $data['variables']['name'] = 'Andy Truong';
    $output = trim($this->render->render($data));
    $this->assertEqual('Hello Andy Truong', $output);
  }

  public function testDynamicVariables() {
    $data = array();

    $expected = 'Hello Andy Truong';
    $data['template_string'] = 'Hello {{ name }}';

    // Function
    $data['variables'] = 'atest_variables';
    $this->assertEqual($expected, $this->render->render($data));

    // Static call
    $data['variables'] = 'At_Base_Test_Class::staticGetVariables';
    $this->assertEqual($expected, $this->render->render($data));

    // object/method
    $obj = new \At_Base_Test_Class();
    $data['variables'] = array($obj, 'getVariables');
    $this->assertEqual($expected, $this->render->render($data));

    // Class/Method
    $data['variables'] = array('At_Base_Test_Class', 'staticGetVariables');
    $this->assertEqual($expected, $this->render->render($data));

    // getVariables method of controller class
    unset($data['variables']);
    $data['controller'] = array('At_Base_Test_Class', 'hi');
    $this->assertEqual($expected, $this->render->render($data));
  }

  /**
   * Conten render Support prefix, suffix
   * @return html
   */
  public function testRenderPrefix() {
    $data = array();
    $data['template']  = '@atest_base/templates/block/render_template.html.twig';
    $data['variables'] = array(
        'name'   => 'Drupal',
        'prefix' => '<div id="abc">',
        'suffix' => '</div>'
    );
    
    $output = strip_tags($this->render->render($data),'<div>');
    $this->assertEqual('<div id="abc">Drupal</div>', $output);
  }
}
