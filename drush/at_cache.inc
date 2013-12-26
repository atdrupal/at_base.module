<?php

function drush_at_cache_clear() {
  $tags = func_get_args();
  at_cache_flush_by_tags($tags);
}
