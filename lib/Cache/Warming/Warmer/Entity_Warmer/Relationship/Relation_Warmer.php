<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

class Relation_Warmer implements Relationship_Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return FALSE !== strpos($tag, '%relationships')
      || FALSE !== strpos($tag, '%process:relationships')
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
