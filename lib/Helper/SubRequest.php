<?php
namespace Drupal\at_base\Helper;

class SubRequest {
  private $path;
  private $original_path;

  public function __construct($path) {
    $this->path = $path;
    $this->original_path = $_GET['q'];
    $_GET['q'] = $path;
  }

  public function __destruct() {
    $_GET['q'] = $this->original_path;
  }

  public function request() {
    return menu_execute_active_handler($this->path, $deliver = FALSE);
  }
}
