<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\core\cases;

require_once ('.setup.php');

use PHPUnit\Framework\TestCase;

class lmbCoreUtilsTest extends TestCase
{
  function testCamelCaseUcfirst()
  {
    $this->assertEquals(lmb_camel_case('foo'), 'Foo');
    $this->assertEquals(lmb_camel_case('foo_bar'), 'FooBar');
    $this->assertEquals(lmb_camel_case('foo168_bar'), 'Foo168Bar');
    $this->assertEquals(lmb_camel_case('foo_bar_hey_wow'), 'FooBarHeyWow');
    $this->assertEquals(lmb_camel_case('_foo_bar'), '_FooBar');
    $this->assertEquals(lmb_camel_case('_foo_bar_'), '_FooBar_');
    $this->assertEquals(lmb_camel_case('___foo___'), '___Foo___');
  }

  function testCamelCaseDontUcfirst()
  {
    $this->assertEquals(lmb_camel_case('foo', false), 'foo');
    $this->assertEquals(lmb_camel_case('foo_bar', false), 'fooBar');
    $this->assertEquals(lmb_camel_case('foo168_bar', false), 'foo168Bar');
    $this->assertEquals(lmb_camel_case('foo_bar_hey_wow', false), 'fooBarHeyWow');
    $this->assertEquals(lmb_camel_case('_foo_bar', false), '_fooBar');
    $this->assertEquals(lmb_camel_case('_foo_bar_', false), '_fooBar_');
    $this->assertEquals(lmb_camel_case('___foo___', false), '___foo___');
  }
  
  function testUnderScores()
  {
    $this->assertEquals(lmb_under_scores('FooBar'), 'foo_bar');
    $this->assertEquals(lmb_under_scores('Foo168Bar'), 'foo168_bar');
    $this->assertEquals(lmb_under_scores('FooBarZoo'), 'foo_bar_zoo');
    $this->assertEquals(lmb_under_scores('_FooBarZoo'), '_foo_bar_zoo');
    $this->assertEquals(lmb_under_scores('_FooBarZoo_'), '_foo_bar_zoo_');
  }
  
  function testPlural()
  {
    //$this->assertEquals(lmb_plural('dog'), 'dogs');
    $this->assertEquals(lmb_plural('glass'), 'glasses');
    $this->assertEquals(lmb_plural('dictionary'), 'dictionaries');
    $this->assertEquals(lmb_plural('boy'), 'boys');
    $this->assertEquals(lmb_plural('half'), 'halves');
    $this->assertEquals(lmb_plural('man'), 'men');
  }

  function testCamelCaseWithNumbers()
  {
    $this->assertEquals(lmb_camel_case('foo_0'), 'Foo_0');
    $this->assertEquals(lmb_camel_case('foo_1_bar'), 'Foo_1Bar');
  }

  function testUnderScoresWithNumbers()
  {
    $this->assertEquals(lmb_under_scores('Foo_0'), 'foo_0');
    $this->assertEquals(lmb_under_scores('Foo_1Bar'), 'foo_1_bar');
  }
}

