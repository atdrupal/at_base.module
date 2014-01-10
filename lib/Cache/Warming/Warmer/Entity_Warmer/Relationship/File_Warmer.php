<?php
namespace Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer\Relationship;

class File_Warmer implements Relationship_Warmer_Interface {
  /**
   * @inheritdoc
   */
  public function validateTag($tag) {
    return FALSE !== strpos($tag, '%files')
      || FALSE !== strpos($tag, '%process:files')
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
