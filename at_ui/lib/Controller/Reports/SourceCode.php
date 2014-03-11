<?php
namespace Drupal\at_ui\Controller\Reports;

class SourceCode {
  private $base_path = 'admin/reports/documentation/at_base/source';
  private $module;
  private $path;

  public function __construct() {
    if (!empty($_GET['module']) && module_exists($_GET['module'])) {
      $this->module = $_GET['module'];
    }

    if (!empty($_GET['path'])) {
      $this->path = $_GET['path'];
    }
  }

  public function render() {
    if (is_null($this->module)) {
      return $this->renderIndex();
    }

    $path = DRUPAL_ROOT . '/' . trim(drupal_get_path('module', $this->module) . '/' . $this->path, '/');
    if (is_dir($path)) {
      return $this->renderModuleDir($path);
    }

    return $this->renderModuleFile($path);
  }

  private function renderIndex() {
    foreach (system_list('module_enabled') as $module => $module_info) {
      $name = l($module, $this->base_path, array('query' => array('module' => $module, 'path' => '/')));
      $path = './' . drupal_get_path('module', $module);
      $rows[] = array($name, $path);
    }

    return array(
      '#theme' => 'table',
      '#header' => array('Module', 'Directory'),
      '#rows' => $rows,
    );
  }

  private function formatFileSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    return round($bytes, $precision) . ' ' . $units[$pow];
  }

  private function renderModuleDir($dir) {
    $bc = drupal_get_breadcrumb();
    $bc[] = l('Source', $this->base_path);
    $bc[] = l($this->module . '.module', $this->base_path, array('query' => array('module' => $this->module)));
    if ($path = trim(dirname($this->path), '/')) {
      $bc[] = l($path, $this->base_path, array('query' => array('module' => $this->module, 'path' => dirname($this->path))));
    }
    drupal_set_breadcrumb($bc);

    foreach (scandir($dir) as $name) {
      if ($name === '.') { continue; }
      if ($name === '.DS_Store') { continue; }
      if ($name === '._.DS_Store') { continue; }

      $file   = "{$dir}/{$name}";
      if ($name === '..') {
        $_name  = l($name, "{$this->base_path}", array('query' => array('module' => $this->module, 'path' => dirname($this->path))));
      }
      else {
        $_name  = l($name, "{$this->base_path}", array('query' => array('module' => $this->module, 'path' => trim($this->path . '/' . $name, '/'))));
      }

      $_stats = stat($file);

      $rows[$file] = array(
        is_dir($file) ? "<strong>{$_name}/</strong>" : $_name,
        $_stats[4],
        $_stats[5],
        $this->formatFileSize($_stats[7]),
        format_date($_stats[9], 'short')
      );
    }

    uksort($rows, function($a, $b) {
      return is_dir($a) ? -1 : 1;
    });

    return array('#theme' => 'table',
      '#header' => array('Name', 'UID', 'GID', 'Size', 'Modified'),
      '#rows' => $rows
    );
  }

  private function renderModuleFile($file) {
    $bc = drupal_get_breadcrumb();
    $bc[] = l('Source', $this->base_path);
    $bc[] = l($this->module . '.module', $this->base_path, array('query' => array('module' => $this->module)));
    if ($path = trim(dirname($this->path), '/')) {
      $bc[] = l($path, $this->base_path, array('query' => array(
        'module' => $this->module, 'path' => dirname($this->path)
      )));
    }
    drupal_set_breadcrumb($bc);

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file);

    switch ($mime_type) {
      case 'text/x-php':
        $type = 'php'; break;

      case 'image/png':
      case 'image/jpg':
      case 'image/jpeg':
      case 'image/gif':
        return '<img src="'. $GLOBALS['base_path'] . drupal_get_path('module', $this->module) .'/'. trim($this->path, '/') .'" />';

      default:
        $type = 'unknown';
        break;
    }

    if ('unknown' === $type) {
      switch (pathinfo($file, PATHINFO_EXTENSION)) {
        case 'module':
        case 'install':
        case 'inc':
        case 'php':
          $type = 'php';
          break;

        case 'css':
        case 'scss':
        case 'less':
          $type = 'css';
          break;

        case 'js':
          $type = 'javascript';
          break;

        case 'twig':
          $type = 'twig';
          break;

        case 'yml':
        case 'yaml':
          $type = 'yaml';
          break;
      }
    }

    return drupal_get_form('at_ui_display_file', $file, $type);
  }
}
