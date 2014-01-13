<?php
namespace Drupal\at_base\Helper;

/**
 * Usage:
 * @code
 *   at_id(new Drupal\at_base\Helper\SubRequest('atest_theming/users'))->request();
 * @code
 */
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

  private function getItem() {
    return menu_get_item($this->path);
  }

  public function build() {
    if (!$item = $this->getItem()) {
      throw new \Exception('Page not found: '. $this->path);
    }

    if (empty($item['access'])) {
      throw new \Exception('Access denied: '. $this->path);
    }

    if ($item['include_file']) {
      require_once DRUPAL_ROOT . '/' . $item['include_file'];
    }

    return call_user_func_array($item['page_callback'], $item['page_arguments']);
  }

  public function request() {
    return menu_execute_active_handler($this->path, $deliver = FALSE);
  }
}
