<?php
namespace Drupal\at_base\Icon;

interface IconInterface {

  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @param string $css_code
   * @return \Drupal\at_base\Icon
   */
  public function get($css_code);
}
