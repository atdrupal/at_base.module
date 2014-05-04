<?php
namespace Drupal\at_base\Cache\Warming\Warmer;

class EntityWarmer implements WarmerInterface {
  private $entity_info;
  private $entity_type;
  private $entity;
  private $entity_bundle;
  private $entity_id;
  private $tokens = array('%entity_type', '%type', '%entity_bundle', '%bundle', '%entity_id', '%id');

  public function __construct() {
  }

  public function validateTag($tag) {
    foreach ($this->tokens as $token) {
      if (FALSE !== strpos($tag, $token)) {
        return TRUE;
      }
    }
  }

  private function setEntityInfoFromContext($context) {
    if (empty($context['entity_type'])) {
      throw new \Exception('Missing entity type');
    }

    if (!$info = entity_get_info($context['entity_type'])) {
      throw new \Exception('Invalid entity type: ' . $context['entity_type']);
    }

    if (!empty($context['entity_id'])) {
      $context['entity'] = entity_load_single($context['entity_type'], $context['entity_id']);
    }

    if (empty($context['entity'])) {
      throw new \Exception('Missing entity object or entity ID.');
    }

    $this->entity_info   = $info;
    $this->entity_type   = $context['entity_type'];
    $this->entity        = $context['entity'];
    $this->entity_bundle = !empty($info['entity keys']['bundle']) ? $this->entity->{$info['entity keys']['bundle']} : '';
    $this->entity_id     = $this->entity->{$info['entity keys']['id']};
  }

  private function getTagFind() {
    return array('%entity_type', '%type', '%entity_bundle', '%bundle', '%entity_id', '%id');
  }

  private function getTagReplace() {
    $type = $this->entity_type;
    $bundle = $this->entity_bundle;
    $id = $this->entity_id;
    return array($type, $type, $bundle, $bundle, $id, $id, $id);
  }

  public function processTag($tag, $context = array()) {
    $this->setEntityInfoFromContext($context);

    // Replace entity's tokens with real values
    $tag = str_replace($this->getTagFind(), $this->getTagReplace(), $tag);

    // Warm relationship entities
    $relationship_warmers = atc()->find('cache.warmer.entity.relationships', 'service');
    foreach ($relationship_warmers as $relationship_warmer) {
      if ($relationship_warmer->validateTag($tag)) {
        $relationship_warmer->processTag($tag, $this);
      }
    }

    return $tag;
  }
}
