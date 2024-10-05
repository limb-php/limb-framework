<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Tests\core\cases;

require_once(dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;

class lmbCoreUtilsTest extends TestCase
{
    function testCamelCaseUcfirst()
    {
        $this->assertEquals('Foo', lmb_camel_case('foo'));
        $this->assertEquals('FooBar', lmb_camel_case('foo_bar'));
        $this->assertEquals('Foo168Bar', lmb_camel_case('foo168_bar'));
        $this->assertEquals('FooBarHeyWow', lmb_camel_case('foo_bar_hey_wow'));
        $this->assertEquals('_FooBar', lmb_camel_case('_foo_bar'));
        $this->assertEquals('_FooBar_', lmb_camel_case('_foo_bar_'));
        $this->assertEquals('___Foo___', lmb_camel_case('___foo___'));
    }

    function testCamelCaseDontUcfirst()
    {
        $this->assertEquals('foo', lmb_camel_case('foo', false));
        $this->assertEquals('fooBar', lmb_camel_case('foo_bar', false));
        $this->assertEquals('foo168Bar', lmb_camel_case('foo168_bar', false));
        $this->assertEquals('fooBarHeyWow', lmb_camel_case('foo_bar_hey_wow', false));
        $this->assertEquals('_fooBar', lmb_camel_case('_foo_bar', false));
        $this->assertEquals('_fooBar_', lmb_camel_case('_foo_bar_', false));
        $this->assertEquals('___foo___', lmb_camel_case('___foo___', false));
    }

    function testUnderScores()
    {
        $this->assertEquals('foo_bar', lmb_under_scores('FooBar'));
        $this->assertEquals('foo168_bar', lmb_under_scores('Foo168Bar'));
        $this->assertEquals('foo_bar_zoo', lmb_under_scores('FooBarZoo'));
        $this->assertEquals('_foo_bar_zoo', lmb_under_scores('_FooBarZoo'));
        $this->assertEquals('_foo_bar_zoo_', lmb_under_scores('_FooBarZoo_'));
    }

    function testPlural()
    {
        //$this->assertEquals(lmb_plural('dog'), 'dogs');
        $this->assertEquals('glasses', lmb_plural('glass'));
        $this->assertEquals('dictionaries', lmb_plural('dictionary'));
        $this->assertEquals('boys', lmb_plural('boy'));
        $this->assertEquals('halves', lmb_plural('half'));
        $this->assertEquals('men', lmb_plural('man'));
    }

    function testCamelCaseWithNumbers()
    {
        $this->assertEquals('Foo_0', lmb_camel_case('foo_0'));
        $this->assertEquals('Foo_1Bar', lmb_camel_case('foo_1_bar'));
    }

    function testUnderScoresWithNumbers()
    {
        $this->assertEquals('foo_0', lmb_under_scores('Foo_0'));
        $this->assertEquals('foo_1_bar', lmb_under_scores('Foo_1Bar'));
    }
}
