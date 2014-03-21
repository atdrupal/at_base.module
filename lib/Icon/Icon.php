<?php

namespace Drupal\at_base\Icon;

class Icon {

  protected $css;
  protected $tag = 'i';
  protected $class;
  protected $text;

  public function __construct($css, $tag, $class, $text) {
    $this->css = $css;
    $this->tag = $tag;
    $this->class = $class;
    $this->text = $text;
  }

  public function render() {
    if (!empty($this->css)) {
      foreach ($this->css as $key => $value) {
        \at_fn::drupal_add_css($value['data'], $value['options']);
      }
    }

    return "<{$this->tag} class=\"{$this->class}\">{$this->text}</$this->tag>";
  }

}
