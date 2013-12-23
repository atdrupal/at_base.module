<?php

/**
 * Callback for twig:doc drush command.
 */
function drush_at_twig_doc($name) {
  $twig = \AT::twig();
  // drush_print_r($twig);

  // Get filters & functions
  $filters = $twig->getFilters();
  $functions = $twig->getFunctions();

  // Display details
  drush_print_r($functions);
  drush_print_r($filters);
}
