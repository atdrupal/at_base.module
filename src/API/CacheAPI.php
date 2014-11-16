<?php

namespace Drupal\at_base\API;

use Drupal\at_base\API\CacheAPI\TagFlusher;
use Drupal\at_base\Helper\Wrapper\DrupalCacheAPI;

class CacheAPI
{

    /** @var TagFlusher */
    private $tagFlusher;

    /**
     * @param DrupalDatabaseAPI $db
     * @param DrupalCacheAPI $cache
     * @return TagFlusher
     */
    public function getTagFlusher($db = NULL, $cache = NULL)
    {
        if (NULL === $this->tagFlusher) {
            $db = NULL !== $db ? $db : at()->getApi()->getDrupalDatabaseAPI();
            $cache = NULL !== $cache ? $cache : at()->getApi()->getDrupalCacheAPI();
            $this->tagFlusher = new TagFlusher($db, $cache);
        }
        return $this->tagFlusher;
    }

    public function setTagFlusher($tagFlusher)
    {
        $this->tagFlusher = $tagFlusher;
        return $this;
    }

}
