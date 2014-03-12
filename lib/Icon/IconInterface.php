<?php
namespace Drupal\at_base\Icon;

interface IconInterface {

  /**
   * Generate html from css code.
   *
   * @param string $css_code
   * @return string
   */
  public function generate($css_code);
}
