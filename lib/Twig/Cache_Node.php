<?php
namespace Drupal\at_base\Twig;

class Cache_Node extends \Twig_Node {
  private $cache_options;

  public function __construct(\Twig_NodeInterface $cache_options, \Twig_NodeInterface $callback, $lineno = 0, $tag = 'cache') {
    parent::__construct(array('cache_options' => $cache_options, 'callback' => $callback), array(), $lineno, $tag);
  }

  public function compile(\Twig_Compiler $compiler) {
    $compiler
      ->addDebugInfo($this)
      ->write("\$env = \$this->env->getGlobals();\n\n")
      ->write("echo at_cache(")
      ->subcompile($this->getNode('cache_options'))
      ->raw(", function() use (\$env) {\n")
      ->indent()
      ->write("ob_start();\n")
      ->write("\$context = \$env->getGlobals();\n\n")
      ->subcompile($this->getNode('callback'))
      ->write("\$tmp = ob_get_clean() ? '' : new Twig_Markup(\$tmp, \$env->getCharset());\n")
      ->write("return ('' === \$tmp = ob_get_clean()) ? '' : new Twig_Markup(\$tmp, \$env->getCharset());\n")
      ->outdent()
      ->write("});\n")
    ;
  }
}
