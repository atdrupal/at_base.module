<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

interface Relationship_Warmer_Interface {
  /**
   * Check if the cache should warm a specific tag.
   *
   * @param  string $tag
   * @return boolean
   */
  public function validateTag($tag);

  /**
   * Get related entities from parent entity.
   *
   * @param  string $entity_type
   * @param  mixed  $entity
   * @return array  Array of related entities.
   */
  public function getRelatedEntities($entity_type, $entity_type, $entity) {
  }

  /**
   * Logic to flush cached-items which tagged with $tag.
   *
   * @param  string $tag
   * @param  [type] $entity_warmer [description]
   * @return [type]                [description]
   */
  public function processTag($tag, $entity_warmer);
}
