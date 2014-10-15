<?php

namespace Drupal\at_base\Icon;

class Icon {

  protected $tag;
  protected $class;
  protected $text;

  public function __construct($class = '', $tag = 'i', $text = '') {
    $this->class = $class;
    $this->tag = $tag;
    $this->text = $text;
  }

  public function render() {
    if (empty($this->class)) {
      return '';
    }

    return "<{$this->tag} class=\"{$this->class}\">{$this->text}</$this->tag>";
  }

}
