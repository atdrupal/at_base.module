<?php

function drush_at_cache_clear()
{
    $tags = func_get_args();
    return at()->getApi()->getCacheAPI()->getTagFlusher()->flush($tags);
}
