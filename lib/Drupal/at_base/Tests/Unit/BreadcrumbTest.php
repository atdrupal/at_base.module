<?php

namespace Drupal\at_base\Tests\Unit;

use Drupal\at_base\Helper\Test\UnitTestCase;
use Drupal\at_base\Helper\Test\Cache;

class BreadcrumbTest extends UnitTestCase {
  public function getInfo() {
    return array('name' => 'AT Unit: Breadcrumb API') + parent::getInfo();
  }

  public function testNodeStatic() {
    $api = at_container('breadcrumb_api');

    $node = new \stdClass();
    $node->type = 'page';

    $config = array();
    $config['breadcrumbs']['entity']['node']['page']['full']['breadcrumbs'] = array(
      array('Home', 'home'),
    );

    // Direct set without hook_entity_view() implementation
    $api->checkEntityConfig($node, 'node', 'full', 'und');

    $page = array();
    at_base_page_build($page);

    $bc = drupal_set_breadcrumb();
    $this->assertEqual('Home', $bc[0][0]);
    $this->assertEqual('/home', $bc[0][1]);
  }

  public function testPath() {
  }
}
