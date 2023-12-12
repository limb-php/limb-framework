<?php

namespace Tests\macro\cases\compiler;

use Tests\macro\cases\lmbBaseMacroTestCase;
use limb\fs\src\exception\lmbFileNotFoundException;
use limb\macro\src\compiler\lmbMacroTreeBuilder;
use limb\macro\src\compiler\lmbMacroTagDictionary;
use limb\macro\src\compiler\lmbMacroParser;
use limb\macro\src\compiler\lmbMacroNode;
use limb\macro\src\compiler\lmbMacroSourceLocation;

class lmbMacroParserTest extends lmbBaseMacroTestCase
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
        } catch (lmbFileNotFoundException $e) {
            $this->assertEquals($e->getFilePath(), $not_existed_file);
            $this->assertEquals($e->getParam('parent_file'), $parent_file);
            $this->assertEquals($e->getParam('parent_file_line'), $line);
        }
    }
}
