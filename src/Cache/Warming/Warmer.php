<?php

namespace Drupal\at_base\Cache\Warming;

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
class Warmer
{

    private $tagDiscover;
    private $tagFlusher;
    private $warmers;
    private $context;
    private $eventName;

    /**
     * A process can start sub-process. This flag will avoid infinitive master
     * processes.
     *
     * @var boolean
     */
    private $is_sub_process = FALSE;

    public function __construct($tag_discover, $tag_flusher)
    {
        $this->tagDiscover = $tag_discover;
        $this->tagFlusher = $tag_flusher;

        $this->warmers = at_container('container')->find('cache.warmer', 'service');
    }

    public function setEventName($event_name)
    {
        $this->eventName = $event_name;
        $this->tagDiscover->setEventName($event_name);
        return $this;
    }

    public function setIsSubProcess($is_sub_process = FALSE)
    {
        $this->is_sub_process = $is_sub_process;
        return $this;
    }

    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Wrapper function to warm cached-tags & views.
     */
    public function warm()
    {
        $this->tagFlusher->resetTags();

        foreach ($this->tagDiscover->tags() as $tag) {
            foreach ($this->warmers as $warmer) {
                if (TRUE === $warmer->validateTag($tag)) {
                    if ($tag = $warmer->processTag($tag, $this->context)) {
                        $this->tagFlusher->addTag($tag);
                    }
                }
            }
        }

        $this->tagFlusher->flush();
    }

}
