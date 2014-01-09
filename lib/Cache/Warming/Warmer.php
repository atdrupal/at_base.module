<?php

namespace Drupal\at_base\Cache\Warming;

use Drupal\at_base\Cache\Warming\Warmer\Entity_Warmer;
use Drupal\at_base\Cache\Warming\Warmer\View_Warmer;
use Drupal\at_base\Cache\Warming\Warmer\Simple_Warmer;

/**
 * @todo  Think about sub-processes.
 *
 * Warm cached data.
 *
 * Usage
 *
 * @code
 *   at_container('cache.warmer')
 *     ->setEventName('user_login')
 *     ->setContext(array('entity_type' => 'user', 'entity' => $account))
 *     ->warm()
 *   ;
 * @code
 */
class Warmer {
  private $tag_discover;
  private $warmers;
  private $context;
  private $event_name;
  private $is_sub_process = FALSE;

  /**
   * A process can start sub-process. This flag will avoid infinitive master
   * processes.
   *
   * @var boolean
   */
  private $is_sub_process = FALSE;

  public function __construct($tag_discover) {
    $this->tag_discover = $tag_discover;

    // @todo: Use tagged services
    $this->warmers = array(
      'entity' => new Entity_Warmer(),
      'view'   => new View_Warmer(),
    );
  }

  public function setEventName($event_name) {
    $this->event_name = $event_name;
    return $this;
  }

  public function setIsSubProcess($is_sub_process = FALSE) {
    $this->is_sub_process = $is_sub_process;
    return $this;
  }

  public function setContext($context) {
    $this->context = $context;
  }

  /**
   * Wrapper function to warm cached-tags & views.
   */
  public function warm() {
    foreach ($tag_discover->tags() as $tag) {
      foreach ($this->warmers as $warmer) {
        if (TRUE === $warmer->validateTag($tag)) {
          $warmer->warm($tag, $context);
        }
      }
    }
  }
}
