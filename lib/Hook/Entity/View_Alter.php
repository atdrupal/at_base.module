<?php
namespace Drupal\at_base\Hook\Entity;

/**
 * This is helper class allow modules to use Twig template for rendering entity.
 *
 * To use this, in YOURMODULE, implements hook_entity_view_alter():
 *
 * @code
 * function YOURMODULE_entity_view_alter(&$build, $entity_type) {
 *   at_id(new \Drupal\at_base\Hook\Entity\View_Alter($build, $entity_type))
 *     ->execute();
 * }
 * @code
 *
 * Configure entity template to be used:
 *
 * @code
 * # YOURMODULE/config/entity_template.yml
 * entity_templates:
 *   node:
 *     article:
 *       full:
 *         template: @YOURMODULE/templates/node/article-full.html.twig
 *         attached:
 *           css:
 *             - @YOURMODULE/misc/css/entity.node.full.css
 * @code
 *
 * The configuration is cached, we need flush cache every time entity template
 * config file get updated.
 *
 * @todo  Test me
 */
class View_Alter {
  private $build;
  private $entity_type;
  private $bundle;
  private $view_mode;

  public function __construct(&$build, $entity_type) {
    $this->build = &$build;
    $this->entity_type = $entity_type;
    $this->bundle = $build['#bundle'];
    $this->view_mode = $build['#view_mode'];
  }

  public function execute() {
    if ($config = $this->getConfig()) {
      $config['variables'] = isset($config['variables']) ? $config['variables'] : array();
      $config['variables'] += array('build' => $this->build);
      $this->build = array(
        '#entity_type' => $this->entity_type,
        '#bundle' => $this->bundle,
        '#view_mode' => $this->view_mode,
        '#language' => $this->build['#language'],
        '#contextual_links ' => !empty($this->build['#contextual_links']) ? $this->build['#contextual_links'] : NULL,
        'at_base' => at_container('helper.content_render')->setData($config)->render(),
        '#build' => $this->build,
      );
    }
  }

  /**
   * Get cached render configuration for context.
   */
  public function getConfig() {
    $o['id'] = "at_theming:entity_template:{$this->entity_type}:{$this->bundle}:{$this->view_mode}";
    $o['ttl'] = '+ 1 year';
    return at_cache($o, array($this, 'fetchConfig'));
  }

  /**
   * Get cached render configuration for context.
   */
  public function fetchConfig() {
    foreach (at_modules('at_base', 'entity_template') as $module) {
      $config = at_config($module, 'entity_template')->get('entity_templates');
      if (!isset($config[$this->entity_type])) continue;

      $config = $config[$this->entity_type];

      if (isset($config[$this->bundle]))    $config = $config[$this->bundle];
      elseif (isset($config['all']))        $config = $config['all'];
      else continue;

      if (isset($config[$this->view_mode])) $config = $config[$this->view_mode];
      elseif (isset($config['all']))  $config = $config['all'];
      else continue;

      return $config;
    }
  }
}
