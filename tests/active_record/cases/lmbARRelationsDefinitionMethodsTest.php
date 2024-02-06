<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Tests\active_record\cases;

require_once '.setup.php';

use PHPUnit\Framework\TestCase;
use Tests\active_record\cases\src\TestOneTableObjectWithRelationsByMethods;

class lmbARRelationsDefinitionMethodsTest extends TestCase
{
    protected $object;

    function setUp(): void
    {
        $this->object = new TestOneTableObjectWithRelationsByMethods();
        $this->relations = $this->object->relations;
    }

    function testHasOne()
    {
        $this->assertEquals($this->object->getRelationInfo('has_one_relation'), $this->relations['has_one_relation']);
        $this->assertEquals($this->object->getRelationInfo('other_has_one_relation'), $this->relations['other_has_one_relation']);
    }

    function testHasMany()
    {
        $this->assertEquals($this->object->getRelationInfo('has_many_relation'), $this->relations['has_many_relation']);
        $this->assertEquals($this->object->getRelationInfo('other_has_many_relation'), $this->relations['other_has_many_relation']);
    }

    function testHasManyToMany()
    {
        $this->assertEquals($this->object->getRelationInfo('has_many_to_many_relation'), $this->relations['has_many_to_many_relation']);
        $this->assertEquals($this->object->getRelationInfo('other_has_many_to_many_relation'), $this->relations['other_has_many_to_many_relation']);
    }

    function testBelongsTo()
    {
        $this->assertEquals($this->object->getRelationInfo('belongs_to_relation'), $this->relations['belongs_to_relation']);
        $this->assertEquals($this->object->getRelationInfo('other_belongs_to_relation'), $this->relations['other_belongs_to_relation']);
    }

    function testManyBelongsTo()
    {
        $this->assertEquals($this->object->getRelationInfo('many_belongs_to_relation'), $this->relations['many_belongs_to_relation']);
        $this->assertEquals($this->object->getRelationInfo('other_many_belongs_to_relation'), $this->relations['other_many_belongs_to_relation']);
    }

    function testComposedOf()
    {
        $this->assertEquals($this->object->getRelationInfo('value_object'), $this->relations['value_object']);
        $this->assertEquals($this->object->getRelationInfo('other_value_object'), $this->relations['other_value_object']);
    }
}
