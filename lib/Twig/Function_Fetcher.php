<?php
namespace Drupal\at_base\Twig;

class Function_Fetcher extends Filter_Fetcher {
  protected $config_id  = 'twig_functions';
  protected $config_key = 'twig_functions';
  protected $twig_base  = '\Twig_SimpleFunction';
  protected $wrapper    = '\Drupal\at_base\Twig\Functions\Wrapper';
}
