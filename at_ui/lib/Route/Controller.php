<?php

namespace Drupal\at_ui\Route;

use Drupal\at_base\Helper\Content_Render;

/**
 * @todo Check permission
 *
 */
class Controller extends \Drupal\at_base\Route\Controller {
  private $access;

  public function __construct($content_render, $request_path) {
    $this->access = !empty($_GET['edit']);
    parent::__construct($content_render, $request_path);
  }

  public function checkAccess() {
    return $this->access;
  }

  public function execute() {
    if ($this->access) {
      $this->attachAssets();
      $this->attachBlocks();
    }

    return parent::execute();
  }

  private function attachAssets() {
    at_ui_include_code_mirror(array(
      'mode/yaml/yaml.js',
      'mode/xml/xml.js',
      'mode/css/css.js',
      'mode/htmlmixed/htmlmixed.js',
    ));

    drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/route.edit.live.js');
    drupal_add_js(drupal_get_path('module', 'at_ui') . '/misc/js/route.edit.js');
    drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/cm.css');
    drupal_add_css(drupal_get_path('module', 'at_ui') . '/misc/css/route.edit.live.css');
    drupal_add_css(drupal_get_path('module', 'block') . '/block.css');
  }

  public function attachBlocks() {
    global $theme;

    $blocks = array();

    $all_regions = system_region_list($theme);
    $visible_regions = array_keys(system_region_list($theme, REGIONS_VISIBLE));

    foreach ($visible_regions as $region) {
      $blocks[$region]['atui_description'] = array(
        'delta' => "atui_{$region}_description",
        'subject' => "Region {$all_regions[$region]}",
        'content' => array(
          'content' => '<div class="block-region">%'. $region .'</div>'
        ),
        'weight' => -500,
      );
    }

    $blocks['content']['atui_description'] = array(
      'delta' => "atui_content_description",
      'subject' => "Region " . $all_regions['content'],
      'content' => array('function' => 'drupal_get_form', 'arguments' => array('at_ui_route_form', $this->route)),
      'weight' => -500,
    );

    at_container('container')->offsetSet('page.blocks', $blocks);
  }
}
