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

class lmbMacroTokenizerTest extends TestCase
{
  protected $parser;
  protected $listener;

  function setUp(): void
  {
    $this->listener = $this->createMock(lmbMacroTokenizerListenerInterface::class);
    $this->parser = new lmbMacroTokenizer($this->listener);
  }

  function testEmpty()
  {
    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->parser->parse('');
  }

  function testSimpledata()
  {
    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('stuff');

    $this->parser->parse('stuff');
  }

  function testPreservingWhiteSpace()
  {
    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->with(" stuff\t\r\n ");

    $this->parser->parse(" stuff\t\r\n ");
  }

  function testEmptyElement()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->with('tag', array());

    $this->listener
        ->expects($this->once())
        ->method('endElement')
        ->with('tag');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag}}{{/tag}}');
  }

  function testEmptyElementSelfClose()
  {
    $this->listener
        ->expects($this->once())
        ->method('emptyElement')
        ->willReturn('tag', array());

    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->parser->parse('{{tag/}}');
  }

  function testElementWithContent()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array());

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('stuff');

    $this->listener
        ->expects($this->once())
        ->method('endElement')
        ->willReturn('tag');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag}}stuff{{/tag}}');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array('attribute' => '\''));

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag attribute="\'"}}');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array('attribute' => '"'));

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag attribute=\'"\'}}');
  }

  function testEmptyClose()
  {
    $this->listener
        ->expects($this->once())
        ->method('endElement')
        ->willReturn('');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->parser->parse('{{/}}');
  }

  function testSelfClosingPHPBlock()
  {
    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->once())
        ->method('php')
        ->willReturn('<?php $var = "{{tag}}{{/tag}}";?>');

    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testSeveralPHPBlocks()
  {
    $this->listener
        ->expects($this->exactly(2))
        ->method('characters');

    $this->listener
        ->expects($this->at(0))
        ->method('characters')
        ->with('hey');

    $this->listener
        ->expects($this->at(1))
        ->method('characters')
        ->with('foo');

    $this->listener
        ->expects($this->exactly(2))
        ->method('php');

    /*$this->listener
        ->expects($this->at(0))
        ->method('php')
        ->with('<?php $yo = "{{foo/}}";?>');

    $this->listener
        ->expects($this->at(1))
        ->method('php')
        ->with('<?php $var = "{{tag}}{{/tag}}";?>');*/

    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('<?php $yo = "{{foo/}}";?>hey<?php $var = "{{tag}}{{/tag}}";?>foo');
  }

  function testNonClosingPHPBlock()
  {
    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->once())
        ->method('php')
        ->willReturn('<?php $var = "{{tag}}{{/tag}}";');

    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";');
  }

  function testTagAfterPHPBlock()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('foo', array());

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('hey');

    $this->listener
        ->expects($this->once())
        ->method('endElement')
        ->willReturn('foo');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->once())
        ->method('php')
        ->willReturn('<?php $var = "{{tag}}{{/tag}}";?>');

    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";?>{{foo}}hey{{/foo}}');
  }

  function testTagBeforePHPBlock()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('foo', array());

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('hey');

    $this->listener
        ->expects($this->once())
        ->method('endElement')
        ->willReturn('foo');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->once())
        ->method('php')
        ->willReturn('<?php $var = "{{tag}}{{/tag}}";?>');

    $this->parser->parse('{{foo}}hey{{/foo}}<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testCharactersBeforePHPBlock()
  {
    $this->listener
        ->expects($this->never())
        ->method('startElement');

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('hey');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->once())
        ->method('php')
        ->willReturn('<?php $var = "{{tag}}{{/tag}}";?>');

    $this->parser->parse('hey<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testMixedTagsAndPHPBlocks()
  {
    $this->listener
        ->expects($this->exactly(2))
        ->method('startElement');

    /*$this->listener
        ->expects($this->at(0))
        ->method('startElement')
        ->with('foo', array());

    $this->listener
        ->expects($this->at(1))
        ->method('startElement')
        ->with('zoo', array());*/

    $this->listener
        ->expects($this->exactly(4))
        ->method('characters');

    $this->listener
        ->expects($this->at(0))
        ->method('characters')
        ->with('hey');

    $this->listener
        ->expects($this->at(1))
        ->method('characters')
        ->with('baz');

    $this->listener
        ->expects($this->at(2))
        ->method('characters')
        ->with('wow');

    $this->listener
        ->expects($this->at(3))
        ->method('characters')
        ->with('hm..');

    $this->listener
        ->expects($this->exactly(2))
        ->method('endElement');

    /*$this->listener
        ->expects($this->at(0))
        ->method('endElement')
        ->with('foo');

    $this->listener
        ->expects($this->at(1))
        ->method('endElement')
        ->with('zoo');*/

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->listener
        ->expects($this->exactly(2))
        ->method('php');

    /*$this->listener
        ->expects($this->at(0))
        ->method('php')
        ->with('<?php $var = "{{tag}}{{/tag}}";?>');

    $this->listener
        ->expects($this->at(1))
        ->method('php')
        ->with('<?php echo 1;?>');*/

    $this->parser->parse('{{foo}}hey{{/foo}}baz<?php $var = "{{tag}}{{/tag}}";?>{{zoo}}wow{{/zoo}}hm..<?php echo 1;?>');
  }

  function testOutputTag()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('$value', array());

    $this->listener
        ->expects($this->never())
        ->method('characters');

    $this->listener
        ->expects($this->never())
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{$value}}');
  }

  function testElementWithPreContent()
  {
    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('stuff');

    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('br', array());

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('stuff{{br}}');
  }

  function testElementWithPostContent()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('br', array());

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('stuff');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{br}}stuff');
  }

  function testExpressionAfterTag()
  {
    $this->listener
        ->expects($this->once())
        ->method('emptyElement')
        ->willReturn('br', array());

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('{$str}');

    $this->parser->parse('{{br/}}{$str}');
  }

  function testSelfClosingTagWithArgumentsAndNoSpaceBeforeClosing()
  {
    $this->listener
        ->expects($this->once())
        ->method('emptyElement')
        ->willReturn('tag', array('str' => 'abcdefgh'));

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag str="abcdefgh"/}}');
  }

  function testExpressionAfterTagWithArguments()
  {
    $this->listener
        ->expects($this->once())
        ->method('emptyElement')
        ->willReturn('tag', array('str' => 'abcdefgh'));

    $this->listener
        ->expects($this->once())
        ->method('characters')
        ->willReturn('{$str}');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag str="abcdefgh" /}}{$str}');
  }

  function testMismatchedElements()
  {
    $this->listener
        ->method('startElement')
        ->withConsecutive(
            ['b', array()],
            ['i', array()]
        );

    /*$this->listener
        ->method('endElement')
        ->with('b')
        ->with('i');*/

    $this->listener
        ->expects($this->exactly(2))
        ->method('startElement');

    $this->listener
        ->expects($this->exactly(2))
        ->method('endElement');

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{b}}{{i}}stuff{{/b}}{{/i}}');
  }

  function testAttributes()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array("a" => "A", "b" => "B", "c" => "C"));

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag a="A" b=\'B\' c = "C"}}');
  }

  function testEmptyAttributes()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array("a" => NULL, "b" => NULL, "c" => NULL));

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse('{{tag a b c}}');
  }

  function testNastyAttributes()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array("a" => "&{\$'?<>",
                                                                   "b" => "\r\n\t\"",
                                                                   "c" => ""));

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse("{{tag a=\"&{\$'?<>\" b='\r\n\t\"' c = ''}}");
  }

  function testAttributesPadding()
  {
    $this->listener
        ->expects($this->once())
        ->method('startElement')
        ->willReturn('tag', array("a" => "A", "b" => "B", "c" => "C"));

    $this->listener
        ->expects($this->never())
        ->method('invalidAttributeSyntax');

    $this->parser->parse("{{tag\ta=\"A\"\rb='B'\nc = \"C\"\n}}");
  }
}
