<?php
namespace Drupal\at_base\Icon;

interface IconSourceInterface {

  /**
   * Get Icon instance with information to generate icon tag.
   *
   * @param string $css_code
   * @return \Drupal\at_base\Icon\Icon
   */
  public function get($css_code);

  /**
   * Get icon-sets.
   *
   * @return [type] [description]
   */
  public function getIconSets();

  public function getIconList($set_name);
}
