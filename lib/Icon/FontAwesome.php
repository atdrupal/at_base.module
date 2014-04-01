<?php
namespace Drupal\at_base\Icon;

class FontAwesome implements IconSourceInterface {
  public function getName() {
    return 'FontAwesome';
  }

  public function __construct() {
    \at_fn::drupal_add_css(at_library('fontawesome', NULL, FALSE) . 'css/font-awesome.css');
  }

  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @param type $name
   *   The name of icon in fontawesome.
   *   Browse available icons at http://fortawesome.github.io/Font-Awesome/icons/
   * @return \Drupal\at_base\Icon\Icon
   *   Contain enough information to generate icon tag.
   */
  public function get($name) {
    return new Icon($class = "fa {$name}");
  }

  public function getIconSets() {
    return array('default');
  }

  public function getIconList($set_name = 'default') {
    return array();
  }
}
