<?php

namespace Drupal\at_base\HookImplementation;

/**
 * This is helper class allow modules to use Twig template for rendering entity.
 *
 * To use this, in settings.php add this line:
 *
 *  define('AT_BASE_ENTITY_TEMPLATE', 1);
 *
 * Configure entity template to be used:
 *
 * @code
 * # YOURMODULE/config/entity_template.yml
 * entity_templates:
 *   node:
 *     article:
 *       full:
 *         template:
 *           - '%theme/templates/node/article-full.html.twig'
 *           - '@YOURMODULE/templates/node/article-full.html.twig'
 *           - '@YOURMODULE/template/%entiy_type/%bundle-%view_mode.html.twig'
 *         attached:
 *           css:
 *             - @YOURMODULE/misc/css/entity.node.full.css
 * @code
 *
 * The configuration is cached, we need flush cache every time entity template
 * config file get updated.
 *
 * @todo  Test me
 * @todo  Update wiki
 */
class HookEntityViewAlter
{

    protected $build;
    protected $entityType;
    protected $bundle;
    protected $id;
    protected $viewMode;

    public function __construct(&$build, $entity_type)
    {
        $entity = isset($build['#entity']) ? $build['#entity'] : $build['#' . $entity_type];
        $this->build = &$build;
        $this->entityType = $entity_type;
        $this->bundle = $build['#bundle'];
        $this->id = entity_id($entity_type, $entity);
        $this->viewMode = $build['#view_mode'];
    }

    protected function resolveTokens($template)
    {
        if (is_array($template)) {
            foreach ($template as $i => $_template) {
                $template[$i] = $this->resolveTokens($_template);
            }
            return $template;
        }

        return str_replace(
            array('%entity_type', '%entity_bundle', '%bundle', '%entity_id', '%id', '%mode', '%view_mode'), array($this->entityType, $this->bundle, $this->bundle, $this->id, $this->id, $this->viewMode, $this->viewMode), $template
        );
    }

    protected function build()
    {
        global $theme;

        if ($config = $this->getConfig()) {
            $config['variables'] = isset($config['variables']) ? $config['variables'] : array();
            $config['variables'] += array('build' => $this->build);

            // Support token in template
            if (!empty($config['template'])) {
                $config['template'] = $this->resolveTokens($config['template']);
            }

            // Attach block if context block is empty
            if (!empty($config['blocks'][$theme])) {
                if (!at_container('container')->offsetExists('page.blocks')) {
                    at_container('container')->offsetSet('page.blocks', $config['blocks'][$theme]);
                }
                unset($config['blocks']);
            }

            try {
                return at_container('helper.ContentRender')->render($config);
            }
            catch (\Exception $e) {
                watchdog_exception(WATCHDOG_CRITICAL, $e);
                return $e->getMessage();
            }
        }
    }

    public function execute()
    {
        if ($build = $this->build()) {
            $this->build = array(
                '#entity_type'       => $this->entityType,
                '#bundle'            => $this->bundle,
                '#view_mode'         => $this->viewMode,
                '#language'          => $this->build['#language'],
                '#contextual_links ' => !empty($this->build['#contextual_links']) ? $this->build['#contextual_links'] : NULL,
                'at_base'            => is_string($build) ? array('#markup' => $build) : $build,
                '#build'             => $this->build,
            );
        }
    }

    /**
     * Get cached render configuration for context.
     */
    public function getConfig()
    {
        $o = array(
            'id'  => "at_theming:entity_template:{$this->entityType}:{$this->bundle}:{$this->viewMode}",
            'ttl' => '+ 1 year',
        );
        return at_cache($o, array($this, 'fetchConfig'));
    }

    /**
     * Get cached render configuration for context.
     */
    public function fetchConfig()
    {
        foreach (at_modules('at_base', 'entity_template') as $module) {
            if ($config = $this->fetchModuleConfig($module)) {
                return $config;
            }
        }
    }

    private function fetchModuleConfig($module)
    {
        $config = at_config($module, 'entity_template')->get('entity_templates');

        foreach (array('entity_type', 'bundle', 'view_mode') as $k) {
            $config = isset($config[$this->{$k}]) ? $config[$this->{$k}] : (isset($config['all']) ? $config['all'] : NULL);

            if (is_null($config)) {
                return;
            }
        }

        return $config;
    }

}
