<?php
namespace Drupal\at_base\Twig;

/**
 * Examples
 *
 *    {% cache(options) %}
 *      sub
 *    {% endcache %}
 *
 *    {% cache({id: 'myCacheId'}) %}
 *      sub
 *    {% endcache %}
 */
class Cache_TokenParser extends \Twig_TokenParser {
  /**
   * @param  \Twig_Token $token
   * @return Cache_Node
   */
  public function parse(\Twig_Token $token) {
    $parser = $this->parser;
    $stream = $parser->getStream();

    $cache_options = $parser->getExpressionParser()->parseExpression();
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);
    $parser->pushLocalScope();
    $callback = $parser->subparse(array($this, 'decideBlockEnd'), true);
    $this->parser->popLocalScope();
    $stream->expect(\Twig_Token::BLOCK_END_TYPE);

    return new Cache_Node($cache_options, $callback, $token->getLine(), $this->getTag());
  }

  public function getTag() {
    return 'cache';
  }

  public function decideBlockEnd(\Twig_Token $token) {
    return $token->test('endcache');
  }
}
