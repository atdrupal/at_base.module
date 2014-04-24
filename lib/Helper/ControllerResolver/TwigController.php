<?php
namespace Drupal\at_base\Helper\ControllerResolver;

class TwigController {
  private $template = '';
  private $arguments = array();
  private $twig;

  public function __construct($twig) {
    $this->twig = $twig;
  }

  public function setTemplate($template) {
    $this->template = $template;
  }

  public function setArguments($arguments) {
    $this->arguments = $arguments;
  }

  public function render() {
    return $this->twig
      ->render($this->template, $this->arguments);
  }
}
