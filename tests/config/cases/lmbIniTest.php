<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

use limb\config\src\lmbIni;
use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;

lmbEnv::setor('INI_TEST_UNIQUE_CONSTANT', '*constant*');

class lmbIniTest extends TestCase
{
  function setUp()
  {
    lmbFs :: mkdir(lmb_var_dir() . '/tmp_ini');
  }

  function tearDown()
  {
    lmbFs :: rm(lmb_var_dir() . '/tmp_ini');
  }

  function _createIni($contents)
  {
    file_put_contents($file = lmb_var_dir() . '/tmp_ini/' . mt_rand() . '.ini', $contents);
    return new lmbIni($file);
  }

  function testFilePath()
  {
    $ini = new lmbIni(dirname(__FILE__) . '/ini_test.ini', false);
    $this->assertEquals($ini->getOriginalFile(), dirname(__FILE__) . '/ini_test.ini');
  }

  function testGet()
  {
    $ini = $this->_createIni('a=foo
                              b=bar');

    $this->assertEquals($ini->get('a'), 'foo');
    $this->assertEquals($ini->get('b'), 'bar');
  }

  function testTrimFileContents()
  {
    $ini = $this->_createIni(
      '
        [group1]
         value = test1
      [group2]
              value = test2
      '
    );

    $this->assertEquals($ini->export(),
      array(
        'group1' => array('value' => 'test1'),
        'group2' => array('value' => 'test2'),
      )
    );
  }

  function testParseComments()
  {
    $ini = $this->_createIni(
      '
      #[group_is_commented]
      [group1]
       value1 = test1#this a commentary #too#
       #"this is just a commentary"
       value2 = test2
       value3 = "#" # symbols are allowed inside of ""
      '
    );

    $this->assertEquals($ini->export(),
      array(
        'group1' => array(
          'value1' => 'test1',
          'value2' => 'test2',
          'value3' => '#'),
      )
    );
  }

  function testParseStringsWithSpaces()
  {
    $ini = $this->_createIni(
      '
      [group1]
       value1 = this is a string with spaces            indeed
       value2 =       "this is string with spaces too
      '
    );

    $this->assertEquals($ini->export(),
      array(
        'group1' => array(
          'value1' => 'this is a string with spaces            indeed',
          'value2' => '"this is string with spaces too',
          ),
      )
    );
  }

  function testParseProperQuotes()
  {
    $ini = $this->_createIni(
      '
      [group1]
       value1 = "  this is a quoted string  "
       value2 = "  this is a quoted string "too"  "
       value3 = "  this is a quoted string \'too\'  "
      '
    );

    $this->assertEquals($ini->export(),
      array(
        'group1' => array(
          'value1' => '  this is a quoted string  ',
          'value2' => '  this is a quoted string "too"  ',
          'value3' => '  this is a quoted string \'too\'  ',
          ),
      )
    );
  }

  function testParseGlobalValues()
  {
    $ini = $this->_createIni(
      '
      value = global_test
      [group1]
       value = test
      '
    );

    $this->assertEquals($ini->export(),
      array(
        'value' => 'global_test',
        'group1' => array('value' => 'test'),
      )
    );
  }

  function testParseNullElements()
  {
    $ini = $this->_createIni(
      '
      [group1]
       value =
      '
    );

    $this->assertEquals($ini->export(),
      array('group1' => array('value' => null))
    );

    $this->assertFalse($ini->hasOption('group1', 'value'));
  }

  function testParseArrayElements()
  {
    $ini = $this->_createIni(
      '
      [group1]
       value[] =
       value[] = 1
       value[] =
       value[] = 2
      '
    );

    $this->assertEquals($ini->export(),
      array('group1' => array('value' => array(null, 1, null, 2)))
    );
  }

  function testParseHashedArrayElements()
  {
    $ini = $this->_createIni(
      '
      [group1]
       value[apple] =
       value[banana] = 1
       value[fruit] =
       value["lime"] = not valid index!
       value[\'lime\'] = not valid index too!
      '
    );

    $this->assertEquals($ini->export(),
      array('group1' => array('value' =>
                 array('apple' => null, 'banana' => 1, 'fruit' => null)))
    );
  }

  function testParseMixedArrays()
  {
    $ini = $this->_createIni(
      '
      [group1]

       foo[apple] = 1
       bar[] = 1
       foo[banana] = 2
       bar[] = 2
      '
    );

    $this->assertEquals($ini->export(),
      array('group1' => array('foo' => array('apple' => 1, 'banana' => 2),
                              'bar' => array(1, 2))));
  }

  function testHasChecks()
  {
    $ini = $this->_createIni(
      '
        unassigned =
        junk = 1

        [test]
        test = 1

        [test2]
        test3 =

        [empty_group]
        test = '
    );

    $this->assertFalse($ini->hasGroup(''));
    $this->assertTrue($ini->hasGroup('test'));
    $this->assertTrue($ini->hasGroup('test2'));
    $this->assertTrue($ini->hasGroup('empty_group'));

    $this->assertFalse($ini->hasOption(null, null));
    $this->assertFalse($ini->hasOption('', ''));
    $this->assertFalse($ini->hasOption('', 'no_such_block'));
    $this->assertTrue($ini->hasOption('test', 'test'));
    $this->assertFalse($ini->hasOption('no_such_variable', 'test3'));
    $this->assertTrue($ini->hasOption('unassigned'));
    $this->assertTrue($ini->hasOption('junk'));
  }

  function testGetOption()
  {
    $ini = $this->_createIni(
      '
        unassigned =
        junk = 1

        [test]
        test = 1

        [test2]
        test[] = 1
        test[] = 2

        [test3]
        test[wow] = 1
        test[hey] = 2'
    );

    $this->assertEquals($ini->getOption('unassigned'), '');
    $this->assertEquals($ini->getOption('junk'), 1);

    $this->assertEquals($ini->getOption('no_such_option'), '');

    $this->assertEquals($ini->getOption('test', 'no_such_group'), '');

    $this->assertEquals($ini->getOption('test', 'test'), 1);

    $var = $ini->getOption('test', 'test2');
    $this->assertEquals($var, array(1, 2));

    $var = $ini->getOption('test', 'test3');
    $this->assertEquals($var, array('wow' => 1, 'hey' => 2));
  }

  function testReplaceConstants()
  {
    $ini = $this->_createIni(
      '
        [{INI_TEST_UNIQUE_CONSTANT}]
        test = {INI_TEST_UNIQUE_CONSTANT}1
      '
    );

    $this->assertEquals($ini->getOption('test', '*constant*'), '*constant*1');
  }

  function testGetGroup()
  {
    $ini = $this->_createIni(
      '
        unassigned =
        junk = 1

        [test]
        test = 1
      '
    );

    $this->assertEquals($ini->getGroup('test'), array('test' => 1));
    $this->assertNull($ini->getGroup('no_such_group'));
  }

  function testAssignOption()
  {
    $ini = $this->_createIni(
      '
        unassigned =
        junk = 1

        [test]
        test = 2
      '
    );

    $this->assertTrue($ini->assignOption($test, 'unassigned'));
    $this->assertEquals($test, '');

    $this->assertTrue($ini->assignOption($test, 'junk'));
    $this->assertEquals($test, 1);

    $this->assertTrue($ini->assignOption($test, 'test', 'test'));
    $this->assertEquals($test, 2);

    $this->assertFalse($ini->assignOption($test, 'no_such_option', 'test'));
    $this->assertEquals($test, 2);
  }

  function testMergeWith()
  {
    $a = $this->_createIni(
      'test = 1
       foo = 1
       val[] = 1

       [group-b]
       a = 2
       foo = 1
       arr[1] = a
      '
    );

    $b = $this->_createIni(
      'test = 2
       bar = 2
       val[] = 2

       [group-b]
       a = 1
       bar = 2
       arr[2] = b
      '
    );

    $c = $a->mergeWith($b);
    $this->assertEquals($c->export(), array('test' => 2,
                                           'foo' => 1,
                                           'bar' => 2,
                                           'val' => array(2),
                                           'group-b' => array('a' => 1,
                                                              'bar' => 2,
                                                              'arr' => array(2 => 'b')
                                                               )
                                            )
                          );
  }
}


