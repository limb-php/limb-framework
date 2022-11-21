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
use limb\macro\src\compiler\lmbMacroTokenizerListenerInterface;
use limb\macro\src\compiler\lmbMacroTokenizer;

require (dirname(__FILE__) . '/../.setup.php');

class lmbMacroTokenizerMalformedTest extends TestCase
{
  protected $parser;
  protected $listener;

  function setUp(): void
  {
    $this->listener = $this->createMock(lmbMacroTokenizerListenerInterface::class);
    $this->parser = new lmbMacroTokenizer($this->listener);
  }

  function testOpenElementMalformedClose()
  {
    $this->listener
        ->expects($this->once())
        ->method('characters', array('stuff'));

    $this->listener
        ->expects($this->once())
        ->method('invalidEntitySyntax', array('{{tag attribute=\'value\'/}morestuff'));

    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->parser->parse('stuff{{tag attribute=\'value\'/}morestuff');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement', array('tag', array('attribute' => '', "'" => NULL)));

    $this->listener
        ->expects($this->once())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->parser->parse('{{tag attribute=\'\'\'}}');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement', array('tag', array('attribute' => '', '"' => NULL)));

    $this->listener
        ->expects($this->once())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->parser->parse('{{tag attribute="""}}');
  }

  function testElementMalformedAttribute()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement', array('tag', array('attribute' => 'test', 'extra' => NULL)));

    $this->listener
        ->expects($this->once())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->parser->parse('{{tag attribute="test"extra}}');
  }
}
