<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

class User_Warmer implements Relationship_Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return FALSE !== strpos($tag, '%author')
      || FALSE !== strpos($tag, '%process:author')
    ;
  }

  /**
   * @inheritdoc
   */
  public function getRelatedEntities($entity) {
  }

  /**
   * @inheritdoc
   */
  public function processTag($tag, $entity_warmer) {}
}
