<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace tests\macro\cases\compiler;

use PHPUnit\Framework\TestCase;

Mock::generate('lmbMacroTokenizerListener', 'MockMacroTokenizerListener');

class lmbMacroTokenizerMalformedTest extends TestCase
{
  protected $parser;
  protected $listener;

  function setUp(): void
  {
    $this->listener = new MockMacroTokenizerListener();
    $this->parser = new lmbMacroTokenizer($this->listener);
  }

  function testOpenElementMalformedClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('invalidEntitySyntax', array('{{tag attribute=\'value\'/}morestuff'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff{{tag attribute=\'value\'/}morestuff');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '', "'" => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('{{tag attribute=\'\'\'}}');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '', '"' => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('{{tag attribute="""}}');
  }

  function testElementMalformedAttribute()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => 'test', 'extra' => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('{{tag attribute="test"extra}}');
  }
}


