<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use tests\macro\cases\lmbBaseMacroTestCase;
use limb\macro\src\compiler\lmbMacroTagInfo;
use limb\macro\src\compiler\lmbMacroSourceLocation;
use limb\macro\src\compiler\lmbMacroRuntimeWidgetTag;

class lmbMacroRuntimeWidgetTagTest extends lmbBaseMacroTestCase
{
    protected $tag;

    function setUp(): void
    {
        parent::setUp();

        $tag_info = new lmbMacroTagInfo('lmbMacroRuntimeWidgetTag', 'whatever');
        $source_location = new lmbMacroSourceLocation('my_file', 10);
        $this->tag = new lmbMacroRuntimeWidgetTag($source_location, 'my_tag', $tag_info);
    }

    function testGetRuntimeId_ByRuntimeIdAttribute()
    {
        $this->tag->set('runtime_id', 'my_id');
        $this->assertEquals($this->tag->getRuntimeId(), 'my_id');
    }

    function testGetRuntimeId_ByIdAttribute()
    {
        $this->tag->set('id', 'my_id');
        $this->assertEquals($this->tag->getRuntimeId(), 'my_id');
    }

    function testGetRuntimeId_ByIdAttribute_DontUseDynamicRuntimeId()
    {
        $this->tag->set('runtime_id', '$#some_value');
        $this->tag->set('id', 'my_id');
        $this->assertEquals($this->tag->getRuntimeId(), 'my_id');
    }

    function testGetRuntimeId_ByNameAttribute()
    {
        $this->tag->set('name', 'my_id');
        $this->assertEquals($this->tag->getRuntimeId(), 'my_id');
    }

    function testGetRuntimeId_ByNameAttribute_DontUseDynamicIdAttribute()
    {
        $this->tag->set('id', '$#some_value');
        $this->tag->set('name', 'my_id');
        $this->assertEquals($this->tag->getRuntimeId(), 'my_id');
    }

    function testGetRuntimeId_ByCleanedNameAttribute()
    {
        $this->tag->set('name', 'my_id[]');
        $this->assertNotEquals($this->tag->getRuntimeId(), 'my_id[]');
    }

    function testGetRuntimeId_PreferRuntimeIdOverOtherWays()
    {
        $this->tag->set('runtime_id', 'id_by_runtime_id');
        $this->tag->set('id', 'id_by_id');
        $this->tag->set('name', 'id_by_name');
        $this->assertEquals($this->tag->getRuntimeId(), 'id_by_runtime_id');
    }

    function testGetRuntimeId_PreferIdOverOtherName()
    {
        $this->tag->set('id', 'id_by_id');
        $this->tag->set('name', 'id_by_name');
        $this->assertEquals($this->tag->getRuntimeId(), 'id_by_id');
    }

    function testGetRuntimeId_GenerateIdByDefault_AndSetRuntimeIdAttributeInThisCase()
    {
        $this->assertNotNull($this->tag->getRuntimeId());
        $this->assertTrue($this->tag->has('runtime_id'));
    }
}
