<?php
namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTest;
use limb\fs\src\exception\lmbFileNotFoundException;
use limb\macro\src\compiler\lmbMacroTreeBuilder;
use limb\macro\src\compiler\lmbMacroTagDictionary;
use limb\macro\src\compiler\lmbMacroParser;
use limb\macro\src\compiler\lmbMacroNode;
use limb\macro\src\compiler\lmbMacroSourceLocation;

Mock::generate('lmbMacroTreeBuilder', 'MockMacroTreeBuilder');
Mock::generate('lmbMacroTagDictionary', 'MockMacroTagDictionary');

class lmbMacroParserTest extends lmbBaseMacroTest
{
  function testParse_raiseExceptionWhenFileNotExist()
  {  	
  	$tree_builder = $this->createMock(lmbMacroTreeBuilder::class);
  	$tag_dictionary = $this->createMock(lmbMacroTagDictionary::class);
  	$parser = new lmbMacroParser($tree_builder, $tag_dictionary);
  	
  	$location = new lmbMacroSourceLocation($parent_file = 'caller', $line = 42);
  	$parent_node = new lmbMacroNode($location);

  	try {
  	  $parser->parse($not_existed_file = 'not_exist', $parent_node);
  	  $this->fail();
  	}
  	catch(lmbFileNotFoundException $e)
  	{
  	  if($this->pass())
  	  {
  	    $this->assertIdentical($e->getFilePath(), $not_existed_file);
  	    $this->assertIdentical($e->getParam('parent_file'), $parent_file);
  	    $this->assertIdentical($e->getParam('parent_file_line'), $line);
  	  }	
  	}
  }
}
