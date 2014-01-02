<?php

function drush_at_cache_clear() {
  $tags = func_get_args();
  at_cache_flush_by_tags($tags);
}

/**
 * Callback when clear cache type "At config"
 */
function drush_at_config_cache_clear() {
  if (function_exists('at_cache_flush_by_tags')) {
    // Clear all caches have "at-config" tag.
    at_cache_flush_by_tags(array('at-config'));
  }
}
