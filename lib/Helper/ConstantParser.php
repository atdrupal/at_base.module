<?php

namespace Drupal\at_base\Helper;

/**
 * Parse 'CONSTANT', 'CONSTANT_1 | CONSTANT_2', 'CONSTANT_1 & CONSTANT_2' to
 * real value.
 *
 * @see  \At_Base_Contant_Parser_TestCase::testAtConstantParser()
 */
class ConstantParser {
  private $string;

  public function __construct($string) {
    $this->string = $string;
  }

  public function parse() {
    if (preg_match('/^[A-Z_]+$/', $this->string)) {
      return constant($this->string);
    }

    return $this->parseOperator();
  }

  private function parseOperator() {
    $part = preg_split('/\s+[\|&]\s+/', $this->string);
    if (count($part)) {
      foreach ($part as $i => $constant) {
        $part[$i] = constant($constant);
      }

      return strpos($this->string, '|') ? ($part[0] | $part[1]) : $part[0] & $part[1];
    }
  }
}
