<?php
namespace Drupal\at_base\Icon;

use Drupal\at_base\Icon;

class FontAwesome implements IconInterface {

  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @staticvar boolean $libraries_added
   * @param type $name
   *   The name of icon in fontawesome.
   *   Browse available icons at http://fortawesome.github.io/Font-Awesome/icons/
   * @return \Drupal\at_base\Icon
   *   Contain enough information to generate icon tag.
   */
  public function get($name) {
    $libraries_added = &drupal_static('fontawesome_library_added');

    $css = array();
    $tag = 'i';
    $class = 'fa ' . $name;
    $text = '';

    if (!$libraries_added) {
      $css[] = array(
        'data' => at_library('fontawesome', NULL, FALSE) . 'css/font-awesome.css',
        'options' => NULL,
      );
      $libraries_added = TRUE;
    }

    return new Icon($css, $tag, $class, $text);
  }
}
