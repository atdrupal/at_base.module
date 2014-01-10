<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

class Entity_Reference_Warmer implements Relationship_Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return FALSE !== strpos($tag, '%entity_references')
      || FALSE !== strpos($tag, '%process:entity_references')
    ;
  }

  /**
   * @inheritdoc
   */
  public function getRelatedEntities($entity_type, $entity) {
  }

  /**
   * @inheritdoc
   */
  public function processTag($tag, $entity_warmer) {}
}
