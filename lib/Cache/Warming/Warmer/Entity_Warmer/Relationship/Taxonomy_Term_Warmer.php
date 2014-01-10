<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

class Taxonomy_Term_Warmer implements Relationship_Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return FALSE !== strpos($tag, '%taxonomy_terms')
      || FALSE !== strpos($tag, '%process:taxonomy_terms')
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
