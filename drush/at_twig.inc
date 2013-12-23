<?php

/**
 * Callback for twig:doc drush command.
 */
function drush_at_twig_doc($name = 'all') {
  $twig = \AT::twig();

  // Get filters & functions
  $filters = $twig->getFilters();
  $functions = $twig->getFunctions();

  // Display details
  if ($name === 'all') {
    drush_print_r($functions);
    drush_print_r($filters);
  }
  elseif ($filter = $twig->getFilter($name)) {
    drush_print_r($filter);
  }
  elseif ($function = $twig->getFunction($name)) {
    drush_print_r($function);
  }
  else {
    drush_print_r('Unknow function/filter: ' . $name);
  }
}
