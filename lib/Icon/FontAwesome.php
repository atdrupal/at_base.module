<?php
namespace Drupal\at_base\Icon;

class FontAwesome implements IconInterface {

  public function generate($css_code) {
    static $libraries_added = FALSE;

    if (!$libraries_added) {
      drupal_add_css(at_library('fontawesome', NULL, FALSE) . 'css/font-awesome.css');
      $libraries_added = TRUE;
    }

    return '<i class="fa '. $css_code .'"></i>';
  }
}
